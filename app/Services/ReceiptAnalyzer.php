<?php

namespace App\Services;

use App\Models\ReceiptUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ReceiptAnalyzer
{
    private const PROMPT = <<<PROMPT
You are an expert Indonesian grocery receipt analyst. Extract all data accurately using the following rules:

WEIGHTED ITEMS CALCULATION:
- If quantity > 100 (like 926, 186, 134), it's GRAMS
- Unit price is PER KILOGRAM (divide by 1000 first)
- Line total = (quantity/1000) × unit_price
- Example: MELON SUPER 926 15.900 14.725 → 926g × 15.900/kg × 0.001 = 14.725

CURRENCY FORMAT:
- Indonesian format: use dots (.) as thousand separators
- Example: 176.100 means 176,100 IDR
- No decimals for IDR - whole numbers only

DISCOUNTS:
- Combine all "HEMAT" discounts into single "Hemat Produk" entry
- Sum all individual discount values

EXTRACT FROM THIS INDONESIAN GROCERY RECEIPT:

{
  "merchant": "<store name>",
  "timestamp": "<ISO 8601 date>",
  "currency": "IDR",
  "total_amount": "<total with dots>",
  "tax_amount": "<tax amount or null>",
  "items": [
    {
      "name": "<exact product name>",
      "quantity": <number>,
      "unit_price": <integer>,
      "line_total": <calculated or exact>,
      "category_hint": "groceries"
    }
  ],
  "discounts": [
    {
      "description": "Hemat Produk",
      "amount": <summed discount>
    }
  ],
  "payment_method_hint": "cash",
  "notes": null,
  "confidence": {
    "overall": 0.9,
    "fields": {
      "merchant": 0.95,
      "timestamp": 0.9,
      "total_amount": 0.95
    }
  },
  "raw_text": "<concise transcription>"
}

CRITICAL: Respond ONLY with valid JSON. No markdown, no explanations, no extra text.
PROMPT;

    public function createFromUpload(UploadedFile $file, int $userId): ReceiptUpload
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid() . '.' . strtolower($extension);
        
        // Store in public disk for URL access
        $path = $file->storeAs("receipts/{$userId}", $filename, 'public');

        return ReceiptUpload::create([
            'user_id' => $userId,
            'image_path' => $path,
            'status' => 'processing',
        ]);
    }

    public function analyze(ReceiptUpload $receipt): array
    {
        // Use public path for checking existence if needed, but storage_path works with default symlink
        // For 'public' disk, the file is in storage/app/public
        $fullPath = storage_path('app/public/' . $receipt->image_path);

        if (! file_exists($fullPath)) {
            throw new RuntimeException('Struk tidak ditemukan di penyimpanan.');
        }

        $apiKey = config('services.receipt_ai.api_key');

        if (! $apiKey) {
            throw new RuntimeException('Receipt AI API key is not configured.');
        }

        // Get public URL
        $imageUrl = asset('storage/' . $receipt->image_path);
        
        // Build payload with URL instead of base64
        $payload = $this->buildPayload($imageUrl);

        $response = Http::timeout(600)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(config('services.receipt_ai.url'), $payload);

        if (! $response->successful()) {
            throw new RuntimeException('AI service error: ' . $response->body());
        }

        $body = $response->json();
        $content = data_get($body, 'choices.0.message.content');
        
        // Save debug info to file
        $debugData = [
            'timestamp' => now()->toISOString(),
            'status_code' => $response->status(),
            'headers' => $response->headers(),
            'raw_response' => $response->body(),
            'parsed_body' => $body,
            'content_field' => $content
        ];
        file_put_contents(storage_path('logs/debug_ai_response.json'), json_encode($debugData, JSON_PRETTY_PRINT));
        
        $parsed = $this->parseContent($content);

        $receipt->forceFill([
            'ai_response' => $body,
            'parsed_payload' => $parsed,
            'status' => 'ready',
            'processed_at' => now(),
        ])->save();

        return $parsed;
    }

    public function markFailed(ReceiptUpload $receipt, string $message): void
    {
        $receipt->forceFill([
            'status' => 'failed',
            'error_message' => $message,
        ])->save();
    }

    public function imageUrl(ReceiptUpload $receipt): string
    {
        return asset('storage/' . $receipt->image_path);
    }

    private function buildPayload(string $image): array
    {
        return [
            'model' => config('services.receipt_ai.model', 'glm-4.5v'),
            'messages' => [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => self::PROMPT,
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => $image,
                        ],
                    ],
                ],
            ]],
            'stream' => false,
        ];
    }

    private function parseContent($content): array
    {
        if (is_array($content)) {
            $content = collect($content)
                ->map(function ($segment) {
                    if (is_array($segment) && isset($segment['text'])) {
                        return $segment['text'];
                    }

                    return is_string($segment) ? $segment : '';
                })
                ->implode("\n");
        }

        if (! is_string($content)) {
            throw new RuntimeException('AI response is not readable.');
        }

        // Debug: log raw content
        file_put_contents(storage_path('logs/debug_receipt.txt'), "Raw content:\n" . $content . "\n\n");
        
        // Also save content to separate file for easier debugging
        file_put_contents(storage_path('logs/debug_content_only.txt'), $content);
        
        // Specific fix for model tokens like <|begin_of_box|>
        $content = str_replace('<|begin_of_box|>', '', $content);
        $content = str_replace('<|end_of_box|>', '', $content);

        // Remove markdown formatting
        $content = str_replace('```json', '', $content);
        $content = str_replace('```', '', $content);
        
        // Robust clean: remove everything before the first { and after the last }
        $content = preg_replace('/^[^\{]*(\{)/s', '$1', $content);
        $content = preg_replace('/(\})[^\}]*$/s', '$1', $content);
        
        // Remove newlines immediately after opening brace
        $content = preg_replace('/^\{\s*\n/', '{', $content);
        
        // Remove spaces after opening brace
        $content = preg_replace('/^\{\s+/', '{', $content);
        
        $content = trim($content);

        // Fix Indonesian currency format: convert "176.100" to 176100 (integer)
        // This regex matches "key": "123.456" and replaces it with "key": 123456
        $content = preg_replace_callback('/:\s*"(\d{1,3}(?:\.\d{3})*)"/', function($matches) {
            $number = $matches[1];
            $cleanNumber = str_replace('.', '', $number); 
            return ': ' . $cleanNumber;
        }, $content);

        // Also handle unit_price and line_total if they match other patterns (just in case)
        $content = preg_replace_callback('/"unit_price":\s*"([^"]+)"/', function($matches) {
            $price = $matches[1];
            if (strpos($price, '.') !== false) {
                return '"unit_price": ' . str_replace('.', '', $price);
            }
            if (is_numeric($price)) {
                 return '"unit_price": ' . $price;
            }
            return '"unit_price": "' . $price . '"';
        }, $content);

        $content = preg_replace_callback('/"line_total":\s*"([^"]+)"/', function($matches) {
            $total = $matches[1];
            if (strpos($total, '.') !== false) {
                return '"line_total": ' . str_replace('.', '', $total);
            }
            if (is_numeric($total)) {
                 return '"line_total": ' . $total;
            }
            return '"line_total": "' . $total . '"';
        }, $content);
        
        // Handle unicode/multibyte characters  
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        
        // Debug: log cleaned content
        file_put_contents(storage_path('logs/debug_cleaned_content.txt'), $content);
        
        // Test if JSON valid
        $testDecode = json_decode($content, true);
        file_put_contents(storage_path('logs/debug_json_test.txt'), 
            "JSON Valid: " . (json_last_error() === JSON_ERROR_NONE ? 'YES' : 'NO') . 
            "\nError: " . json_last_error_msg() . 
            "\nContent length: " . strlen($content)
        );

        // Debug: Try multiple parsing methods
        $decoded = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to fix common JSON issues
            $fixedContent = $this->fixJsonContent($content);
            $decoded = json_decode($fixedContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try manual parsing from the debug content since AI response is valid but truncated
                $debugContent = file_get_contents(storage_path('logs/debug_cleaned_content.txt'));
                if ($debugContent) {
                    // Extract available data from the AI response
                    $parsed = $this->parsePartialJson($debugContent);
                    if ($parsed) {
                        file_put_contents(storage_path('logs/debug_parsed_success.txt'), "Successfully parsed partial AI response");
                        return $parsed;
                    }
                }
                
                throw new RuntimeException('AI response is not valid JSON. Raw: ' . substr($content, 0, 200));
            }
        }

        return $decoded;
    }
    
    private function fixJsonContent(string $content): string
    {
        // Common fixes for JSON content
        $fixed = $content;
        
        // Remove BOM
        $fixed = str_replace("\xEF\xBB\xBF", '', $fixed);
        $fixed = str_replace("\xFE\xFF", '', $fixed);
        $fixed = str_replace("\xFF\xFE", '', $fixed);
        
        // Remove unicode control characters
        $fixed = preg_replace('/[\x00-\x1F\x7F]/', '', $fixed);
        
        // Fix truncated JSON - complete incomplete structures
        $fixed = $this->fixTruncatedJson($fixed);
        
        // Fix escaped quotes
        $fixed = str_replace('\\\"', '"', $fixed);
        
        // Remove any leading non-JSON characters
        $fixed = preg_replace('/^[^{]*(\{)/', '$1', $fixed);
        
        // Try to validate with manual check
        $test = json_decode($fixed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            file_put_contents(storage_path('logs/debug_fixed_content.txt'), "FIXED SUCCESSFULLY");
            return $fixed;
        }
        
        file_put_contents(storage_path('logs/debug_fixed_content.txt'), "FIX FAILED: " . json_last_error_msg());
        return $content;
    }
    
    private function fixTruncatedJson(string $json): string
    {
        // Count brackets to fix incomplete JSON
        $openBraces = substr_count($json, '{');
        $closeBraces = substr_count($json, '}');
        $openBrackets = substr_count($json, '[');
        $closeBrackets = substr_count($json, ']');
        
        // Fix missing closing braces
        while ($openBraces > $closeBraces) {
            $json .= '}';
            $closeBraces++;
        }
        
        // Fix missing closing brackets
        while ($openBrackets > $closeBrackets) {
            $json .= ']';
            $closeBrackets++;
        }
        
        // Fix incomplete strings - look for unclosed quotes
        $quoteCount = substr_count($json, '"');
        if ($quoteCount % 2 !== 0) {
            // Add missing closing quote
            $lastQuote = strrpos($json, '"');
            if ($lastQuote !== false) {
                $json = substr($json, 0, strlen($json)) . '"';
            }
        }
        
        return $json;
    }
    
    private function parsePartialJson(string $content): ?array
    {
        // Extract data from partial JSON using regex
        $data = [];
        
        // Extract merchant
        if (preg_match('/"merchant"\s*:\s*"([^"]+)"/', $content, $matches)) {
            $data['merchant'] = $matches[1];
        }
        
        // Extract timestamp
        if (preg_match('/"timestamp"\s*:\s*"([^"]+)"/', $content, $matches)) {
            $data['timestamp'] = $matches[1];
        }
        
        // Extract currency
        if (preg_match('/"currency"\s*:\s*"([^"]+)"/', $content, $matches)) {
            $data['currency'] = $matches[1];
        }
        
        // Extract total_amount
        if (preg_match('/"total_amount"\s*:\s*([\d.]+)/', $content, $matches)) {
            $total = $matches[1];
            // Convert Indonesian format: 176.100 -> 176100 (no decimal handling for IDR)
            if (strpos($total, '.') !== false) {
                // Remove dots - Indonesian format uses dots for thousands
                $total = str_replace('.', '', $total);
            }
            $data['total_amount'] = (float)$total;
        }
        
        // Extract tax_amount
        if (preg_match('/"tax_amount"\s*:\s*([\d.]+)/', $content, $matches)) {
            $data['tax_amount'] = (float)$matches[1];
        } else {
            $data['tax_amount'] = null;
        }
        
        // Extract items (simplified)
        $data['items'] = [];
        if (preg_match_all('/"name"\s*:\s*"([^"]+)"[^}]*"quantity"\s*:\s*(\d+)[^}]*"unit_price"\s*:\s*([\d.]+)/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $item) {
                // Convert Indonesian currency format (no decimal for IDR)
                $unitPrice = $item[3];
                if (strpos($unitPrice, '.') !== false) {
                    $unitPrice = str_replace('.', '', $unitPrice);
                }
                
                // Extract line_total directly from JSON if available, otherwise use basic calculation
                $lineTotal = (float)$unitPrice; // default fallback
                
                // Look for line_total in the item data
                $fullItemText = $item[0];
                if (preg_match('/"line_total"\s*:\s*([\d.]+)/', $fullItemText, $totalMatch)) {
                    $totalValue = $totalMatch[1];
                    if (strpos($totalValue, '.') !== false) {
                        $totalValue = str_replace('.', '', $totalValue);
                    }
                    $lineTotal = (float)$totalValue;
                }
                
                $data['items'][] = [
                    'name' => $item[1],
                    'quantity' => (int)$item[2],
                    'unit_price' => (float)$unitPrice,
                    'line_total' => $lineTotal,
                    'category_hint' => 'food' // default
                ];
            }
        }
        
        // Extract discounts with Indonesian format - SUM into single "Hemat Produk"
        $totalDiscount = 0;
        $data['discounts'] = [];
        
        if (preg_match_all('/"description"\s*:\s*"([^"]+)"[^}]*"amount"\s*:\s*(-[\d.]+)/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $discount) {
                $amount = $discount[2];
                if (strpos($amount, '.') !== false) {
                    $amount = str_replace('.', '', $amount);
                }
                $totalDiscount += (float)$amount;
            }
            
            // Add single combined discount entry
            if ($totalDiscount < 0) {
                $data['discounts'][] = [
                    'description' => 'Hemat Produk',
                    'amount' => $totalDiscount
                ];
            }
        }
        
        // Set default values
        $data['payment_method_hint'] = null;
        $data['notes'] = 'Parsed from partial AI response';
        $data['confidence'] = [
            'overall' => 0.8,
            'fields' => ['merchant' => 0.9, 'timestamp' => 0.7, 'total_amount' => 0.8]
        ];
        $data['raw_text'] = 'Extracted from partial response';
        
        return !empty($data['merchant']) ? $data : null;
    }
}

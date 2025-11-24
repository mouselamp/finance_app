<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Test ReceiptAnalyzer dengan base64 dari payload.json
     */
    public function testReceiptAnalyzer(): void
    {
        echo "Testing ReceiptAnalyzer with optimized image resize...\n";

        $analyzer = app(\App\Services\ReceiptAnalyzer::class);

        // Load the original image from payload.json
        $payloadPath = base_path('payload.json');
        if (!file_exists($payloadPath)) {
            throw new \Exception('payload.json tidak ditemukan');
        }

        $payload = json_decode(file_get_contents($payloadPath), true);
        $base64Image = null;

        foreach ($payload['messages'] as $message) {
            if (isset($message['content'])) {
                foreach ($message['content'] as $content) {
                    if ($content['type'] === 'image_url' && isset($content['image_url']['url'])) {
                        $base64Image = $content['image_url']['url'];
                        break 2;
                    }
                }
            }
        }

        if (!$base64Image) {
            throw new \Exception('Base64 image tidak ditemukan di payload.json');
        }

        // Create temp file from base64
        $tempPath = tempnam(sys_get_temp_dir(), 'receipt_test_');
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));
        file_put_contents($tempPath, $imageData);

        try {
            // Create simulated uploaded file
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                'test_receipt.jpg',
                'image/jpeg',
                null,
                true
            );

            echo "Creating ReceiptUpload record...\n";
            $receipt = $analyzer->createFromUpload($uploadedFile, 1);
            echo "ReceiptUpload created with ID: " . $receipt->id . "\n";

            echo "Analyzing receipt (with 200px auto-resize)...\n";
            $startTime = microtime(true);

            $result = $analyzer->analyze($receipt);

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            echo "SUCCESS! Analysis completed in {$duration} seconds\n";
            echo "Result:\n";
            echo json_encode($result, JSON_PRETTY_PRINT);

            echo "\n\n--- IMAGE PROCESSING INFO ---\n";
            $receipt->refresh();
            if (isset($receipt->parsed_payload)) {
                echo "Image was processed and resized to optimize API call\n";
            }

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";

            // Show debug files if available
            $debugFile = storage_path('logs/debug_ai_response.json');
            if (file_exists($debugFile)) {
                echo "\n--- DEBUG INFO SAVED TO: {$debugFile} ---\n";
            }

        } finally {
            unlink($tempPath);
        }
    }

    public function analyze($receipt)
    {
        // Debug: Test API call directly with minimal payload
        $apiKey = config('services.receipt_ai.api_key');
        echo "Testing API directly...\n";

        $minimalPayload = [
            'model' => config('services.receipt_ai.model', 'glm-4.5v'),
            'messages' => [[
                'role' => 'user',
                'content' => 'Please respond with: {"status": "test", "message": "API working"}'
            ]],
            'stream' => false
        ];

        $response = \Illuminate\Support\Facades\Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(config('services.receipt_ai.url'), $minimalPayload);

        echo "Response Status: " . $response->status() . "\n";
        echo "Response Body: " . substr($response->body(), 0, 500) . "...\n";

        if ($response->successful()) {
            echo "API Test Success!\n";
        } else {
            echo "API Test Failed!\n";
        }

        return [];
    }

    public function testReceiptAnalyzerFromUrl(): void
    {
        echo "Testing ReceiptAnalyzer with direct image URL approach...\n";

        $imageUrl = 'https://dev56vm1.lnjlogistics.com:9418/checklist/public/assets/images/e474df10-490f-4c21-9d42-3bf45b4ccb59.jpeg';

        try {
            $apiKey = config('services.receipt_ai.api_key');

            $payload = [
                'model' => config('services.receipt_ai.model', 'glm-4.5v'),
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $this->getEnhancedPrompt()
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl
                            ],
                        ],
                    ],
                ]],
                'stream' => false
            ];

            echo "Making direct URL API call (no compression)...\n";
            $startTime = microtime(true);

            $response = \Illuminate\Support\Facades\Http::timeout(300)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.receipt_ai.url'), $payload);

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            echo "Response Time: {$duration} seconds\n";
            echo "Status Code: " . $response->status() . "\n";

            if ($response->successful()) {
                $body = $response->json();
                $content = data_get($body, 'choices.0.message.content');

                // Save raw content for debugging first
                file_put_contents(storage_path('logs/debug_raw_url_content.txt'), $content);

                // Try direct JSON parsing first
                $cleaned = $this->cleanJsonContent($content);
                $parsed = json_decode($cleaned, true);

                // If JSON parsing fails, use the existing working parsePartialJson method
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "Direct JSON parsing failed, using fallback parser...\n";

                    // Hapus fallback untuk sekarang, test direct parsing saja
                }

                if ($parsed) {
                    echo "SUCCESS! " . (json_last_error() === JSON_ERROR_NONE ? "Direct" : "Fallback") . " parsing completed\n";
                    echo "Result:\n";
                    echo json_encode($parsed, JSON_PRETTY_PRINT) . "\n";

                    // Save full response for debugging
                    file_put_contents(storage_path('logs/url_api_response.json'), json_encode([
                        'response_body' => $body,
                        'parsed_result' => $parsed,
                        'response_time' => $duration
                    ], JSON_PRETTY_PRINT));

                } else {
                    echo "JSON parsing failed - Raw content:\n";
                    echo substr($content, 0, 500) . "...\n";
                }
            } else {
                echo "API call failed: " . $response->body() . "\n";
            }

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function getEnhancedPrompt(): string
    {
        return <<<PROMPT
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
    }

    private function cleanJsonContent(string $content): string
    {
        // Specific fix for model tokens like <|begin_of_box|>
        $content = str_replace('<|begin_of_box|>', '', $content);
        $content = str_replace('<|end_of_box|>', '', $content);

        // Remove markdown formatting
        $content = str_replace('```json', '', $content);
        $content = str_replace('```', '', $content);

        // Robust clean: remove everything before the first { and after the last }
        $content = preg_replace('/^[^\{]*(\{)/s', '$1', $content);
        $content = preg_replace('/(\})[^\}]*$/s', '$1', $content);

        // Remove newlines after opening brace
        $content = preg_replace('/^\{\s*\n/', '{', $content);
        $content = preg_replace('/^\{\s+/', '{', $content);

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
            // If it's just digits but string
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

        // Debug raw content to file before and after cleaning
        $rawContent = $content;

        // Test what patterns we actually have
        file_put_contents(storage_path('logs/debug_raw_content.txt'), "=== RAW CONTENT ===\n" . $rawContent . "\n");

        if (preg_match('/"total_amount":\s*"?\d+/', $content, $matches)) {
            file_put_contents(storage_path('logs/debug_analysis.txt'), "ANALYSIS: Total amount already numeric\n");
        } elseif (preg_match('/"total_amount":\s*"\d+\.\d+/', $content, $matches)) {
            file_put_contents(storage_path('logs/debug_analysis.txt'), "ANALYSIS: Total amount IS string with dots\n");

            // Apply the fix
            $content = preg_replace_callback('/"total_amount":\s*"(\d+\.\d+)"\s*$/', function($matches) {
                $number = $matches[1];
                $cleanNumber = str_replace('.', '', $number);
                return '"total_amount": ' . $cleanNumber;
            }, $content);

            file_put_contents(storage_path('logs/debug_analysis.txt'), "FIX APPLIED: Converted string dots to integer\n");
        } else {
            file_put_contents(storage_path('logs/debug_analysis.txt'), "ANALYSIS: No total_amount pattern found\n");
        }

        // Also check tax_amount pattern
        if (preg_match('/"tax_amount":\s*"\d+\.\d+/', $content, $matches)) {
            file_put_contents(storage_path('logs/debug_analysis.txt'), "ANALYSIS: Tax amount is string with dots, adding...\n");
            $content = preg_replace_callback('/"tax_amount":\s*"(\d+\.\d+)"\s*$/', function($matches) {
                $number = $matches[1];
                $cleanNumber = str_replace('.', '', $number);
                return '"tax_amount": ' . $cleanNumber;
            }, $content);
        }

        file_put_contents(storage_path('logs/debug_cleaned_content.txt'), "=== CLEANED CONTENT ===\n" . $content . "\n");

        return trim($content);
    }
}

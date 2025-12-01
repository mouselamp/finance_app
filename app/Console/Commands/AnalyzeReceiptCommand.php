<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class AnalyzeReceiptCommand extends Command
{
    protected $signature = 'mcp:analyze-receipt {image}';
    protected $description = 'Analyze receipt image using Claude MCP & ZAI MCP server (output JSON only)';

    public function handle()
    {
        $image = $this->argument('image');

        if (!file_exists($image)) {
            $this->error("File not found: $image");
            return 1;
        }

        $prompt = <<<EOT
Extract all information from this receipt and format it as JSON with the following structure:
{
  "merchant": "<store name>",
  "timestamp": "<ISO 8601 date>",
  "currency": "IDR",
  "total_amount": "<decimal(8,2)>",
  "tax_amount": "<tax amount or null>",
  "items": [
    {
      "name": "<exact product name>",
      "quantity": <number>,
      "unit_price": <decimal(8,2)>,
      "line_total": <decimal(8,2)>,
      "category_hint": "groceries"
    }
  ],
  "discounts": [
    {
      "description": "<discount description>",
      "amount": <summed discount>
    }
  ],
  "payment_method_hint": "cash",
  "notes": null,
  "raw_text": "<concise transcription>"
}
EOT;

        $cmd = [
            'sh', '-c',
            'claude mcp call zai-mcp-server analyze_image ' .
            '--image ' . escapeshellarg($image) . ' ' .
            '--prompt ' . escapeshellarg($prompt)
        ];

        $process = new Process($cmd);
        $process->setTimeout(500); // 3 menit
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("MCP process failed: " . $process->getErrorOutput());
            return 1;
        }

        $output = trim($process->getOutput());

        // Tampilkan langsung output JSON
        $this->line($output);

        return 0;
    }
}

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Log activity with simple format
     */
    public static function log($action, $message, $context = [])
    {
        $data = [
            'user_id' => auth()->id(),
            'action' => $action,
            'message' => $message,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'ip' => request()->ip(),
        ];

        Log::info($action . ': ' . $message, array_merge($data, $context));
    }

    /**
     * Log transaction
     */
    public static function transaction($action, $transaction)
    {
        self::log('TRANSACTION', $action, [
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'account' => $transaction->account->name ?? null,
        ]);
    }

    /**
     * Log error
     */
    public static function error($message, $context = [])
    {
        Log::error('ERROR: ' . $message, array_merge([
            'user_id' => auth()->id(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ], $context));
    }
}
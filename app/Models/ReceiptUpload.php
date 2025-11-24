<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'ai_response',
        'parsed_payload',
        'status',
        'error_message',
        'processed_at',
        'saved_at',
    ];

    protected $casts = [
        'ai_response' => 'array',
        'parsed_payload' => 'array',
        'processed_at' => 'datetime',
        'saved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

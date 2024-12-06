<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'response'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

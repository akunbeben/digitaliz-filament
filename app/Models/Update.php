<?php

namespace App\Models;

use App\Support\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'status',
        'notes',
        'message',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

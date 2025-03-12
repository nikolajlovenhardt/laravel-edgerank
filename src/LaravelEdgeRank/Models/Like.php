<?php

namespace LaravelEdgeRank\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Like extends Model
{
    protected $fillable = [
        'reaction',
        'post_id',
        'user_id',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(config('edgerank.models.main', Post::class));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('edgerank.models.user', User::class));
    }
}
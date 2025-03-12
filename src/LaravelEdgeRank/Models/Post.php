<?php

namespace LaravelEdgeRank\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Post extends Model
{
    protected $fillable = [
        'title',
        'post',
        'parent_id',
        'user_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(config('edgerank.models.comment', Comment::class));
    }

    public function likes(): HasMany
    {
        return $this->hasMany(config('edgerank.models.like', Like::class));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('edgerank.models.user', User::class));
    }
}

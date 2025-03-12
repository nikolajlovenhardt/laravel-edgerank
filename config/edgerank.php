<?php

use LaravelEdgeRank\Models\Comment;
use LaravelEdgeRank\Models\Like;
use LaravelEdgeRank\Models\Post;
use App\Models\User;

return [
    /**
     * Default models
     */
    'models' => [
        'main' => Post::class,
        'user' => User::class,

        'like' => Like::class,
        'comment' => Comment::class,
    ],

    /**
     * Weights
     */
    'weights' => [
        'likes' => 1,
        'comments' => 3,
        'shares' => 5,
        'time_decay' => 1.95,
    ],
];

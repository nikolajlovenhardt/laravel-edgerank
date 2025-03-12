# Laravel: Facebook EdgeRank Algorithm for Laravel

This package is a simple and flexible implementation of the Facebook EdgeRank algorithm for Laravel.
Ideal for newsfeeds like Twitter, Facebook, LinkedIn, etc. Use the EdgeRank algorithm to sort posts and similar content based on factors such as likes, comments, shares, and time. The configuration makes it easy to manage the weighting of different parameters.

## Installation and setup

### Installation with Composer

```
composer require nikolajlovenhardt/laravel-edgerank
```

### Publish resources

Config
```
php artisan vendor:publish --tag="edgerank-config"
```

Migrations
```
php artisan vendor:publish --tag="edgerank-migrations"
```

## Use-cases and demos

### Sort posts using default weights

```php
use LaravelEdgeRank\Builders\EdgeRankBuilder;

$posts = EdgeRankBuilder::make()
    ->get();
```

### Limits and offsets

#### Simple pagination

Paginate with a simple limit and offset

```php
use LaravelEdgeRank\Builders\EdgeRankBuilder;

$posts = EdgeRankBuilder::make()
    ->limit(50)
    ->offset(100)
    ->get();
```

#### Cursor

Do you need to paginate using ids? No worries, we got your back

```php
use LaravelEdgeRank\Builders\EdgeRankBuilder;

$posts = EdgeRankBuilder::make()
    ->after(100, 'id') // Get older posts than #100
    ->get();
```

### Custom query

Use the `$builder->query()` method to access the query builder before fetching the results. This allows you to add custom query code still utilizing the EdgeRank-sort.

```php
use LaravelEdgeRank\Builders\EdgeRankBuilder;
use App\Models\Feed\Item;

$friendIds = [1, 2, 3, 4];

$posts = EdgeRankBuilder::make(Item::class)
    ->query()
    ->where(function ($query) use ($friendIds) {
        $query->whereIn('user_id', $friendIds)
        
        $query->orWhereHas('user', function () {
            $query->where('is_public', '=', true)
        });
    })
    ->get();
```

### Custom model / Override config

Use a custom model without configuration using the `EdgeRankBuilder::make()` method, and add custom config using `weights()`and `options()`-methods.

```php
use LaravelEdgeRank\Builders\EdgeRankBuilder;
use App\Models\Feed\Item;

$posts = EdgeRankBuilder::make(Item::class)
    ->weights([
        'likes' => 10,
        'hates' => 2,
        'customRelation' => 1.5, // Item->customRelation(): HasMany
    ])
    ->get();
```

## Contributing

Thank you for considering contributing to Laravel EdgeRank!

## License

Laravel EdgeRank is open-sourced software licensed under the [MIT license](LICENSE.md).
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class PolymorphicMappingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'User' => 'App\Models\User',
            'Gift' => 'App\Models\Gift',
            'GiftRating' => 'App\Models\GiftRating',
            'GiftLike' => 'App\Models\GiftLike',
            'Order' => 'App\Models\Order',
        ]);
    }
}

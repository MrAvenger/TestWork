<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Product;
use App\Models\PartTwo\ProductType;
use App\Models\PartTwo\Warehouse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('part1.orders.create', function($view) {
            $view->with('users', User::all());
            $view->with('products', Product::all());
        });
        view()->composer('part1.orders.edit', function ($view) {
            $view->with('users', User::all());
            $view->with('products', Product::all());
        });
        view()->composer('part2.products.create', function ($view) {
            $view->with('types', ProductType::all());
        });
        view()->composer('part2.products.edit', function ($view) {
            $view->with('types', ProductType::all());
        });
        view()->composer('part2.managing.index', function ($view) {
            $view->with('warehouses', Warehouse::all());
        });
        view()->composer('part2.managing.create', function ($view) {
            $view->with('warehouses', Warehouse::all());
            $view->with('products', \App\Models\PartTwo\Product::all());
        });
        view()->composer('part2.managing.edit', function ($view) {
            $view->with('products', \App\Models\PartTwo\Product::all());
        });
        view()->composer('part2.transfer.create', function ($view) {
            $view->with('warehouses', Warehouse::all());
        });
    }
}

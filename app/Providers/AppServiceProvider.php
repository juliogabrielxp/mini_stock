<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\ProductList;
use App\Livewire\ProductManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('product-list', ProductList::class);
        Livewire::component('product-manager', ProductManager::class);
    }
}

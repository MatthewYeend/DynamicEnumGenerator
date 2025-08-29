<?php

namespace MattYeend\DynamicEnumGenerator;

use Illuminate\Support\ServiceProvider;
use MattYeend\DynamicEnumGenerator\Console\GenerateEnums;

class DynamicEnumServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            GenerateEnums::class,
        ]);
    }

    public function boot()
    {
        //
    }
}

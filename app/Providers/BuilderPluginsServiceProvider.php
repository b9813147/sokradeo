<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class BuilderPluginsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // for  Eloquent\Builder
        \Illuminate\Database\Eloquent\Builder::macro('toSqlBinding', function () {
            $bindings = collect($this->getBindings())->map(function ($item) {
                if (!is_numeric($item)) {
                    return "'" . $item . "'";
                }
                return $item;
            })->toArray();
            return Str::replaceArray('?', $bindings, $this->toSql());
        });
        // for  Query\Builder
        \Illuminate\Database\Query\Builder::macro('toSqlBinding', function () {
            $bindings = collect($this->getBindings())->map(function ($item) {
                if (!is_numeric($item)) {
                    return "'" . $item . "'";
                }
                return $item;
            })->toArray();

            return Str::replaceArray('?', $bindings, $this->toSql());
        });
    }
}

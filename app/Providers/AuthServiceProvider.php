<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'                      => 'App\Policies\ModelPolicy',
        'App\Models\ExhibitionCmsSet'    => 'App\Policies\ExhibitionCmsSetPolicy',
        'App\Models\Group'               => 'App\Policies\GroupPolicy',
        'App\Models\GroupChannel'        => 'App\Policies\GroupChannelPolicy',
        'App\Models\GroupChannelContent' => 'App\Policies\GroupChannelContentPolicy',
        'App\Models\Tba'                 => 'App\Policies\TbaPolicy',
        'App\Models\TbaAnalysisEvent'    => 'App\Policies\TbaAnalysisEventPolicy',
        'App\Models\TbaEvaluateEvent'    => 'App\Policies\TbaEvaluateEventPolicy',
        'App\Models\Video'               => 'App\Policies\VideoPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Auth::extend('client', function ($app, $name, array $config) {
            return new \App\Services\Auth\ClientGuard(
                    Auth::createUserProvider($config['provider']),
                    $app->make('request'),
                    new \App\Repositories\ClientRepository(),
                    app(UserRepository::class)
            );
        });
    }
}

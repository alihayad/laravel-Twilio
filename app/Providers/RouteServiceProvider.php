<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for ('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for ('login', function (Request $request) {
            $limit = new Limit($request->ip(), 4, 20);

            $response = $limit->response(function (Request $request, array $headers) {

                return redirect()->back()->withErrors(['throttle' => "Toomany attempts please try in " . $headers['Retry-After'] . " seconds"]);
            }
            ); 

            return $response;
        });

        RateLimiter::for ('sms', function (Request $request) {
            $limit = new Limit($request->ip(), 2, 60);

            $response = $limit->response(function (Request $request, array $headers) {

                return redirect()->back()->withErrors(['throttle' => "Toomany attempts please try in " . $headers['Retry-After'] . " seconds"]);
            }
            );

            return $response;
        });

        RateLimiter::for ('verify', function (Request $request) {
            $limit = new Limit($request->ip(), 2, 60);

            $response = $limit->response(function (Request $request, array $headers) {

                return redirect()->back()->withErrors(['throttle' => "Toomany attempts please try in " . $headers['Retry-After'] . " seconds"]);
            }
            );

            return $response;
        });
    }
}
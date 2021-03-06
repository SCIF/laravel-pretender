<?php declare(strict_types=1);

namespace Scif\LaravelPretend;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\ServiceProvider;
use Scif\LaravelPretend\Service\Impersonator;

class LaravelPretendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/pretend.php';

        if ( ! $this->isLumen()) {
            $this->publishes([$configPath => config_path('pretend.php')], 'config');
        }

        $this->mergeConfigFrom($configPath, 'pretend');

        if (!$this->app->bound('impersonator')) {
            $this->app->bind('impersonator', Impersonator::class);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(Impersonator::class)
          ->needs(UserProvider::class)
          ->give(function (): UserProvider {
              return \Auth::getProvider();
          });

        $this->app->singleton(Impersonator::class, Impersonator::class);
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen(): bool
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }
}

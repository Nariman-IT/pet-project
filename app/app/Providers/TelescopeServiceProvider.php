<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    

    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            // ВРЕМЕННО для теста - разрешаем всем
            return true;
            
            // ПОТОМ замените на реальную проверку:
            // return in_array($user->email, [
            //     'your-email@example.com',
            // ]);
        });
    }



    protected function authorization()
    {
        Telescope::auth(function ($request) {
            // Для разработки - разрешаем доступ в local окружении
            if (app()->environment('local')) {
                return true;
            }
            
            // Для production - проверяем авторизацию
            $user = auth('sanctum')->user() ?? auth('api')->user();
            
            return $user && in_array($user->email, [
                'admin@example.com',
                'your-email@example.com',
            ]);
        });
    }
}

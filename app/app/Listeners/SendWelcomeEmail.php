<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
/**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        
        Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Добро пожаловать в наше приложение!');
        });
    }
}

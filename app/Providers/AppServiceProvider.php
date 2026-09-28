<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('[VUA TABLET] Xác thực địa chỉ email của bạn')
                ->greeting('Xin chào ' . $notifiable->name . '!')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại VUA TABLET.')
                ->line('Vui lòng bấm vào nút bên dưới để xác thực địa chỉ email và hoàn tất kích hoạt tài khoản của bạn:')
                ->action('Xác thực tài khoản ngay', $url)
                ->line('Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.')
                ->salutation('Trân trọng, Đội ngũ VUA TABLET');
        });
    }
}

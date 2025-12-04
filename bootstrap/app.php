<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckBanned; // 1. Import Middleware CheckBanned

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 2. Đăng ký alias cho Admin (bạn đã làm cái này rồi)
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        // 3. QUAN TRỌNG: Thêm CheckBanned vào nhóm 'web'
        // Để nó chạy ở MỌI trang web, kiểm tra xem user có bị ban không
        $middleware->web(append: [
            CheckBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
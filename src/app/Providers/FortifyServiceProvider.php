<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\LoginRequest as AppLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FortifyLoginRequest::class,
            AppLoginRequest::class
        );

        $this->app->singleton(
            CreatesNewUsers::class,
            CreateNewUser::class
        );
    }

    public function boot(): void
    {
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
        
        Fortify::registerView(function () {
            if (Auth::check() && Auth::user()->role === 'admin') {
                throw new NotFoundHttpException();
            }
            return view('auth.register');
        });

        Fortify::loginView(function (Request $request) {
            return $request->is('admin/login')
                ? view('auth.login')
                : view('auth.user-login');
        });

        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            if ($request->login_type === 'admin' && $user->role !== 'admin') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'login_error' => '一般ユーザーとしてログインしてください',
                ]);
            }

            if ($request->login_type === 'user' && $user->role === 'admin') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'login_error' => '管理者としてログインしてください',
                ]);
            }

            return $user;
        });

        Fortify::redirects('login', function (Request $request) {
            return $request->login_type === 'admin'
                ? route('admin.attendance.list')
                : '/attendance';
        });
    }
}
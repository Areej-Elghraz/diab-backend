<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\GenerateTokensService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    public function __invoke(LoginRequest $request, GenerateTokensService $generateTokensService)
    {
        return $this->runWithTransaction(function () use ($request, $generateTokensService) {
            $validated = $request->validated();
            $inputType = filter_var($validated['input'], FILTER_VALIDATE_EMAIL)
                ? 'email'
                : 'username';
            $user = User::where($inputType, $validated['input'])->first();

            if (!$user) {
                throw ValidationException::withMessages(
                    ['input' => __('validation.invalid_value', ['attribute' => __('validation.attributes.' . $inputType)])]
                );
            }

            if (!Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages(
                    ['password' => __('validation.invalid_value', ['attribute' => __('validation.attributes.password')])]
                );
            }

            if ($inputType && !$user->email_verified_at) {
                throw new \Exception(__('auth.account_not_verified'), 403);
            }

            $tokens = $generateTokensService($user, $request->header('User-Agent'), (bool) $request->remember ?? false);

            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();

            return [
                'user' => $user->refresh(),
                'access_token' => $tokens['access_token'],
                'access_token_expires_in' => $tokens['access_token_expires_in'],
                'remember_token' => $tokens['remember_token'],
                'remember_token_expires_in' => $tokens['remember_token_expires_in'],
            ];
        },  successMessage: __('messages.login_success'));
    }
}

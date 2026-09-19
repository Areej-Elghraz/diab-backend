<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerifyOtpController extends ApiController
{
    public function __invoke(VerifyOtpRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {

            $validated = $request->validated();
            $inputType = filter_var($validated['input'], FILTER_VALIDATE_EMAIL)
                ? 'email'
                : 'username';

            $user       = User::where($inputType, $validated['input'])?->first();
            $expiration = config('auth.passwords.users.expire', 10); //minutes
            $otpRecord  = DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->first();

            if (!$user) {
                throw ValidationException::withMessages(['input' => __('validation.invalid_value', ['attribute' => __('validation.attributes.' . $inputType)])]);
            }

            if (!$otpRecord) {
                throw ValidationException::withMessages(['otp' => __('validation.invalid_value', ['attribute' => __('validation.attributes.otp')])]);
            }

            if ($otpRecord) {
                $createdAt    = \Carbon\Carbon::parse($otpRecord->created_at);
                $minutesSince = $createdAt->diffInMinutes(now());
                if ($minutesSince >= $expiration) {
                    throw ValidationException::withMessages(['otp' => __('messages.otp_expired')]);
                }
                if (!Hash::check($validated['otp'], $otpRecord->token)) {
                    throw ValidationException::withMessages(['otp' => __('validation.invalid_value', ['attribute' => __('validation.attributes.otp')])]);
                }
            }

            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update([
                    'verified' => true,
                ]);

            return [];
        },  __('messages.otp_verified'));
    }
}

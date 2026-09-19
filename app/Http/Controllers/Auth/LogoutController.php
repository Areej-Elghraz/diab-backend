<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\LogoutRequest;

class LogoutController extends ApiController
{
    public function __invoke(LogoutRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {

            $validated = $request->validated();

            /** @var \App\Models\User $user */
            $user = auth('sanctum')->user();

            if (!empty($validated['all_devices']) and $validated['all_devices'] == 1) { ///
                $user->tokens()->delete();
            } else {
                $user->tokens()?->where('name', $user->currentAccessToken()?->name)->delete();
            }
            return [];
        },  __('messages.logout_success'));
    }
}

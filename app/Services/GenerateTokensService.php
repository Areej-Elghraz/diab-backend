<?php

namespace App\Services;

use App\Enums\TokenAbilityEnum;
use App\Models\User;

class GenerateTokensService
{
  public function __invoke(User $user, string $deviceName, bool $remember = true)
  {
    /**
     * @var \App\Models\User $user
     */
    $user->tokens()?->where('name', $user->currentAccessToken()?->name)
      ->orWhere('name', 'access_token_' . $deviceName)
      ->orWhere('name', 'remember_token_' . $deviceName)
      ->delete();
    $user->tokens()->where('expires_at', '<', now())->delete();

    // $accessToken = $user->createToken('access_token_' . $deviceName, [TokenAbilityEnum::access_token->value], now()->addHours(config('sanctum.access_token_expiration')))->plainTextToken;
    $accessToken = $user->createToken('access_token_' . $deviceName, [TokenAbilityEnum::access_token->value], now()->addSeconds(config('sanctum.access_token_expiration', 7200)))->plainTextToken;
    if ($remember) {
      // $rememberToken = $user->createToken('remember_token_' . $deviceName, [TokenAbilityEnum::remember_token->value], now()->addDays(config('sanctum.remember_token_expiration')))->plainTextToken;
      $rememberToken = $user->createToken('remember_token_' . $deviceName, [TokenAbilityEnum::remember_token->value], now()->addSeconds(config('sanctum.remember_token_expiration', 2592000)))->plainTextToken;
    }
    return [
      'access_token'              => $accessToken,
      'access_token_expires_in'   => config('sanctum.access_token_expiration'),
      'remember_token'            => $rememberToken ?? null,
      'remember_token_expires_in' => config('sanctum.remember_token_expiration'),
    ];
  }
}

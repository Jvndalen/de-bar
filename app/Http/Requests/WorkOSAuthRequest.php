<?php

namespace App\Http\Requests;

use App\Models\User as AppUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\User;
use Laravel\WorkOS\WorkOS;
use WorkOS\Exception\WorkOSException;
use WorkOS\UserManagement;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;

class WorkOSAuthRequest extends AuthKitAuthenticationRequest
{
    /**
     * @throws WorkOSException
     */
    public function authenticate(?callable $findUsing = null, ?callable $createUsing = null, ?callable $updateUsing = null): mixed
    {
        WorkOS::configure();

        $this->ensureStateIsValid();

        $findUsing ??= $this->findUsing(...);
        $createUsing ??= $this->createUsing(...);
        $updateUsing ??= $this->updateUsing(...);

        $response = (new UserManagement)->authenticateWithCode(
            config('services.workos.client_id'),
            $this->query('code'),
        );

        [$workosUser, $accessToken, $refreshToken] = [
            $response->user,
            $response->access_token,
            $response->refresh_token,
        ];

        $user = new User(
            id: $workosUser->id,
            firstName: $workosUser->firstName,
            lastName: $workosUser->lastName,
            email: $workosUser->email,
            avatar: $workosUser->profilePictureUrl,
        );

        $existingUser = $findUsing($user->id);

        if (! $existingUser && $user->email) {
            $existingUser = AppUser::where('email', $user->email)->first();

            if ($existingUser) {
                $existingUser->update([
                    'workos_id' => $user->id,
                    'avatar' => $user->avatar ?? $existingUser->avatar,
                ]);
            }
        }

        if (! $existingUser) {
            $existingUser = $createUsing($user);
            event(new Registered($existingUser));
        } elseif (! is_null($updateUsing)) {
            $existingUser = $updateUsing($existingUser, $user);
        }

        Auth::guard('web')->login($existingUser);

        $this->session()->put('workos_access_token', $accessToken);
        $this->session()->put('workos_refresh_token', $refreshToken);
        $this->session()->regenerate();

        return $existingUser;
    }
}


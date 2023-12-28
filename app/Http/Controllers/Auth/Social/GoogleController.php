<?php

namespace App\Http\Controllers\Auth\Social;


use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

/**
 * GoogleController
 */
class GoogleController extends Controller
{

    public $userRepository;
    public $authRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        AuthRepositoryInterface $authRepository
    ) {
        $this->userRepository = $userRepository;
        $this->authRepository = $authRepository;
    }

    /**
     * redirectToGoogle
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * handleGoogleCallback
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->stateless()->user();

            $find_user = $this->userRepository->getBySocialAuth('google', $user->id);


            if ($find_user) {

                Auth::login($find_user);

                return redirect('/home');
            } else {
                $data = [
                    'first_name' => $user->user['given_name'],
                    'last_name' => $user->user['family_name'],
                    'email' => $user->email,
                    'google_id' => $user->id,
                ];

                $new_user = $this->authRepository->register($data, true);

                Auth::login($new_user);

                return redirect('/home');
            }
        } catch (Exception $ex) {
        }
    }
}

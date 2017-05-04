<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\User;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\Response;
use Illuminate\Contracts\Hashing\Hasher;
use JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 * @Resource("Auth", uri="/auth")
 */
class AuthController extends Controller
{
    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * AuthController constructor.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Auth
     *
     * Auth users by their username and pin. The pin is encrypted before storage.
     *
     * @param \App\Http\Requests\AuthRequest $request
     *
     * @return \Dingo\Api\Http\Response
     *
     * @Post("/")
     */
    public function login(AuthRequest $request)
    {
        try {
            $user = User::whereUsername($request->username)->firstOrFail();
            if ($this->hasher->check($request->pin, $user->pin)) {
                $token = JWTAuth::fromUser($user);

                return new Response([
                    'token' => $token,
                ]);
            }
            throw new \Exception();
        } catch (JWTException $exception) {
            throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage(), $exception);
        } catch (\Exception $exception) {
            throw new UnprocessableEntityHttpException('Wrong Pin', $exception);
        }
    }
}

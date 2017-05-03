<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    public function auth(AuthRequest $request)
    {
        try {
            $token = JWTAuth::attempt($request->all());
        } catch (JWTException $exception) {
            throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage(), $exception);
        } catch (\Exception $exception) {
            throw new UnprocessableEntityHttpException('Wrong Pin', $exception);
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Lang;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

class JWTChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First, get the token
        $token = $request->bearerToken();

        if( is_null($token) || empty($token) )
        {
            return response([Lang::get('auth.jwt_empty')], 433);
        }

        try
        {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        }
        catch( InvalidArgumentException $exc )
        {
            // provided key/key-array is empty or malformed.
            return response([Lang::get('auth.jwt_empty_key')], 403);
        }
        catch( DomainException $exc )
        {
            // provided algorithm is unsupported OR provided key is invalid OR 
            // unknown error thrown in openSSL or libsodium OR libsodium is required but not available.
            return response([Lang::get('auth.jwt_invalid_key')], 403);
        }
        catch( SignatureInvalidException $exc )
        {
            // provided JWT signature verification failed.
            return response([Lang::get('auth.jwt_signature_failed')], 403);
        }
        catch( BeforeValidException $exc )
        {
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
            return response([Lang::get('auth.jwt_to_early')], 403);
        }
        catch( ExpiredException $exc )
        {
            // provided JWT is trying to be used after "exp" claim.
            return response([Lang::get('auth.jwt_expired')], 403);
        }
        catch( UnexpectedValueException $exc )
        {
            // provided JWT is malformed OR provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR provided key ID in key/key-array is empty or invalid.
            return response([Lang::get('auth.jwt_invalid')], 403);
        }

        return $next($request);
    }
}

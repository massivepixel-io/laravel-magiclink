<?php

namespace MagicLink\Middlewares;

use Closure;
use Illuminate\Http\Request;
use MagicLink\MagicLink;
use MagicLink\Responses\Response;

class MagiclinkMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = (string) $request->route('token');

        $magicLink = static::getValidMagicLinkByToken($token);

        if ($request->method() === 'HEAD') {
            return response()->noContent($magicLink ? 200 : 404);
        }

        if (! $magicLink) {
            return $this->badResponse();
        }

        $responseAccessCode = $magicLink->getResponseAccessCode();

        if ($responseAccessCode) {
            return $responseAccessCode;
        }

        $magicLink->visited();

        return $next($request);
    }

    protected function badResponse()
    {
        $responseClass = config('magiclink.invalid_response.class', Response::class);

        $response = new $responseClass();

        return $response(config('magiclink.invalid_response.options', []));
    }
}

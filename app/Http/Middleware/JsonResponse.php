<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $content = json_decode($response->content(), true);

        $content['status'] = isset($content['status']) ? $content['status'] : $response->status();
        $content['message'] = isset($content['message']) ? $content['message'] : null;
        $content['errors'] = isset($content['errors']) ? $content['errors'] : null;

        return $response->setContent(json_encode($content));
    }
}

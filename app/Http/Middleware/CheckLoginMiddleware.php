<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CheckLoginMiddleware
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
        /**
         * A simple token authentication service
         */
        $token = $request->header('Auth-token');
        if (empty($token)) {
            return response()->json([
                'error' => 'No Token provided'
            ], 403);
        }
        //the token is not empty
        $decrypt = Crypt::decryptString($token);
        $token_data = explode(",", $decrypt); // the first part is the user-identifier, the next part is the issue date
        return response()->json([
            'name' => $token_data[0],
            'issue_date' => $token_data[1]
        ], 200);
        return $next($request);
    }
}

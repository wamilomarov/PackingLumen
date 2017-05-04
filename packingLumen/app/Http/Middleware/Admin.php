<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 04.05.2017
 * Time: 20:18
 */

namespace App\Http\Middleware;

use Closure;
use Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->status == 1){
            return $next($request);
        }
        else{
            $result['status'] = 403;
            return response()->json($result);
        };
    }



}
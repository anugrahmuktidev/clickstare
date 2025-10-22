<?php
// app/Http/Middleware/EnsureUserValidated.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserValidated
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if ($u && method_exists($u, 'isValidated') && ! $u->isValidated()) {
            // jangan loop: jika sudah di halaman pending, biarkan lewat
            if ($request->routeIs('validation.pending')) {
                return $next($request);
            }
            return redirect()->route('validation.pending');
        }
        return $next($request);
    }
}

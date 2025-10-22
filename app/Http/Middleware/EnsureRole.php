<?php
// app/Http/Middleware/EnsureRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $u = $request->user();


        if (! $u) {
            return redirect()->route('login');
        }

        $ok = match ($role) {
            'admin'  => $u?->isAdmin(),
            'guru'   => $u?->isGuru(),
            'siswa'  => $u?->isSiswa(),
            default  => false,
        };

        abort_unless($ok, 403, 'Anda tidak berhak mengakses halaman ini.');
        return $next($request);
    }
}

<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Auth;

// class RoleMiddleware
// {
//     /**
//      * Pakai: ->middleware('role:admin') atau 'role:admin,guru'
//      */
//     public function handle(Request $request, Closure $next, string $roles): Response
//     {
//         $user = $request->user();
//         if (! $user) {
//             return redirect()->route('login');
//         }

//         $allowed = array_map('trim', explode(',', $roles));
//         if (! in_array($user->role, $allowed, true)) {
//             // Redirect halus sesuai role
//             $route = match ($user->role) {
//                 'admin' => 'admin.dashboard',
//                 'guru'  => 'guru.dashboard',
//                 default => 'dashboard',
//             };
//             return redirect()->route($route)->with('ok', 'Anda tidak punya akses ke halaman tersebut.');
//             // Kalau tetap ingin 403: ganti baris di atas dengan -> abort(403)
//         }

//         return $next($request);
//     }
// }

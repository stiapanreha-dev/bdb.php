<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ModuleSetting;

class CheckModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleKey
     */
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        // Проверяем включен ли модуль
        if (!ModuleSetting::isModuleEnabled($moduleKey)) {
            return redirect()->route('home')->with('error', 'Данный модуль отключен администратором.');
        }

        return $next($request);
    }
}

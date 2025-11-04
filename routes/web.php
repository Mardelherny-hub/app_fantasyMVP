<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Support\DashboardRoute;

use App\Http\Controllers\DashboardRouterController;


Route::pattern('locale', 'es|en|fr');

// Redirige "/" al idioma (sesión o Accept-Language)
Route::get('/', function () {
    $supported = ['es', 'en', 'fr'];
    $preferred = request()->getPreferredLanguage($supported) ?? 'es';
    $locale = session('app_locale', $preferred);
    return redirect($locale);
});

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'es|en|fr'],
], function () {
    // Página pública
    Route::get('/', fn() => view('welcome'))->name('home');

    // Jetstream / Fortify dashboard redirect
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [DashboardRouterController::class, 'redirect'])
            ->name('dashboard');
    });

    // Socialite
    //Route::get('auth/google', [SocialiteController::class, 'redirect']);
    //Route::get('auth/google/callback', [SocialiteController::class, 'callback']);
});

// ========================================
// RUTAS DE UTILIDAD Y DESARROLLO
// ========================================

// Limpiar caché y optimizar (producción: restringir a admin)
Route::get('/clear', function() {
    $commands = [
        'config:clear' => 'Config cache cleared',
        'cache:clear' => 'Application cache cleared',
        'route:clear' => 'Route cache cleared',
        'view:clear' => 'Compiled views cleared',
        'event:clear' => 'Cached events cleared',
        'clear-compiled' => 'Compiled services cleared',
        'optimize:clear' => 'All optimization caches cleared',
        'config:cache' => 'Config cached',
        'route:cache' => 'Routes cached',
        'view:cache' => 'Views cached',
    ];

    $output = '<h2 style="color: #10b981;">✅ Comandos Artisan Ejecutados:</h2><ul style="line-height: 2;">';
    
    foreach ($commands as $command => $message) {
        try {
            \Artisan::call($command);
            $output .= '<li>✓ ' . $message . '</li>';
        } catch (\Exception $e) {
            $output .= '<li style="color: #ef4444;">✗ Error en ' . $command . ': ' . $e->getMessage() . '</li>';
        }
    }
    
    $output .= '</ul><h3 style="color: #10b981; margin-top: 20px;">🔧 Limpieza de OPcache:</h3>';
    
    // 🆕 LIMPIAR OPCACHE
    if (function_exists('opcache_reset')) {
        if (opcache_reset()) {
            $output .= '<p>✅ OPcache limpiado correctamente</p>';
        } else {
            $output .= '<p style="color: #ef4444;">❌ Error al limpiar OPcache</p>';
        }
    } else {
        $output .= '<p style="color: #f59e0b;">⚠️ OPcache no disponible o no puede ser limpiado via web</p>';
    }
    
    // Verificar estado de OPcache después
    if (function_exists('opcache_get_status')) {
        $status = opcache_get_status();
        $output .= '<p>📊 Scripts en caché: ' . ($status['opcache_statistics']['num_cached_scripts'] ?? 0) . '</p>';
    }
    
    $output .= '<p style="margin-top: 20px; color: #6b7280;">Tiempo: ' . date('Y-m-d H:i:s') . '</p>';
    
    return $output;
});

// Crear symlink de storage
Route::get('/symlink', function () {
    \Artisan::call('storage:link');
    return 'The storage link has been created!';
});

// Fix temporal - eliminar después de ejecutar
Route::get('/fix-deadlines', function() {
    $fixed = \App\Models\LeagueMember::whereNotNull('squad_deadline_at')
        ->whereHas('user.fantasyTeams', function($q) {
            $q->where('is_squad_complete', true);
        })
        ->update(['squad_deadline_at' => null]);
    
    return "✅ {$fixed} deadlines limpiados. Puedes eliminar esta ruta.";
});


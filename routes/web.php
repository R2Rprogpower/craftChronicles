<?php

use App\Http\Controllers\WebAuthController;
use App\Modules\Messenger\Http\Controllers\BotSetupController;
use App\Modules\Religions\Http\Controllers\ReligionsAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('root');

Route::view('/dima', 'dima')->name('dima');

require base_path('app/Modules/PersonalBrand/web.php');
require base_path('app/Modules/ServiceRequests/web.php');

Route::get('/login', function () {
    return view('auth-login');
})->name('login');

Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');

Route::view('/surgeon', 'surgeon')->name('surgeon');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    $routes = array_map(static function ($route): array {
        return [
            'methods' => implode('|', array_values(array_diff($route->methods(), ['HEAD']))),
            'uri' => '/'.$route->uri(),
            'name' => $route->getName() ?? '-',
            'action' => $route->getActionName(),
            'middleware' => implode(', ', $route->middleware()),
        ];
    }, app('router')->getRoutes()->getRoutes());

    usort($routes, static function (array $a, array $b): int {
        return [$a['uri'], $a['methods']] <=> [$b['uri'], $b['methods']];
    });

    return view('dashboard', [
        'user' => $user,
        'routes' => $routes,
    ]);
})->middleware('auth')->name('dashboard');

Route::get('/bots/setup', [BotSetupController::class, 'index'])->name('bots.setup');
Route::post('/bots/setup', [BotSetupController::class, 'storeBot'])->name('bots.setup.store');
Route::post('/bots/connect-group', [BotSetupController::class, 'connectGroup'])->name('bots.connect-group');
Route::post('/bots/relink-group', [BotSetupController::class, 'relinkGroup'])->name('bots.relink-group');
Route::post('/bots/discover-groups', [BotSetupController::class, 'discoverGroups'])->name('bots.discover-groups');
Route::post('/bots/register-webhook', [BotSetupController::class, 'registerWebhook'])->name('bots.register-webhook');

Route::get('/admin/religions', [ReligionsAdminController::class, 'index'])->name('religions.admin');
Route::post('/admin/religions/link-command', [ReligionsAdminController::class, 'linkCommand'])->name('religions.admin.link-command');
Route::get('/admin/religions/bundle-export', [ReligionsAdminController::class, 'exportBundle'])->name('religions.admin.bundle-export');
Route::post('/admin/religions/bundle-import', [ReligionsAdminController::class, 'importBundle'])->name('religions.admin.bundle-import');
Route::post('/admin/religions/word-mode-next-minute', [ReligionsAdminController::class, 'setWordModeReminderToNextMinute'])->name('religions.admin.word-mode-next-minute');
Route::get('/admin/religions/reminder-wizard', [ReligionsAdminController::class, 'reminderWizardPage'])->name('religions.admin.reminder-wizard.page');
Route::post('/admin/religions/reminder-wizard', [ReligionsAdminController::class, 'reminderWizard'])->name('religions.admin.reminder-wizard');
Route::post('/admin/religions/create', [ReligionsAdminController::class, 'store'])->name('religions.admin.store');
Route::post('/admin/religions/update', [ReligionsAdminController::class, 'update'])->name('religions.admin.update');
Route::post('/admin/religions/delete', [ReligionsAdminController::class, 'destroy'])->name('religions.admin.destroy');
Route::get('/admin/religions/commands/link', [ReligionsAdminController::class, 'commandLinkPage'])->name('religions.admin.command-link');
Route::get('/admin/religions/{entity}/create', [ReligionsAdminController::class, 'createPage'])->name('religions.admin.create');
Route::get('/admin/religions/{entity}', [ReligionsAdminController::class, 'entity'])->name('religions.admin.entity');
Route::get('/admin/religions/{entity}/{id}/edit', [ReligionsAdminController::class, 'edit'])->whereNumber('id')->name('religions.admin.edit');
Route::post('/admin/religions/{entity}', [ReligionsAdminController::class, 'storeEntity'])->name('religions.admin.entity.store');
Route::post('/admin/religions/{entity}/{id}', [ReligionsAdminController::class, 'updateEntity'])->whereNumber('id')->name('religions.admin.entity.update');
Route::post('/admin/religions/{entity}/{id}/delete', [ReligionsAdminController::class, 'destroyEntity'])->whereNumber('id')->name('religions.admin.entity.destroy');

Route::prefix('theme-preview')->group(function () {
    $previewPages = [
        'ui-alerts' => 'ui-alerts',
        'form-elements' => 'form-elements',
        'ui-rangeslider' => 'ui-rangeslider',
    ];

    Route::get('/', function () use ($previewPages) {
        $links = collect($previewPages)
            ->keys()
            ->map(fn (string $page) => [
                'name' => $page,
                'url' => route('theme-preview.page', ['page' => $page]),
            ]);

        return response()->json([
            'message' => 'Theme preview endpoints',
            'endpoints' => $links,
        ]);
    })->name('theme-preview.index');

    Route::get('/{page}', function (string $page) use ($previewPages) {
        abort_unless(array_key_exists($page, $previewPages), 404);

        return view($previewPages[$page]);
    })->name('theme-preview.page');
});

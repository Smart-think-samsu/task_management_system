<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::group(['middleware' => ['auth', 'verified', 'checkActiveStatus']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/graph_data', [DashboardController::class, 'graphData'])->name('dashboard.graph_data');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/status/{user}', [UserController::class, 'userstatus'])->name('user.status');
    Route::post('/user/work_status/{user}', [UserController::class, 'workingstatus'])->name('user.work_status');

    //=== Role Route ===//

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    //=== Permission Route ===//

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/dashboard_settings', [SettingsController::class, 'create'])->name('settings.create');
    Route::put('/dashboard_settings_update', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/sms/update', [SettingsController::class, 'smsSettingUpdate'])->name('settings.sms.update');


    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index'); // Display all tasks
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create'); // Show form to create a new task
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store'); // Store a new task
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show'); // Show a single task
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit'); // Show form to edit a task
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update'); // Update a task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy'); // Delete a task
});


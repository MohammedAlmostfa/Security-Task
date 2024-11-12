<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

// Auth Routes
Route::middleware(['throttle:60,1', 'security'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
    });

});


// User Routes with permission middleware
Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::apiResource('user', UserController::class)->names([
        'index' => 'user.index',
        'store' => 'user.store',
        'show' => 'user.show',
        'update' => 'user.update',
        'destroy' => 'user.destroy',
    ]);
});





// Task Routes with permission middleware
Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::apiResource('task', TaskController::class)->names([
        'index' => 'task.index',
        'store' => 'task.store',
        'show' => 'task.show',
        'update' => 'task.update',
        'destroy' => 'task.destroy',
    ]);

    Route::get('/tasks/Blocked', [TaskController::class, 'showBlockedtask'])->name('tasksBlocked');

});



Route::middleware(['auth', 'check.permission','checkUserRole','throttle:60,1', 'security'])->group(function () {
    Route::put('/tasks/{taskid}/status', [TaskController::class, 'updateStatus'])->name('task.update.status');
    Route::post('/tasks/{taskid}/comments/{id}', [CommentController::class, 'return'])->name('comment.return');
    Route::apiResource('/tasks/{taskid}/comments', CommentController::class)->names([
        'index' => 'comment.index',
        'store' => 'comment.store',
        'show' => 'comment.show',
        'update' => 'comment.update',
        'destroy' => 'comment.destroy',
    ]);

});





// Permission Routes with permission middleware
Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::apiResource('permission', PermissionController::class)->names([
        'index' => 'permission.index',
        'store' => 'permission.store',
        'show' => 'permission.show',
        'update' => 'permission.update',
        'destroy' => 'permission.destroy',
    ]);
});



// Role Routes with permission middleware
Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::apiResource('role', RoleController::class)->names([
        'index' => 'role.index',
        'store' => 'role.store',
        'show' => 'role.show',
        'update' => 'role.update',
        'destroy' => 'role.destroy',
    ]);
    Route::post('/permission/{permissionId}/role/{roleId}', [PermissionController::class, 'addPermissionToRole'])->name('addPermissionToRole');
});




// Additional Routes
Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::post('tasks/{id}/assign', [TaskController::class, 'assignTask'])->name('assignTask');
    Route::put('tasks/{id}/reassign', [TaskController::class, 'reassiganTask'])->name('reassignTask');
});

Route::get('/users/tasks', [TaskController::class, 'showTaskOfUser'])->name('generateDailyReport');


Route::middleware(['auth', 'check.permission','throttle:60,1', 'security'])->group(function () {
    Route::apiResource('/tasks/{taskId}/attachment', AttachmentController::class)->names([
        'index' => 'attachment.index',
        'store' => 'attachment.store',
        'show' => 'attachment.show',
        'update' => 'attachment.update',
        'destroy' => 'attachment.destroy',
    ]);

    Route::post('/tasks/connect', [TaskController::class, 'connectTask'])->name('connectTask');
    Route::get('/download/{id}', [AttachmentController::class, 'download'])->name('downloadAttachment');
    Route::get('/reports/daily-tasks', [TaskController::class, 'generateDailyReport'])->name('generateDailyReport');



});

<?php
use SkillDo\Middleware\AdminAuthMiddleware;
use SkillDo\Middleware\AdminPermissionMiddleware;

Route::middleware([AdminAuthMiddleware::class, AdminPermissionMiddleware::class])->prefix('admin')->group(function() {
    $controller = 'views/plugins/branch-management/controllers';
    //Nhà cung cấp
    Route::get('/branch', 'BranchController@index', ['namespace' => $controller])->name('admin.branch');
    Route::get('/branch/add', 'BranchController@add', ['namespace' => $controller])->name('admin.branch.add');
    Route::get('/branch/edit/{num:id}', 'BranchController@edit', ['namespace' => $controller])->name('admin.branch.edit');
});
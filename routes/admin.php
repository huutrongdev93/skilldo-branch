<?php

use SkillDo\Support\Facades\Route;

Route::middleware('auth:admin')->prefix('admin')->group(function() {
    Route::get('/branch', '\BranchManagement\Controllers\Admin\BranchController@index')->name('admin.branch');
    Route::get('/branch/add', '\BranchManagement\Controllers\Admin\BranchController@add')->name('admin.branch.add');
    Route::get('/branch/edit/{id}', '\BranchManagement\Controllers\Admin\BranchController@edit')->name('admin.branch.edit');
});
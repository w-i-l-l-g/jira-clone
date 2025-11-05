<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{OrganizationController, ProjectController, BoardController, ColumnController, IssueController};

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn (Request $r) => $r->user());

    Route::apiResource('orgs', OrganizationController::class)->only(['index','store','show','update']);
    Route::get('/orgs/{org}/members', [OrganizationController::class, 'members']);
    Route::post('/orgs/{org}/members', [OrganizationController::class, 'invite']); // stub for later

    Route::apiResource('projects', ProjectController::class)->only(['index','store','show','update','destroy']);

    Route::apiResource('boards', BoardController::class)->only(['index','store','show','update']);
    Route::patch('/columns/reorder', [ColumnController::class, 'reorder'])->name('columns.reorder');
    Route::apiResource('columns', ColumnController::class)->only(['index','store','update','destroy']);

    Route::apiResource('issues', IssueController::class)->only(['index','store','show','update','destroy']);
    Route::patch('/issues/{issue}/move', [IssueController::class, 'move']);
});
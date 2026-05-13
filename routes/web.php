<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/login', [ChatController::class, 'login'])->name('chat.login');
Route::post('/logout', function () {
    auth()->logout();
    return redirect()->route('chat.index');
})->name('logout');
Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('message.send')->middleware('auth');
Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages.get');



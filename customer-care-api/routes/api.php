<?php

// routes/api.php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::apiResource('/Tickets',TicketController::class);


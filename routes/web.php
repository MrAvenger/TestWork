<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\ManagingAvailabilityController;
use App\Http\Controllers\TransferController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome'); // Рендерим приветственную страницу
});

Route::prefix('part1')->group(function () {
    Route::get('/', function () {
        $title = 'Стартовая страница';
        return view('part1',compact('title')); // Выведем первую страницу первого раздела (с обычным сообщением в контенте)
    });
    Route::resource('orders', OrdersController::class); // Поскольку здесь CRUD операции, сделан роут resource. Работа с заказами
    Route::get('report', [OrdersController::class, 'report'])->name('orders.report'); // Страница отчёта
    Route::post('getReport', [OrdersController::class, 'getReport'])->name('orders.getReport'); // Получение данных о успешных заказах. Сюда только POST запрос
});

Route::prefix('part2')->group(function () {
    Route::get('/', function () {
        $title = 'Стартовая страница';
        return view('part2', compact('title')); // Выведем первую страницу второго раздела (с обычным сообщением в контенте)
    });
    Route::resource('products', ProductsController::class); // Поскольку здесь CRUD операции, сделан роут resource. Работа с товарами
    Route::resource('warehouses', WarehousesController::class); // Поскольку здесь CRUD операции, сделан роут resource. Работа со складами
    Route::resource('managing', ManagingAvailabilityController::class); // Поскольку здесь CRUD операции, сделан роут resource. Работа с разделом управления наличием
    Route::get('transfer', [TransferController::class, 'create'])->name('transfer'); // Страница перевода товаров
    Route::get('/history', [TransferController::class, 'index'])->name('history'); // Страница истории
    Route::post('transfer/getProducts', [ManagingAvailabilityController::class, 'getProducts'])->name('transfer.getProducts'); // Получаем список продуктов через ajax методом POST
    Route::post('transfer/store', [TransferController::class, 'store'])->name('transfer.store'); // Создание новой записи о переводе и запись в историю. Ожидается POST запрос
    
});


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartTwo\Warehouse; // Модель склада
use Illuminate\Support\Facades\Validator; // Валидатор 
use App\Rules\PartTwo\HasProductsManaging; // Проверка что товары были отправлены

class ManagingAvailabilityController extends Controller
{
    // Основная страница со всеми записями
    public function index()
    {
        $title = 'Управление складами'; // Титул страницы
        return view('part2.managing.index',compact('title')); // Загружаем вид
    }
    // Просмотр информации о конкретной записи
    public function show($id)
    {
        $warehouse = Warehouse::find($id); // Склад
        // Если есть склад
        if($warehouse){
            $title = 'Просмотр товаров на складе';
            return view('part2.managing.show', compact('warehouse', 'title')); // Возвращаем вид и передаём в него данные
        }
        else{
            abort('404'); // Ошибка 404
        }
    }
    // Страница создания записи
    public function create()
    {
        $title = 'Добавление данных о товарах на складе'; // Титул страницы
        return view('part2.managing.create', compact('title')); // Загружаем вид
    }
    // Функция создания новой записи
    public function store(Request $request)
    {
        // Правила
        $rules = [
            'warehouse' => ['required', 'exists:mysql2.warehouses,id', 'unique:mysql2.warehouse_products,warehouse_id'],
            'items' => [new HasProductsManaging],
        ];
        // Сообщения
        $messages = [
            'warehouse.required' => 'Выберите доступный склад!',
            'warehouse.exists' => 'Выберите доступный склад!',
            'warehouse.unique' => 'Выберите доступный склад!',
            'items' => 'Укажите товары!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
        if (!$validator->fails()) {
            $warehouse = Warehouse::find($request->get('warehouse')); // Получаем склад
            $warehouse->addProducts(json_decode($request->get('items'))); // Добавляем товары
            return response()->json(['success' => 'Успешное добавление данных!']); // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Выводим ошибки
    }
    // Страница редактирования
    public function edit($id)
    {
        $warehouse = Warehouse::find($id); // Ищем склад
        $title = "Изменение данных о поступивших товарах";
        if($warehouse){
            return view('part2.managing.edit',compact('title','warehouse')); // Возвращаем страницу просмотра данных о товарах на складе
        }
        else{
            abort(404); // Ошибка 404
        }
    }
    // Функция обновления данных записи
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id); // Получаем склад
        if($warehouse){
            // Правила
            $rules = [
                'items' => [new HasProductsManaging],
            ];
            // Сообщения
            $messages = [
                'items' => 'Укажите товары!',
            ];
            $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
            if (!$validator->fails()) {
                $warehouse->updateProducts(json_decode($request->get('items'))); // Обновляем данные о товарах на складе
                return response()->json(['success' => 'Успешное изменение данных!']); // Успешная операция
            }
            return response()->json(['error' => $validator->errors()]); // Выводим ошибки
        }
        else{
            abort(404); // Ошибка 404
        }
    }
    // Функция удаления записи
    public function destroy($id)
    {
        $warehouse = Warehouse::find($id); // Получаем склад
        if($warehouse){
            $warehouse->removeProducts(); // Удаляем товары со склада
            return response()->json(['success' => 'Успешное удаление данных!']); // Успешная операция
        }
        else{
            abort(404);
        }
    }
    // Получение списка товаров
    public function getProducts(Request $request)
    {
        if($request->get('id')){
            $warehouse = Warehouse::find($request->get('id')); // Получаем склад
            if($warehouse){
                return response()->json(['products' => $warehouse->products]); // Возвращаем товары склада
            }
            else{
                return response()->json(['products' => []]); // Товаров нет
            }
        }
    }

}

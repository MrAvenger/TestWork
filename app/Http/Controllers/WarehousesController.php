<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartTwo\Warehouse; // Модель склада
use Illuminate\Support\Facades\Validator; // Валидатор
use Illuminate\Validation\Rule; // Правила (доп возможности для валидации);

class WarehousesController extends Controller
{
    // Список складов (стр с данными)
    public function index()
    {
        $warehouses = Warehouse::all(); // Получаем склады
        $title = 'Склады';
        return view('part2.warehouses.index', compact('warehouses', 'title')); // Вид с данными
    }
    // Страница создания склада
    public function create()
    {
        $title = 'Создание склада';
        return view('part2.warehouses.create', compact('title')); // Вид с данными
    }
    // Страница отображения склада
    public function show($id)
    {
        $warehouse = Warehouse::find($id); // Склад
        if ($warehouse) {
            $title = 'Просмотр информации о складе';
            return view('part2.warehouses.show', compact('warehouse','title')); // Вид с данными
        } else {
            abort(404); // Ошибка 404
        }
    }
    // Добавление записи
    public function store(Request $request)
    {
        $name = $request->get('name'); // Наименование склада
        $adress = $request->get('adress'); // Адрес
        // Правила
        $rules = [
            'name' => ['required', 'max:255', Rule::unique('mysql2.warehouses')->where(function ($query) use ($name, $adress) {
                return $query->where('name', $name)
                    ->where('adress', $adress);
            })],
            'adress' => 'required|max:255',
        ];
        // Сообщения
        $messages = [
            'name.required' => 'Укажите наименование!',
            'name.max' => 'Максимальная длина поля - 255 символов!',
            'name.unique' => 'Такой склад уже есть!',
            'adress.required' => 'Укажите адрес!',
            'adress.max' => 'Максимальная длина поля - 255 символов!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидация
        if (!$validator->fails()) {
            Warehouse::create($request->all()); // Создаём склад
            return response()->json(['success' => 'Успешное добавление склада!']);  // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Ошибки
    }
    // Страница изменения записи
    public function edit($id)
    {
        $warehouse = Warehouse::find($id); // Находим склад
        if ($warehouse) {
            $title = 'Изменение данных склада';
            return view('part2.warehouses.edit', compact('warehouse', 'title')); // Возвращаем вид с данными
        } else {
            abort(404); // Ошибка 404
        }
    }
    // Обновление
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id); // Получаем склад
        if ($warehouse) {
            $name = $warehouse->name;
            $adress = $warehouse->adress;
            $id_rule = $warehouse->id;
            // Правила
            $rules = [
                'name' => ['required', 'max:255', Rule::unique('mysql2.warehouses')->where(function ($query) use ($name, $adress, $id_rule) {
                    return $query->where('name', $name)
                        ->where('adress', $adress)->where('id', '<>', $id_rule);
                })],
                'adress' => 'required|max:255',
            ];
            // Сообщения
            $messages = [
                'name.required' => 'Укажите наименование!',
                'name.max' => 'Максимальная длина поля - 255 символов!',
                'name.unique' => 'Такой склад уже есть!',
                'adress.required' => 'Укажите адрес!',
                'adress.max' => 'Максимальная длина поля - 255 символов!',
            ];
            $validator = Validator::make($request->all(), $rules, $messages); // Валидация
            if (!$validator->fails()) {
                $warehouse->update($request->all()); // Обновляем данные
                return response()->json(['success' => 'Успешное изменение склада!']); // Успешная операция
            }
            return response()->json(['error' => $validator->errors()]); // Ошибки
        } else {
            abort(404);
        }
    }
    // Удаление
    public function destroy($id)
    {
        Warehouse::find($id)->delete(); // Удаляем склад
        return response()->json(['success' => 'Запись успешно удалена!']); // Успешная операция
    }
}

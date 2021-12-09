<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartTwo\Product; // Модель товара
use Illuminate\Support\Facades\Validator; // Валидатор
use Illuminate\Validation\Rule; // Правила (доп возможности для валидации)

class ProductsController extends Controller
{
    // Основная страница со всеми записями
    public function index()
    {
        $title = 'Товары';
        $products = Product::all(); // Все товары
        return view('part2.products.index', compact('title', 'products')); //Рендерим view и передаём данные
    }
    // Страница создания записи
    function create()
    {
        $title = 'Добавление товара';
        return view('part2.products.create', compact('title')); //Рендерим view и передаём данные
    }
    // Просмотр данных
    public function show($id)
    {
        $product = Product::find($id); // Ищем товар
        if ($product) {
            $title = 'Просмотр информации о товаре';
            return view('part2.products.show', compact('product','title')); // Вовзращаем вид с данными товара
        } else {
            abort(404); // Ошибка 404
        }
    }
    // Добавление новой записи
    public function store(Request $request)
    {
        // Правила
        $rules = [
            'name' => ['required', 'max:255', Rule::unique('mysql2.products')],
            'price' => 'required|numeric',
            'type_id' => 'required|exists:mysql2.product_types,id',
        ];
        // Сообщения
        $messages = [
            'name.required' => 'Укажите наименование!',
            'name.max' => 'Максимальная длина поля - 255 символов!',
            'name.unique' => 'Такое наименование уже есть!',
            'price.required' => 'Укажите цену!',
            'price.numeric' => 'Цена - число!',
            'type_id.exists' => 'Выберите тип из списка!',
            'type_id.required' => 'Выберите тип из списка!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
        if (!$validator->fails()) {
            Product::create($request->all()); // Создаём товар
            return response()->json(['success' => 'Успешное добавление товара!']); // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Ошибки валидации
    }
    // Страница редактирования записи
    public function edit($id)
    {
        $product = Product::find($id); // Получаем товар
        if ($product) {
            $title = 'Изменение товара';
            return view('part2.products.edit', compact('title', 'product')); //Рендерим view и передаём данные
        } else {
            abort(404); // Ошибка 404
        }
    }
    // Обновление записи
    public function update(Request $request, $id)
    {
        $product = Product::find($id); // Получаем товар
        // Правила
        $rules = [
            'name' => [
                'required',
                'max:255',
                Rule::unique('mysql2.products')->ignore($product->id)
            ],
            'price' => 'required|numeric',
            'type_id' => 'required|exists:mysql2.product_types,id',
        ];
        // Сообщения
        $messages = [
            'name.required' => 'Укажите наименование!',
            'name.max' => 'Максимальная длина поля - 255 символов!',
            'name.unique' => 'Такое наименование уже есть!',
            'price.required' => 'Укажите цену!',
            'price.numeric' => 'Цена - число!',
            'type_id.exists' => 'Выберите тип из списка!',
            'type_id.required' => 'Выберите тип из списка!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
        if (!$validator->fails()) {
            if ($product) {
                $product->update($request->all()); // Обновление данных
            }
            return response()->json(['success' => 'Успешное изменение товара!']); // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Ошибки валидации
    }
    // Удаление записи
    public function destroy($id)
    {
        Product::find($id)->delete(); // Удаляем запись
        return response()->json(['success' => 'Запись успешно удалена!']); // Успешная операция
    }
}

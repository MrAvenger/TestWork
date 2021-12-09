<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartTwo\TransferHistory; // Модель истории переводов
use App\Models\PartTwo\Warehouse; // Модель склада
use Illuminate\Support\Facades\Validator; // Валидатор
use App\Rules\WareHouseHasProduct; // Через это правило убедимся, что товар есть на складе
use App\Rules\WareHouseHasProductCount; // Через это правило убедимся, что кол-во переводимого товара - допустимо
use App\Rules\WarehouseFromNotTo; // Убедимся что склад перевода не склад получателя

class TransferController extends Controller
{
    // Страница истории переводов
    public function index()
    {
        $title = 'История передвижения товаров';
        $data = TransferHistory::all(); // Вся история
        return view('part2.transfer.index', compact('title', 'data')); // Вернём вид с данными
    }
    // Страница создания перевода
    public function create()
    {
        $title = 'Перевод между складами';
        return view('part2.transfer.create', compact('title')); // Вернём вид с данными (им.страницы)
    }
    // Функция осуществления перевода
    public function store(Request $request)
    {
        // Правила
        $rules = [
            'from_warehouse' => 'required|exists:mysql2.warehouses,id',
            'to_warehouse' => ['required','exists:mysql2.warehouses,id',new WarehouseFromNotTo($request->get('from_warehouse'), $request->get('to_warehouse'))],
            'product' => ['required','exists:mysql2.products,id', new WareHouseHasProduct($request->get('from_warehouse'), $request->get('product'))],
            'count' => ['required','numeric',new WareHouseHasProductCount($request->get('from_warehouse'), $request->get('product'), $request->get('count'))]
        ];
        // Сообщения
        $messages = [
            'from_warehouse.required' => 'Укажите из какого склада перевод',
            'from_warehouse.exists' => 'Такого склада нет!',
            'to_warehouse.required' => 'Укажите в какой склад перевод!',
            'to_warehouse.exists' => 'Такого склада нет!',
            'product.required' => 'Укажите товар!',
            'product.exists' => 'Такого товара нет!',
            'count.required' => 'Количество - обязательное поле!',
            'count.numeric' => 'Количество - число!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
        if (!$validator->fails()) {
            $warehouse_from = Warehouse::find($request->get('from_warehouse')); // Получим склад, с которого отправляем
            $warehouse_to = Warehouse::find($request->get('to_warehouse')); // Получим склад на который отправляем
            // Если на складе получателя уже есть такой товар
            if($warehouse_to->hasProduct($request->get('product'))){
                $curret_warehouse_product = $warehouse_to->getProduct($request->get('product')); // Получим запись с данными о имеющемся товаре
                $newCount = $request->get('count')+ $curret_warehouse_product->count; // Сформируем новое значение кол-ва
                $warehouse_to->updateProduct($request->get('product'), $newCount); // Обновим данные о товаре
            }
            else{
                $warehouse_to->addProduct($request->get('product'), $request->get('count')); // Просто добавим новые данные о товаре на складе
            }
            $old_warhouse_product =  $warehouse_from->getProduct($request->get('product')); // Получим продукт отправителя
            $new_value = $old_warhouse_product->count - $request->get('count'); // Сформируем новое значение имеющегося на складе товара
            $warehouse_from->updateProduct($request->get('product'), $new_value); // Обновим данные о товаре на складе
            // Добавляем запись о переводе
            TransferHistory::create([
                'warehouse_id_from' => $request->get('from_warehouse'),
                'warehouse_id_to' => $request->get('to_warehouse'),
                'product_id' => $request->get('product'),
                'count' => $request->get('count'),
            ]);
            return response()->json(['success' => 'Успешный перевод!']); // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Выводим ошибки
    }
}

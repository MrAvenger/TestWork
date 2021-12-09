<?php

namespace App\Http\Controllers;
use App\Models\Order; // Модель заказа
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Для использования доп.правил валидации
use Illuminate\Support\Facades\Validator; // Валидатор
use App\Rules\HasProducts; // Своё правило. Проверяем точно ли пришедший список товаров содержит товары из бд
use App\Rules\ValidCountAllProducts; // Своё правило. Допустимо ли указанное количество для указанных товаров

class OrdersController extends Controller
{
    // Вывод всех заказов
    public function index()
    {
        $title = 'Заказы'; // Заголовок страницы
        $orders = Order::all(); // Формируем список заказов
        return view('part1.orders.index', compact('title', 'orders')); //Рендерим view и передаём данные
    }
    // Вывод страницы для создания заказа
    public function create()
    {
        $title = 'Создание заказа'; // Заголовок страницы
        return view('part1.orders.create', compact('title')); //Рендерим view и передаём данные
    }
    // Создание заказа
    public function store(Request $request)
    {
        // Правила
        $rules = [
            'customer' => 'required|max:255',
            'clear_phone' => 'required|min:11|max:11',
            'type' =>
            [
                'required',
                Rule::in(['online', 'offline']),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'completed']),
            ],
            'user_id' => 'exists:users,id',
            'items' => [new HasProducts($request->get('status')), new ValidCountAllProducts(null, $request->get('status'))]
        ];
        // Сообщения
        $messages = [
            'customer.required' => 'Укажите клиента!',
            'customer.max' => 'Максимальная длина поля - 255 символов!',
            'clear_phone.required' => 'Укажите клиента!',
            'clear_phone.max' => 'Номер телефона должен содержать 11 цифр!',
            'clear_phone.min' => 'Номер телефона должен содержать 11 цифр!',
            'type.in' => 'Недопустимое значение!',
            'status.in' => 'Недопустимое значение!',
            'user_id.exists' => 'Такого пользователя нет!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages); // Валидируем данные
        if (!$validator->fails()) {
            $order = Order::add($request->only('customer', 'phone', 'type', 'status', 'user_id')); //Используя релизованный метод в класе, создаём новый заказ
            $order->setItems(json_decode($request->get('items'))); // Добавляем товары в заказ
            return response()->json(['success' => 'Успешное формирование заказа!']); // Успешная операция
        }
        return response()->json(['error' => $validator->errors()]); // Выводим ошибки (возвращаем ответ и через скрипт выводим)
    }

    // Просмотр данных о заказе
    public function show($id)
    {
        // Получаем нужный заказ
        $order = Order::find($id);
        $title = 'Просмотр заказа';
        if ($order) {
            //Возвращаем вид и передаём в него данные.
            return view('part1.orders.show', compact('order', 'title'));
        }
        else{
            abort(404); // Ошибка 404
        }
    }
    // Просмотр данных о заказе с возможностью редактирования
    public function edit($id)
    {
        // Получаем нужный заказ
        $order = Order::find($id);
        if ($order) {
            $title = 'Изменение заказа';
            //Возвращаем вид и передаём в него данные.
            return view('part1.orders.edit', compact('order', 'title'));
        }
        else{
            abort(404); // Ошибка 404
        }
    }
    // Обновление заказа
    public function update(Request $request, $id)
    {
        $order = Order::find($id); // Ищем заказ
        $old_status = $order->status; // Запоминаем старый статус для дальнейших операций
        //Если такой заказ есть, валидируем данные
        if (isset($order)) {
            // Правила
            $rules = [
                'customer' => 'required|max:255',
                'clear_phone' => 'required|min:11|max:11',
                'type' =>
                [
                    'required',
                    Rule::in(['online', 'offline']),
                ],
                'status' => [
                    'required',
                    Rule::in(['active', 'completed', 'canceled']),
                ],
                'user_id' => 'exists:users,id',
                'items' => [new HasProducts($request->get('status')), new ValidCountAllProducts($order->id, $request->get('status'))]
            ];
            // Сообщения
            $messages = [
                'customer.required' => 'Укажите клиента!',
                'customer.max' => 'Максимальная длина поля - 255 символов!',
                'clear_phone.required' => 'Укажите клиента!',
                'clear_phone.max' => 'Номер телефона должен содержать 11 цифр!',
                'clear_phone.min' => 'Номер телефона должен содержать 11 цифр!',
                'type.in' => 'Недопустимое значение!',
                'status.in' => 'Недопустимое значение!',
                'user_id.exists' => 'Такого пользователя нет!',
                'items.required' => 'Укажите товары!',
            ];
            $validator = Validator::make($request->all(), $rules, $messages); //Валидируем данные
            //Если валидация прошла успешно
            if (!$validator->fails()) {
                $order->edit($request->only('customer', 'phone', 'type', 'status', 'user_id')); // Изменяем данные о заказе
                $order->updateItems(json_decode($request->get('items')), $old_status); // Обновляем данные о товарах заказа
                return response()->json(['success' => 'Успешное обновление данных']); // Вернём сообщение о успешном изменении
            }
            return response()->json(['error' => $validator->errors()]); // Вернём ошибки валидации
        }
    }
    // Удаление заказа
    public function destroy($id)
    {
        $order = Order::find($id); // Получим заказ
        if($order){
            $order->remove(); // Удалим заказ
            return response()->json(['success' => 'Запись успешно удалена!']); // Успешная операция
        }
        else{
            abort(404); // Ошибка 404
        }
    }
    //Страница с отчётом
    public function report()
    {
        $title = 'Отчёт по дате';
        return view('part1.orders.report', compact('title'));
    }
    // Отдаём сформированный отчёт
    public function getReport(Request $request)
    {
        $order = new Order;
        // Результат получаем из модели и возвращаем (только завершённые заказы по дате).
        return response()->json(['orders' => $order->getTotalCostItems('completed', $request->get('completed_at'))]); 
    }
}

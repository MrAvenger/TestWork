<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product; // Модель товаров
use App\Models\OrderItem; // Модель товаров и заказов (товары_и_заказы)

class Order extends Model
{
    use HasFactory;
    // Что разрешается заполнять
    protected $fillable = ['customer', 'phone', 'type', 'status', 'user_id'];
    // Не требуется, поскольку используется свой способ
    public $timestamps = false;
    // Менеджер
    public function user()
    {
        return $this->belongsTo('App\Models\User'); // Установка связи с users.
    }
    // Товары заказа 
    public function items()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')->withPivot('count', 'discount', 'cost');
    }

    // Создание заказа
    public static function add($data)
    {
        $order = new static;
        $order->fill($data);  // Заполняем доступные поля из $filasble
        $order->setCreated_At(); // Устанавливаем дату создания заказа
        $order->setCompleted_At(); // Устанавливаем дату завершения заказа (нельзя оставить пустым, нет значения поумолчанию)
        $order->save(); // Сохраняем
        return $order; // Возвращаем созданный заказ (объект)
    }

    // Изменение заказа
    public function edit($data)
    {
        $this->fill($data); // Заполняем доступные поля из $filasble
        if ($this->status == 'completed') {
            $this->setCompleted_At(); // Устанавливаем дату завершения заказа (если он завершён)
        }
        $this->save(); // Сохраняем
    }
    // Удаляем заказ
    public function remove()
    {
        // Сначала проверим есть ли у заказа товары
        if ($this->items) {
            foreach ($this->items as $item) {
                $this->updateCountProduct($item->id, 'remove', $item->pivot->count); // Если есть - проверим можно ли вернуть назад и если можно - вернём (статус не completed и не canceled)
            }
            $this->items()->detach(); // Удалим элементы (товары). Чтобы не было ошибки с FK
        }
        $this->delete(); // Удалим заказ
    }

    // Устанавливаем значение для поля "created_at"
    public function setCreated_At()
    {
        // Поставим текущую дату
        $this->created_at = date('Y-m-d H:i:s');
    }

    // Устанавливаем значение для поля "completed_at"
    public function setCompleted_At()
    {
        // Поскольку у столбца нет значения по умолчанию и оно не может быть null, поставим текущую дату
        $this->completed_at = date('Y-m-d H:i:s');
    }
    // Устанавливаем товары в заказ
    public function setItems($items)
    {
        foreach ($items as $item) {
            $obj = $item[1]; // Объект товара корзины
            $this->items()->attach($obj->id, [
                'count' => $obj->count,
                'discount' => $obj->discount,
                'cost' => $obj->cost
            ]);
            $this->updateCountProduct($obj->id, 'new', $obj->count); // Обновляем данные товаров
        }
    }
    // Обновляем товары заказа
    public function updateItems($items, $old_status = null)
    {
        $arrIds = []; // Пришедшие id товаров
        $old_items=$this->items; // Текущие товары
        if ($items) {
            foreach ($items as $item) {
                $obj = $item[1];
                $this->updateCountProduct($obj->id, 'update', $obj->count, $old_status);
                $this->items()->syncWithPivotValues($obj->id, [
                    'count' => $obj->count,
                    'discount' => $obj->discount,
                    'cost' => $obj->cost
                ], false);
                array_push($arrIds, $obj->id);
            }
        }
        if($old_items){
            $ids = [];
            foreach ($old_items as $item) {
                array_push($ids, $item->id); 
            }
            if ($arrIds && $ids) {
                $result = array_diff($ids, $arrIds); // Извлекаем элементы, которые отменены (удалены из корзины)
                if ($result) {
                    foreach ($result as $res) {
                        foreach ($old_items as $item) {
                            if ($item->id == $res) {
                                $this->updateCountProduct($item->id, 'canceled_items', $item->pivot->count, $old_status);
                            }
                        }
                    }
                }
            }
        }
        $this->items()->sync($arrIds);
    }

    // Получаем значение поля "type" в нужном виде
    public function getType()
    {
        // Вернём тип
        return ($this->type == 'online') ? 'Онлайн' : 'Оффлайн';
    }

    // Получаем значение поля "status" в нужном виде
    public function getStatus()
    {
        // Вернём статус
        switch ($this->status) {
            case 'active': {
                    return 'Активный';
                }
                break;
            case 'canceled': {
                    return 'Отменён';
                }
                break;
            case 'completed': {
                    return 'Завершён';
                }
                break;
            default: {
                    return 'Не определён';
                }
        }
    }
    // Имеет товар
    public function hasProduct($id)
    {
        foreach ($this->items as $product) {
            if ($product->id == $id) {
                return $product;
            }
        }
        return false;
    }
    // Обновление кол-ва товара
    function updateCountProduct($product_id, $type, $count, $old_status = null)
    {
        $product = Product::find($product_id);
        switch ($type) {
            // Работа с новыми данными
            case 'new': {
                    $product->stock -= $count; // Уменьшаем колв-во продукта
                    $product->save(); // Сохраня кол-во продукта
                }
                break;
            // Работа со старыми данными
            case 'update': {
                    $old_item = OrderItem::where(['order_id' => $this->id, 'product_id' => $product_id])->first();
                    if ($old_item) {
                        if ($this->status != 'canceled' && $old_status != 'canceled') {
                            if ($count != $old_item->count) {
                                $product->stock += $old_item->count;
                                $product->stock -= $count;
                                $product->save();
                            }
                        } else if ($old_status != 'canceled' && $this->status == 'canceled') {
                            $product->stock += $count;
                            $product->save();
                        } else if ($old_status == 'canceled' && $this->status != 'canceled') {
                            $product->stock -= $count;
                            $product->save();
                        }
                    } else {
                        $product->stock -= $count;
                        $product->save();
                    }
                }
                break;
            // Удаление заказа
            case 'remove': {
                    // Если заказ завершённый или отменённый, то не списываем кол-во товара
                    if ($this->status != 'completed' && $this->status != 'canceled') {
                        $product->stock += $count; // списываем прешедшее кол-во
                        $product->save(); // Сохраняем
                    }
                }
                break;
            // Если были отменены товары (удалены из корзины)
            case 'canceled_items': {
                    // В данном случае будут вовзращены только те товары, которые не присутствуют в отменённом заказе
                    if($old_status != 'canceled') {
                        $product->stock += $count;
                        $product->save();
                    }
                }
        }
    }
    // Получение итоговой стоимости всех товаров
    public function getTotalCostItems($type = 'all', $date = null)
    {
        switch ($type) {
            // Считаем абсолютно все позиции заказа
            case 'all': {
                    $val = 0;
                    foreach ($this->items as $item) {
                        $val += $item->pivot->cost;
                    }
                    return $val;
                }
                break;
            // Считаем кол-во только завершённых заказов по дате
            case 'completed': {
                    $orders = Order::whereDate('completed_at', $date)->where('status', 'completed')->get(); // Только завершённые заказы
                    $countOrders = 0; // Кол-во заказов
                    $totalCost = 0; // Суммарная стоимость
                    $success = true; // Заказы есть (или же их нет, т.е это статус операции)
                    if ($orders) {
                        foreach ($orders as $order) {
                            $countOrders++;
                            if ($order->items) {
                                foreach ($order->items as $item) {
                                    $totalCost += $item->pivot->cost;
                                }
                            }
                        }
                    }
                    if ($totalCost == 0 || $countOrders == 0) {
                        $success = false;
                    }
                    return ['totalCost' => $totalCost, 'countOrders' => $countOrders, 'success' => $success];
                }
                break;
        }
    }
}

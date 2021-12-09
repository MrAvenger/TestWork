<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Product;
use App\Models\Order;

class ValidCountAllProducts implements Rule
{
    protected $names = []; // Поля
    protected $order_id = null; // Заказ
    protected $status = null; // Статус заказа
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($order_id, $status)
    {
        $this->order_id = $order_id;
        $this->status = $status;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validated = true;
        $cartArr = json_decode($value); // Массив корзины
        if($cartArr){
            // Если новый заказ
            if (!$this->order_id) {
                foreach ($cartArr as $cartItem) {
                    $obj = $cartItem[1]; // Объект (только на данном индексе)
                    $product = Product::find($obj->id); // Товар
                    if (!empty($product)) {
                        if ($product->stock < $obj->count) {
                            $validated = false;
                            array_push($this->names, $obj->name);
                        }
                    }
                }
            } 
            // Если старый заказ
            else {
                foreach ($cartArr as $cartItem) {
                    $obj = $cartItem[1]; // Объект (только на данном индексе)
                    $order = Order::find($this->order_id); // Заказ
                    $product = Product::find($obj->id); // Товар
                    if (empty($product)) {
                        $validated = false;
                        array_push($this->names, $obj->name);
                    } else {
                        $existingItem = $order->hasProduct($product->id);
                        if (
                            $existingItem && $this->status != 'canceled' && $order->status != 'canceled'
                        ) {
                            $product_count = $product->stock;
                            if ((($product_count + $existingItem->count) - $obj->count) < 0) {
                                $validated = false;
                            }
                        } else if ($existingItem) {
                            $product_count = $product->stock;
                            if (($product_count - ($obj->count + $existingItem->count)) < 0) {
                                $validated = false;
                            }
                        } else if (!$existingItem) {
                            $product_count = $product->stock;
                            if (($product_count - ($obj->count + $product->count)) < 0) {
                                $validated = false;
                            }
                        }
                    }
                }
            }
        }
        return $validated;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (count($this->names) > 0) {
            return 'Недопустимое количество следующих товаров: ' . implode(', ', $this->names);
        }
    }
}

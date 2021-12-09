<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Product;

class HasProducts implements Rule
{
    protected $names = []; // Товаров нет в базе (имена)
    protected $status; // Статус заказа
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($status)
    {
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
            foreach($cartArr as $cartItem){
                $obj = $cartItem[1]; // Объект на этом индексе
                if($obj){
                    $product = Product::find($obj->id); // Товар
                    if (empty($product)) {
                        $validated = false;
                        array_push($this->names, $obj->name);
                    }
                }
            }
        }
        else if($this->status!='canceled'){
            $validated = false;
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
            return 'Проверьте перечень товаров! Данных товаров нет в базе: ' . implode(', ', $this->names);
        }
        else{
            return 'Укажите товары!';
        }
    }
}

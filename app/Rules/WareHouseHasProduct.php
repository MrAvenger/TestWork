<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class WareHouseHasProduct implements Rule
{

    protected $warehouse_id; // Склад
    protected $product_id; // Товар
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($warehouse, $product)
    {
        $this->warehouse_id = $warehouse; // Установим значение
        $this->product_id = $product; // Установим значение
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
        // Получим запись (если есть)
        $record = DB::connection('mysql2')->table('warehouse_products')->where(['warehouse_id' =>$this->warehouse_id,'product_id'=>$this->product_id])->first();
        // Если она есть, значит всё ок, иначе ошибка
        if($record){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'На выбранном складе нет такого товара!';
    }
}

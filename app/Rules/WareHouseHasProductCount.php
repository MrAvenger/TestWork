<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class WareHouseHasProductCount implements Rule
{

    protected $warehouse_id; // Склад
    protected $product_id; // Товар
    protected $count; // Кол-во
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($warehouse, $product,$count)
    {
        $this->warehouse_id = $warehouse;
        $this->product_id = $product;
        $this->count = $count;
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
        // Получаем запись с данными о товаре на складе
        $record = DB::connection('mysql2')->table('warehouse_products')->where(['warehouse_id' => $this->warehouse_id, 'product_id' => $this->product_id])->first();
        if ($record) {
            if($record->count >= $this->count){
                return $validated;
            }
            else{
                $validated = false;
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
        return 'Такого количества нет на складе!';
    }
}

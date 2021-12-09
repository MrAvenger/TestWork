<?php

namespace App\Rules\PartTwo;

use Illuminate\Contracts\Validation\Rule;
use App\Models\PartTwo\Product;

class HasProductsManaging implements Rule
{
    protected $names = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        // Здесь проверяется пришли ли товары или были подставлены фейковые данные
        $validated = true;
        $storageArr = json_decode($value);
        if ($storageArr) {
            foreach ($storageArr as $storageItem) {
                $obj = $storageItem[1];
                if ($obj) {
                    $product = Product::find($obj->id);
                    if (empty($product)) {
                        $validated = false;
                        array_push($this->names, $obj->name);
                    }
                }
            }
        } else {
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
        } else {
            return 'Укажите товары!';
        }
    }
}

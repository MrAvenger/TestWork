<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class WarehouseFromNotTo implements Rule
{
    protected $from; // Склад откуда
    protected $to; // Склад куда
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($from,$to)
    {
        $this->from = $from; // Установим значение
        $this->to = $to; // Установим значение
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
        // Если значения пришли
        if($this->from && $this->to){
            // Проверим не является ли склад-отправитель складом-получателем
            if($this->from == $this->to){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Склады должны быть разными!';
    }
}

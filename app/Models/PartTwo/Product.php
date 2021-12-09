<?php

namespace App\Models\PartTwo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // Подключение (используется вторая бд)
    protected $fillable = ['name','price','type_id']; // Заполняемые поля
    // Связь с таблицей типов товаров (можем забрать тип товара)
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }
}

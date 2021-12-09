<?php

namespace App\Models\PartTwo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PartTwo\Product;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // Подключение
    protected $fillable = ['name','adress']; // Заполняемы поля
    public $timestamps = false; // created_at и updated_at автоматически не заполняем (их нет)
    //Связь с товарами (у одного заказа много товаров)
    public function products()
    {
        return $this->belongsToMany(Product::class,'warehouse_products', 'warehouse_id','product_id')->withPivot('count');
    }
    // Добавление товаров
    public function addProducts($items)
    {
        foreach ($items as $item) {
            $obj = $item[1]; // Объект товара корзины
            $this->products()->attach($obj->id, [
                'count' => $obj->count,
            ]);
        }
    }
    //  Проверка есть ли товары
    public function hasProducts()
    {
        return $this->products()->where('warehouse_id', $this->id)->exists();
    }
    //  Проверка есть ли определённый товар
    public function hasProduct($id)
    {
        return $this->products()->where('product_id', $id)->exists();
    }
    // Получаем товар
    public function getProduct($id)
    {
        return DB::connection('mysql2')->table('warehouse_products')->where(['warehouse_id' => $this->id, 'product_id' => $id])->first();
    }
    // Обновляем данные о товарах склада
    public function updateProducts($items)
    {
        $arrIds = []; // Пришедшие id товаров
        if ($items) {
            foreach ($items as $item) {
                $obj = $item[1];
                $this->products()->syncWithPivotValues($obj->id, [
                    'count' => $obj->count,
                ], false);
                array_push($arrIds, $obj->id);
            }
        }
        $this->products()->sync($arrIds);
    }

    // Обновляем определённый товар склада
    public function updateProduct($id,$count)
    {
        $this->products()->syncWithPivotValues($id, [
            'count' => $count,
        ], false);
        DB::connection('mysql2')->table('warehouse_products')->where('warehouse_id', $this->id)->where('count','=','0')->delete();
    }
    // Добавляем товар на склад
    public function addProduct($product, $count)
    {
        $this->products()->attach($product, [
            'count' => $count,
        ]);
    }
    // Удаляем товары склада
    public function removeProducts()
    {
        $this->products()->detach();
    }
}

<?php

namespace App\Models\PartTwo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PartTwo\Warehouse;
use App\Models\PartTwo\Product;

class TransferHistory extends Model
{
    use HasFactory;
    protected $connection = "mysql2";
    protected $fillable = ['warehouse_id_from', 'warehouse_id_to', 'product_id', 'count'];
    public $timestamps = true;

    public function warehouse_from()
    {
       return $this->belongsTo(Warehouse::class, 'warehouse_id_from','id');
    }

    public function warehouse_to()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id_to','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

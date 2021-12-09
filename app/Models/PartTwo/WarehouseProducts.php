<?php

namespace App\Models\PartTwo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProducts extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = 'mysql2'; // Подключение
}

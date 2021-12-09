<?php

namespace App\Models\PartTwo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // Подключение
}

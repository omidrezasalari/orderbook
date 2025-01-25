<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;
    protected $fillable = ['id','type','price','quantity','created_at'];


    public function getId(): int
    {
        return (int)$this->id;
    }
    public function getType(): string
    {
        return ucfirst($this->type);
    }

    public function getPrice(): string
    {
        return number_format($this->price, 2, '.', ',');
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getStatus(): string
    {
        return ucfirst($this->status);
    }
}

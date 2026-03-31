<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Transaction;
use App\Models\Debt;

class Customer extends Model
{
    protected $table = 'customers';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'id');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'customer_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Customer;

class Debt extends Model
{
    protected $table = 'debts';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}

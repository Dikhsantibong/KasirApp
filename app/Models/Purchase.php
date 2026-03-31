<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Supplier;

class Purchase extends Model
{
    protected $table = 'purchases';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}

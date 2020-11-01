<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contract_name',
        'contract_phone',
        'last_used_at',
    ];

    protected $datas = ['last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttributeee()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}

<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;


class Organization extends Model {
    use CrudTrait;
    protected $table = 'organizations';
    protected $fillable = ['name', 'code'];

public function users() {
    return $this->hasMany(User::class);
}

// Новая связь: Много отправителей
    public function shippers()
    {
        return $this->belongsToMany(Shipper::class, 'organization_shipper');
    }
}

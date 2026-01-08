<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use CrudTrait;
    protected $fillable = ['name', 'code'];

public function users() {
    return $this->hasMany(User::class);
}
}

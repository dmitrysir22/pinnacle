<?php
namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    use CrudTrait;

    protected $table = 'shippers';
    protected $fillable = [ 'external_code', 'name', 'address1', 'address2', 'city', 'state', 'postcode', 'country'];

// Новая связь: Много организаций
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_shipper');
    }
}
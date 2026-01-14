<?php
namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ShipperRequest extends Model
{
    use CrudTrait;

    protected $table = 'shipper_requests';
    protected $fillable = [ 'user_id', 'status', 'name', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'admin_comment'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Аксессор для удобного отображения полного адреса
    public function getFullAddressAttribute()
    {
        return collect([$this->address1, $this->city, $this->country])->filter()->implode(', ');
    }
}



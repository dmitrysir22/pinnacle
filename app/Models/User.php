<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use App\Notifications\AgentResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use CrudTrait;
    use HasFactory, Notifiable, HasRoles;
    use MustVerifyEmailTrait;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
		'email_verified_at' ,
		'organization_id',
		'access_level',
		'last_login_at',
		'login_count'
		
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---- Helpers ----

    public function isAgent(): bool
    {
        return $this->hasRole('AgentUser');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isLocked(): bool
    {
        return $this->locked_until && now()->lt($this->locked_until);
    }

    public function canResendOtp(): bool
    {
        return ! $this->otp_last_sent_at ||
            now()->diffInSeconds($this->otp_last_sent_at) >= 60;
    }
	
public function organization() { 
    return $this->belongsTo(Organization::class, 'organization_id');
}

public function sendPasswordResetNotification($token)
{
    // Только для агентов
    if ($this->isAgent()) {
        $this->notify(new AgentResetPasswordNotification($token));
        return;
    }

    // fallback (на будущее)
    parent::sendPasswordResetNotification($token);
}

}

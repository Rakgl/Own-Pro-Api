<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens;  // Changed from Laravel\Passport\HasApiTokens
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\DataScope;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, DataScope, \OwenIt\Auditing\Auditable;

	protected $auditInclude = [
		'name',
		'email',
		'image',
		'username',
		'status',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'name',
		'email',
		'image',
		'username',
		'password',
		'customer_id',
		'status',
		'role_id',
		'locale',
		'created_by',
		'updated_by',
		'update_num',
		'fcm_token',
		'platform',
		'avatar_fallback_color',
		'language',
    ];

    public function findForPassport($identifier) {
        return $this->where('username', $identifier)->first();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Local Scope
    public function scopeExceptRoot($q) {
        return $q->where('username', '!=', 'admin')
               ->where('username', '!=', 'developer');
    }

    public function scopeExceptCurrentUser($q) {
        return $q->where('id', '!=', auth()->user()->id);
    }

    //Relationship
    public function role()
    {
		return $this->belongsTo(Role::class, 'role_id');
	}

	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function updatedBy()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	// public function customer()
	// {
	// 	return $this->belongsTo(Customer::class, 'customer_id');
	// }

	// public function stations()
	// {
	// 	return $this->belongsToMany(Station::class, 'station_user');
	// }

	public function logins() {
		return $this->hasMany(UserLogin::class);
	}
}

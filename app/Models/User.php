<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'water_units', 'balance', 'agent_id',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'
    ];

    protected $casts = [
        'water_units' => 'float',
        'balance' => 'float',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array'
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function submittedReadings()
    {
        return $this->hasMany(MeterReading::class, 'agent_id');
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'customer_id')->latest();
    }

    public function latestMeterReading()
    {
        return $this->hasOne(MeterReading::class, 'customer_id')->latestOfMany();
    }

    public function pendingMeterReadings()
    {
        return $this->meterReadings()->where('status', 'pending');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->whereNull('read_at');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function customers()
    {
        return $this->hasMany(User::class, 'agent_id')->where('role', 'customer');
    }
}
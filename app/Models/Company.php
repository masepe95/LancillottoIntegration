<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable {

    use Notifiable;

    protected $guard = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
        'email_verified_at' => 'datetime',
    ];

    public function profile() {
        return $this->hasOne(CompanyProfile::class, 'company_id');
    }

    public function hrProfile() {
        return $this->hasOne(CompanyHRProfile::class, 'company_id');
    }

    public function webReputation() {
        return $this->hasOne(WebReputation::class);
    }

    public function current_tasks() {
        return $this->hasOne(CompanyCurrentTask::class);
    }
    
    public function most_wanted_tasks() {
        return $this->hasOne(CompanyMostWantedTask::class);
    }
    
    public function creditsAccount() {
        return $this->hasOne(CreditsAccount::class, 'company_id');
    }

    public function ricerca() {
        return $this->hasMany(CompanyResearchCandidate::class, 'company_id');
    }
    
    public function matches() {
        return $this->hasMany(CompanyMatch::class, 'company_id');
    }
    
    public function cvs() {
        return $this->hasMany(CompanyPurchase::class, 'company_id');
    }
    
}

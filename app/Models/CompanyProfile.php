<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;


class CompanyProfile extends Model {

    use HasFactory;

    // public $table = 'company_profile';

    protected $fillable = [
        'name',
        'address',
        'address_GPS_lat',
        'address_GPS_lon',
        'iva',
        'referente',
        'referenteruolo',
        'referenteemail',
        'mobile',
        'content',
        'company_website',
        'company_logo',
        //'company_id', //??? fillable?
        'company_id',
        
        'billing_codice_destinatario',
        'billing_pec',

        'billing_address',
        'billing_address_number',
        'billing_zip_code',
        'billing_city',
        'billing_district_code',
        'billing_nation_code',

    ];

    public function cancel_logo() {
        Storage::delete($this->logo);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    /*
    public function webReputation() {
        return $this->hasOne(WebReputation::class);
    }
    */
    
}

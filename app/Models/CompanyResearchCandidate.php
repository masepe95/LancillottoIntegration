<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Experience;
use App\Models\Task;

class CompanyResearchCandidate extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'search_type',
        'status_id',
        'company_id',
        'sector_id',
        'task_id',
        'test_level_id',
        'job_experience_level_id',
        'job_contract_id',
        'job_contractual_conditions_id',
        'job_working_time_id',
        'comune',
        'GPS_lat',
        'GPS_lon',
        'content',
        'compenso',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function experience() {
        return $this->belongsTo(Experience::class,'job_experience_level_id','id'); 
    }

    public function workingTime() {
        return $this->belongsTo(JobWorkingTime::class,'job_working_time_id','id');
    }

    public function contractualCondition() {
        return $this->belongsTo(JobContractualCondition::class,'job_contractual_conditions_id','id');
    }

    public function task() {
        return $this->belongsTo(Task::class); // TO BE CHECKED...
    }

    public function deductCredit() {
        return $this->hasMany(CompanyCreditDeduct::class,'company_search_id','id');
    }

    public function matches() {
        return $this->hasMany(CompanyMatch::class,'company_research_id','id');
    }

    use HasFactory;
}

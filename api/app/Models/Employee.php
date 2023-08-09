<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_id',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = request()->user()->id;
        });
    }

    // to concat first and last name into full_name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->select('id','name','logo','website');
    }

    // Creator of this record
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->select('name');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Company extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'logo',
        'website',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = request()->user()->id;
        });
    }
    
    // Get All employees of the company
    public function employees(){
        return $this->hasMany(Employee::class)->select('id','first_name','last_name','email');
    }
    
    // Creator of this record
    public function creator(){
        return $this->belongsTo(User::class, 'created_by')->select('name');
    }
}

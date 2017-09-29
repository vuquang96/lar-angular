<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = "employee";

    protected $fillable = [
        'id', 'name', 'photo', 'job_title', "phone", "email", "birthday", 'sex', 'address', 'date_start_work', '	id_intialized', 'id_department'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id_intialized'
    ];

    public $timestamps = true;

    public function department(){
    	return $this->belongsTo("App\Department", "id_department");
    }

    public function user(){
    	return $this->belongsTo("App\User", "id_intialized");
    }
}

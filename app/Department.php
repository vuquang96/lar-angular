<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
	protected $table = "department";

    protected $fillable = [
        'id', 'name', 'office_phone', 'manager', "id_intialized",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ ];

    public $timestamps = true;

    public function employee(){
    	return $this->hasMany("App\Employee", 'id_department');
    }

    public function user(){
    	return $this->belongsTo("App\User", "id_intialized");
    }
}

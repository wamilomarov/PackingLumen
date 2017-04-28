<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:19
 */

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'last_name', 'first_name', 'email', 'office_phone', 'mobile_phone', 'position'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];


}
<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:20
 */

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class CompanyNote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'user_id', 'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}
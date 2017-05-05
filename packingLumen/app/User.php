<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'name', 'email', 'password', 'api_token', 'office_phone', 'mobile_phone'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'created_at', 'updated_at'
    ];

    static function exists($params = [])
    {
        if (count($params) > 0){
            $count = DB::table('users');

            foreach ($params as $key => $value){
                $count = $count->where($key, $value);
            }
            $count = $count->count();

            if ($count > 0){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    public function hasAccess($access)
    {
        $users_access = DB::table('users_accesses')->select('table')->where('user_id', '=', $this->id)->where('table', '=', $access)->count();

        if ($users_access > 0){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

}

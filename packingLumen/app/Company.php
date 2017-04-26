<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 22.04.2017
 * Time: 17:50
 */

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'address', 'phone', 'website', 'workers_count'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    static function exists($table, $params = [])
    {
        if (count($params) > 0){
            $count = DB::table($table);

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

    public function getNotes()
    {
        $notes = DB::table('company_notes')->leftJoin('users', 'users.id', '=', 'company_notes.user_id')->
        select('company_notes.*', 'users.last_name as user_last_name', 'users.first_name as user_first_name')->where('company_notes.company_id', '=', $this->id)->get();
        return $notes;
    }

    public function getWorkers()
    {
        $workers = DB::table('workers')->select('*')->where('company_id', '=', $this->id)->get();
        return $workers;
    }

}

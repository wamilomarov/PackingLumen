<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:21
 */

namespace App\Http\Controllers;

use App\Meeting;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;

class MeetingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function add(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('user_id') && $request->has('worker_id') &&
            $request->has('meeting_date')) {

            $meeting = new Meeting($request->all());

            if($meeting){
                $result['status'] = 200;
            } else {
                $result['status'] = 402;
            }

        } else {
            $result['status'] = 405;
        }

        return response()->json($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();

        if (!Company::exists('meetings', ['id' => $request->get('updated_meeting_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('meetings')->where('id', '=', $request->get('updated_meeting_id'));

        $params = [];

        if ($request->has('company_id')){
            $params['company_id'] = $request->get('company_id');
        }

        if ($request->has('worker_id')){
            $params['worker_id'] = $request->get('worker_id');
        }

        if ($request->has('user_id')){
            $params['user_id'] = $request->get('user_id');
        }

        if ($request->has('meeting_date')){
            $params['meeting_date'] = $request->get('meeting_date');
        }

        if ($request->has('organizer')){
            $params['organiser'] = $request->get('organiser');
        }

        if ($request->has('note')){
            $params['note'] = $request->get('note');
        }

        if (count($params) > 0){
            $update->update($params);
            $result['status'] = 200;
        }
        else{
            $result['status'] = 405;
        }

        return response()->json($result);

    }

    public function delete($id)
    {
        if (!Company::exists('meetings', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('meetings')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        $result['data']['meeting'] = DB::table('meetings')->leftJoin('companies', 'companies.id', '=', 'meetings.company_id')->
        leftJoin('users', 'meetings.user_id', '=', 'users.id')->
        leftJoin('workers', 'meetings.worker_id', '=', 'workers.id')->
        select('meetings.*', 'companies.name as company_name', 'workers.first_name as worker_first_name', 'workers.last_name as worker_last_name', 'users.first_name as users_first_name', 'users.last_name as users_last_name')->
        where('meetings.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

    public function getReminders($id)
    {
        $result['data']['meetings'] = DB::table('meetings')->leftJoin('companies', 'companies.id', '=', 'meetings.company_id')->
        leftJoin('users', 'meetings.user_id', '=', 'users.id')->
        leftJoin('workers', 'meetings.worker_id', '=', 'workers.id')->
        select('meetings.*', 'companies.name as company_name', 'workers.first_name as worker_first_name', 'workers.last_name as worker_last_name', 'users.first_name as users_first_name', 'users.last_name as users_last_name')->
        where('meetings.id', '=', intval($id))->where(DB::raw("DATEDIFF(meeting_date, NOW())"), '<=', 3)->get();
        $result['status'] = 200;
        return response()->json($result);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 22.04.2017
 * Time: 17:46
 */


namespace App\Http\Controllers;

use App\Company;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;

class UserController extends Controller
{
    private $salt;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->salt="packingcrm";
    }

    public function login(Request $request){

//        var_dump($request->json()->has('email'));
//        var_dump($request);

        $request = $request->json();
        if ($request->has('email') && $request->has('password')) {


            $user = User:: where("email", "=", $request->get('email'))
                ->where("password", "=", sha1($this->salt.$request->get('password')))
                ->first();
            if ($user) {
                $token=uniqid('crm', true);
                $user->api_token=$token;
                $user->save();
                $result['status'] = 200;
                $result['data']['api_token'] = $token;
                $result['data']['user'] = $user;
                return response()->json($result);
            } else {
                $result['status'] = 407;
            }
        } else {
            $result['status'] = 405;
            return response()->json($result);
        }
    }

    public function logout(Request $request)
    {
        $request = $request->json();

        $user = User::find($request->get('user_id'));
        $user->api_token = uniqid('crm', true);
        $user->save();

        $result['status'] = 200;
        return response()->json($result);
    }

    public function register(Request $request){
        $request = $request->json();

        if ($request->has('email') && $request->has('password') && $request->has('first_name') && $request->has('last_name') &&
            $request->has('office_phone') && $request->has('mobile_phone')) {
            if (Company::exists('users', ['email' => $request->get('email')])){
                $result['status'] = 410;
            }
            else{
                $user = new User;
                $user->email=$request->get('email');
                $user->first_name=$request->get('first_name');
                $user->last_name=$request->get('last_name');
                $user->mobile_phone=$request->get('mobile_phone');
                $user->office_phone=$request->get('office_phone');
                $user->password=sha1($this->salt.$request->get('password'));
                $token=uniqid('crm', true);
                $user->api_token=$token;
                if ($request->has('status')){
                    $user->status=$request->get('status');
                }
                if($user->save()){
                    $result['status'] = 200;
                } else {
                    $result['status'] = 402;
                }
            }

        } else {
            $result['status'] = 405;
        }

        return response()->json($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();

        if (!Company::exists('users', ['id' => $request->get('updated_user_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('users')->where('id', '=', $request->get('updated_user_id'));

        $params = [];

        if ($request->has('status')){
            $params['status'] = $request->get('status');
        }

        if ($request->has('email')){
            $params['email'] = $request->get('email');
        }

        if ($request->has('first_name')){
            $params['first_name'] = $request->get('first_name');
        }

        if ($request->has('last_name')){
            $params['last_name'] = $request->get('last_name');
        }

        if ($request->has('mobile_phone')){
            $params['mobile_phone'] = $request->get('mobile_phone');
        }

        if ($request->has('office_phone')){
            $params['office_phone'] = $request->get('office_phone');
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
        if (!Company::exists('users', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('users')->where('users.id', '=', $id)->delete();
        DB::table('company_notes')->where('user_id', '=', $id)->delete();
        DB::table('meetings')->where('user_id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        $result['data']['user'] = User::find(intval($id));
        $result['status'] = 200;
        return response()->json($result);
    }

    public function search(Request $request)
    {
        $request = $request->json();

        $users = DB::table('users')->select('id', 'status', 'first_name', 'last_name', 'mobile_phone', 'office_phone', 'email');
        $count = 0;

        if ($request->has('status')){
            $users = $users->where('status', 'LIKE', $request->get('status'));
            $count++;
        }

        if ($request->has('email')){
            $users = $users->where('email', 'LIKE', "%".$request->get('email')."%");
            $count++;
        }

        if ($request->has('first_name')){
            $users = $users->where('first_name', 'LIKE', "%".$request->get('first_name')."%");
            $count++;
        }

        if ($request->has('last_name')){
            $users = $users->where('last_name', 'LIKE', "%".$request->get('last_name')."%");
            $count++;
        }

        if ($request->has('mobile_phone')){
            $users = $users->where('mobile_phone', 'LIKE', "%".$request->get('mobile_phone')."%");
            $count++;
        }

        if ($request->has('office_phone')){
            $users = $users->where('office_phone', 'LIKE', "%".$request->get('office_phone')."%");
            $count++;
        }

        if ($request->has('email')){
            $users = $users->where('email', 'LIKE', "%".$request->get('email')."%");
            $count++;
        }

        if ($count > 0){
            $result['data']['users'] = $users->get();
            $result['status'] = 200;
        }
        else{
            $result['status'] = 405;
        }

        return response()->json($result);
    }


    public function addNote(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('user_id') && $request->has('note')) {

                $note = DB::table('company_notes')->insert($request->all());

                if($note){
                    $result['status'] = 200;
                } else {
                    $result['status'] = 402;
                }


        } else {
            $result['status'] = 405;
        }

        return response()->json($result);
    }

    public function updateNote(Request $request)
    {
        $request = $request->json();

        if (!Company::exists('company_notes', ['id' => $request->get('updated_note_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('company_notes')->where('id', '=', $request->get('updated_note_id'));

        $params = [];

        if ($request->has('company_id')){
            $params['company_id'] = $request->get('company_id');
        }

        if ($request->has('brand')){
            $params['brand'] = $request->get('brand');
        }

        if ($request->has('model')){
            $params['model'] = $request->get('model');
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

    public function deleteNote($id)
    {
        if (!Company::exists('company_notes', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('company_notes')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function infoNote($id)
    {
        $result['data']['note'] = DB::table('company_notes')->leftJoin('companies', 'companies.id', '=', 'company_notes.company_id')->
        leftJoin('users', 'company_notes.user_id', '=', 'users.id')->
        select('company_notes.*', 'companies.name as company_name', 'users.first_name as users_first_name', 'users.last_name as users_last_name')->
        where('company_notes.id', '=', intval($id))->get();
        $result['status'] = 200;
        return response()->json($result);
    }


    public function addMeeting(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('user_id') && $request->has('worker_id') &&
            $request->has('meeting_date') && $request->has('organiser') && $request->has('note')) {

            $meeting = DB::table('meetings')->insert($request->all());

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

    public function updateMeeting(Request $request)
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

    public function deleteMeeting($id)
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

    public function infoMeeting($id)
    {
        $result['data']['meeting'] = DB::table('meetings')->leftJoin('companies', 'companies.id', '=', 'meetings.company_id')->
        leftJoin('users', 'meetings.user_id', '=', 'users.id')->
        leftJoin('workers', 'meetings.worker_id', '=', 'workers.id')->
        select('meetings.*', 'companies.name as company_name', 'workers.first_name as worker_first_name', 'workers.last_name as worker_last_name', 'users.first_name as users_first_name', 'users.last_name as users_last_name')->
        where('meetings.id', '=', intval($id))->get();
        $result['status'] = 200;
        return response()->json($result);
    }



}
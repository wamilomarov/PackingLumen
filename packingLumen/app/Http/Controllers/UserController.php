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
                $result['data']['user']['accesses'] = DB::table('users_accesses')->select('*')->where('user_id', '=', $user->id)->first();
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

    public function getUserStatuses()
    {
        $result['status'] = 200;
        $result['data']['statuses'] = DB::table('users_statuses')->select('id', 'name')->get();

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
                $user->status=$request->get('status');


                if($user->save()){

                    if ($request->get('status') != 1) {

                        $access = [
                            'user_id' => $user->id,
                            'company_read' => $request->get('company_read'),
                            'company_write' => $request->get('company_write'),
                            'machines_read' => $request->get('machines_read'),
                            'machines_write' => $request->get('machines_write'),
                            'workers_read' => $request->get('workers_read'),
                            'workers_write' => $request->get('workers_write'),
                            'notes_read' => $request->get('notes_read'),
                            'notes_write' => $request->get('notes_write'),
                            'meetings_read' => $request->get('meetings_read'),
                            'meetings_write' => $request->get('meetings_write')
                        ];

                        DB::table('users_accesses')->insert($access);
                    }
                    elseif ($request->get('status') == 1){
                        DB::table('users_accesses')->insert(
                            [
                                'user_id' => $user->id,
                                'company_read' => 1,
                                'company_write' => 1,
                                'machines_read' => 1,
                                'machines_write' => 1,
                                'workers_read' => 1,
                                'workers_write' => 1,
                                'notes_read' => 1,
                                'notes_write' => 1,
                                'meetings_read' => 1,
                                'meetings_write' => 1]
                        );
                    }
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

            if ($request->get('status') == 1){

                    $access = [
                        'company_read' => 1,
                        'company_write' => 1,
                        'machines_read' => 1,
                        'machines_write' => 1,
                        'workers_read' => 1,
                        'workers_write' => 1,
                        'notes_read' => 1,
                        'notes_write' => 1,
                        'meetings_read' => 1,
                        'meetings_write' => 1];
            }
            elseif ($request->get('status') == 2){
                $access = [
                    'company_read' => $request->get('company_read'),
                    'company_write' => $request->get('company_write'),
                    'machines_read' => $request->get('machines_read'),
                    'machines_write' => $request->get('machines_write'),
                    'workers_read' => $request->get('workers_read'),
                    'workers_write' => $request->get('workers_write'),
                    'notes_read' => $request->get('notes_read'),
                    'notes_write' => $request->get('notes_write'),
                    'meetings_read' => $request->get('meetings_read'),
                    'meetings_write' => $request->get('meetings_write')
                ];
            }
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
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->update($access);
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
        $result['data']['user'] = DB::table('users')->leftJoin('users_statuses','users_statuses.id', '=', 'users.status')
            ->leftJoin('users_accesses', 'users_accesses.user_id', '=', 'users.id')
            ->select('users_accesses.*', 'users.id', 'users_statuses.name as status_name', 'users_statuses.id as status', 'users.first_name', 'users.last_name', 'users.mobile_phone',
                'users.office_phone', 'users.email')
            ->where('users.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

    public function getUsersList()
    {
        $result['data']['users'] = DB::table('users')->leftJoin('users_statuses','users_statuses.id', '=', 'users.status')
            ->select('users.id', 'users_statuses.name as status_name', 'users.first_name', 'users.last_name', 'users.mobile_phone', 'users.office_phone', 'users.email')
            ->paginate(30);
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

    public function pass($pass)
    {
        return sha1($this->salt.$pass);
    }

}
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
        $result['data']['statuses'] = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Designer'],
            ['id' => 3, 'name' => 'Sales manager']];
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

                    $access = [];
                    if ($request->get('company_read') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>1]);
                    }
                    if ($request->get('company_write') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>2]);
                    }
                    if ($request->get('machines_read') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>3]);
                    }
                    if ($request->get('machines_write') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>4]);
                    }
                    if ($request->get('workers_read') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>5]);
                    }
                    if ($request->get('workers_write') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>6]);
                    }
                    if ($request->get('notes_read') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>7]);
                    }
                    if ($request->get('notes_write') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>8]);
                    }
                    if ($request->get('meetings_read') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>9]);
                    }
                    if ($request->get('meetings_write') == TRUE){
                        array_push($access, ['user_id' => $user->id, 'table' =>10]);
                    }

                    DB::table('users_accesses')->insert($access);
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


        $access = [];
        if ($request->get('company_read') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>1]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('company_write') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>2]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('machines_read') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>3]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('machines_write') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>4]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('workers_read') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>5]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('workers_write') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>6]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('notes_read') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>7]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('notes_write') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>8]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('meetings_read') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>9]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }
        if ($request->get('meetings_write') == TRUE){
            array_push($access, ['user_id' => $request->get('updated_user_id'), 'table' =>10]);
        }
        else {
            DB::table('users_accesses')->where('user_id', '=', $request->get('updated_user_id'))->where('table',  '=',  1)->delete();
        }

        DB::table('users_accesses')->insert($access);



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

}
<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:21
 */

namespace App\Http\Controllers;

use App\Worker;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;

class WorkerController extends Controller
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

    public function register(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('position') && $request->has('email') && $request->has('first_name') &&
            $request->has('last_name')) {
            if (Company::exists('workers', ['email' => $request->get('email')])){
                $result['status'] = 410;
            }
            else{
                $worker = new Worker($request->all());
                if($worker->save()){
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

        if (!Company::exists('workers', ['id' => $request->get('updated_worker_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('workers')->where('id', '=', $request->get('updated_worker_id'));

        $params = [];

        if ($request->has('position')){
            $params['position'] = $request->get('position');
        }

        if ($request->has('company_id')){
            $params['company_id'] = $request->get('company_id');
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
        if (!Company::exists('workers', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('workers')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        $result['data']['worker'] = DB::table('workers')->leftJoin('companies', 'companies.id', '=', 'workers.company_id')->select('workers.*', 'companies.name as company_name')->where('workers.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

    public function search(Request $request)
    {
        $request = $request->json();

        $workers = DB::table('workers')->leftJoin('companies', 'companies.id', '=', 'workers.company_id')->select('workers.*', 'companies.name as company_name');
        $count = 0;

        if ($request->has('company_name')){
            $workers = $workers->where('companies.name', 'LIKE', "%".$request->get('company_name'));
            $count++;
        }

        if ($request->has('email')){
            $workers = $workers->where('workers.email', 'LIKE', "%".$request->get('email')."%");
            $count++;
        }

        if ($request->has('first_name')){
            $workers = $workers->where('workers.first_name', 'LIKE', "%".$request->get('first_name')."%");
            $count++;
        }

        if ($request->has('last_name')){
            $workers = $workers->where('workers.last_name', 'LIKE', "%".$request->get('last_name')."%");
            $count++;
        }

        if ($request->has('mobile_phone')){
            $workers = $workers->where('workers.mobile_phone', 'LIKE', "%".$request->get('mobile_phone')."%");
            $count++;
        }

        if ($request->has('office_phone')){
            $workers = $workers->where('workers.office_phone', 'LIKE', "%".$request->get('office_phone')."%");
            $count++;
        }

        if ($request->has('position')){
            $workers = $workers->where('workers.position', 'LIKE', "%".$request->get('position')."%");
            $count++;
        }

        if ($count > 0){
            $result['data']['workers'] = $workers->get();
            $result['status'] = 200;
        }
        else{
            $result['status'] = 405;
        }

        return response()->json($result);
    }


    public function searchWorkerName(Request $request)
    {
        $q = $request->get('q');

        return DB::table('workers')->select('id', 'first_name', 'last_name')->where('first_name', 'LIKE', "%$q%")->orWhere('last_name', 'LIKE', "%$q%")->get();
    }
}
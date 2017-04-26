<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 22.04.2017
 * Time: 17:49
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;


class CompanyController extends Controller
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

    public function create(Request $request)
    {
        $request = $request->json();

        if ($request->has('name') && $request->has('email') && $request->has('address') &&
            $request->has('phone') && $request->has('website')){
            if (Company::exists('companies', ['email' => $request->get('email')])){
                $result['status'] = 410;
            }
            else {
                $company = new Company($request->all());
                $company->save();
                $result['status'] = 200;
            }
        }
        else {
            $result['status'] = 405;
        }
        return response()->json($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();

        if (!Company::exists('companies', ['id' => $request->get('updated_company_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('companies')->where('id', '=', $request->get('updated_company_id'));

        $params = [];

        if ($request->has('name')){
            $params['name'] = $request->get('name');
        }

        if ($request->has('email')){
            $params['email'] = $request->get('email');
        }

        if ($request->has('address')){
            $params['address'] = $request->get('address');
        }

        if ($request->has('website')){
            $params['website'] = $request->get('website');
        }

        if ($request->has('phone')){
            $params['phone'] = $request->get('phone');
        }

        if ($request->has('workers_count')){
            $params['workers_count'] = intval($request->get('workers_count'));
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
        if (!Company::exists('companies', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('companies')->where('id', '=', $id)->delete();
        DB::table('company_notes')->where('company_id', '=', $id)->delete();
        DB::table('workers')->where('company_id', '=', $id)->delete();
        DB::table('meetings')->where('company_id', '=', $id)->delete();
        DB::table('machines')->where('company_id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        $company = Company::find(intval($id));
        $result['data']['company'] = $company;
        $result['data']['notes'] = $company->getNotes();
        $result['data']['workers'] = $company->getWorkers();
        $result['status'] = 200;
        return response()->json($result);
    }


    public function getCompaniesList()
    {
        $response['status'] = 200;
        $response['data']['companies'] = DB::table('companies')->select('id', 'name')->paginate(1);
        return response()->json($response);
    }

    public function search(Request $request)
    {
        $request = $request->json();

        $companies = DB::table('companies')->select('id', 'name', 'phone', 'email', 'address', 'workers_count');
        $count = 0;


        if ($request->has('email')){
            $companies = $companies->where('email', 'LIKE', "%".$request->get('email')."%");
            $count++;
        }

        if ($request->has('name')){
            $companies = $companies->where('name', 'LIKE', "%".$request->get('name')."%");
            $count++;
        }

        if ($request->has('address')){
            $companies = $companies->where('address', 'LIKE', "%".$request->get('address')."%");
            $count++;
        }

        if ($request->has('phone')){
            $companies = $companies->where('phone', 'LIKE', "%".$request->get('phone')."%");
            $count++;
        }

        if ($request->has('website')){
            $companies = $companies->where('website', 'LIKE', "%".$request->get('website')."%");
            $count++;
        }

        if ($request->has('workers_count')){
            $companies = $companies->where('workers_count', '=', intval($request->get('email')));
            $count++;
        }

        if ($count > 0){
            $result['data']['companies'] = $companies->get();
            $result['status'] = 200;
        }
        else{
            $result['status'] = 405;
        }

        return response()->json($result);
    }


    public function registerWorker(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('position') && $request->has('email') && $request->has('first_name') &&
            $request->has('last_name') && $request->has('office_phone') && $request->has('mobile_phone')) {
            if (Company::exists('workers', ['email' => $request->get('email')])){
                $result['status'] = 410;
            }
            else{
                $worker = DB::table('workers')->insert($request->all());

                if($worker){
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

    public function updateWorker(Request $request)
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

    public function deleteWorker($id)
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

    public function infoWorker($id)
    {
        $result['data']['worker'] = DB::table('workers')->leftJoin('companies', 'companies.id', '=', 'workers.company_id')->select('workers.*', 'companies.name as company_name')->where('workers.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

    public function searchWorker(Request $request)
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


    public function addMachine(Request $request)
    {
        $request = $request->json();

        if ($request->has('company_id') && $request->has('brand') && $request->has('model') && $request->has('note')) {

                $machine = DB::table('machines')->insert($request->all());

                if($machine){
                    $result['status'] = 200;
                } else {
                    $result['status'] = 402;
                }

        } else {
            $result['status'] = 405;
        }

        return response()->json($result);
    }

    public function updateMachine(Request $request)
    {
        $request = $request->json();

        if (!Company::exists('machines', ['id' => $request->get('updated_machine_id')])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $update = DB::table('machines')->where('id', '=', $request->get('updated_machine_id'));

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

    public function deleteMachine($id)
    {
        if (!Company::exists('machines', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('machines')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function infoMachine($id)
    {
        $result['data']['machine'] = DB::table('machines')->leftJoin('companies', 'companies.id', '=', 'machines.company_id')->select('machines.*', 'companies.name')->where('machines.id', '=', intval($id))->get();
        $result['status'] = 200;
        return response()->json($result);
    }






}

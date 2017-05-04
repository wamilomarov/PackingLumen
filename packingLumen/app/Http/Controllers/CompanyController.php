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
        if(!Auth::user()->hasAccess(2)){
            $result['status'] = 403;
            return response()->json($result);
        }

        $request = $request->json();

        if ($request->has('name') && $request->has('email') && $request->has('phone')){
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
        if(!Auth::user()->hasAccess(2)){
            $result['status'] = 403;
            return response()->json($result);
        }

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
        if(!Auth::user()->hasAccess(2)){
            $result['status'] = 403;
            return response()->json($result);
        }

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
        if(!Auth::user()->hasAccess(1)){
            $result['status'] = 403;
            return response()->json($result);
        }
        $company = Company::find(intval($id));
        $result['data']['company'] = $company;

        if (Auth::user()->hasAccess(7)){
            $result['data']['notes'] = $company->getNotes();
        }
        else{
            $result['data']['notes'] = 403;
        }

        if (Auth::user()->hasAccess(5)){
            $result['data']['workers'] = $company->getWorkers();
        }
        else{
            $result['data']['workers'] = 403;
        }

        if (Auth::user()->hasAccess(3)){
            $result['data']['machines'] = $company->getMachines();
        }
        else{
            $result['data']['machines'] = 403;
        }

        $result['status'] = 200;
        return response()->json($result);
    }

    public function getCompaniesList()
    {
        if(!Auth::user()->hasAccess(1)){
            $result['status'] = 403;
            return response()->json($result);
        }
        $response['status'] = 200;
        $response['data']['companies'] = DB::table('companies')->select('*')->paginate(30);
        return response()->json($response);
    }

    public function search(Request $request)
    {
        if(!Auth::user()->hasAccess(1)){
            $result['status'] = 403;
            return response()->json($result);
        }
        $q = $request->input('q');
        $response['status'] = 200;
        $response['data']['companies'] = DB::table('companies')->select('*')->where('name', 'LIKE', "%$q%")->
            orWhere('address', 'LIKE', "%$q%")->orWhere('phone', 'LIKE', "%$q%")->orWhere('email', 'LIKE', "%$q%")
                ->orWhere('website', 'LIKE', "%$q%")->paginate(30);
        return response()->json($response);
    }

    public function searchByFields(Request $request)
    {

        if(!Auth::user()->hasAccess(1)){
            $result['status'] = 403;
            return response()->json($result);
        }

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

}

<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:21
 */

namespace App\Http\Controllers;

use App\Machine;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;

class MachineController extends Controller
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
        if(!Auth::user()->hasAccess('machines_write')){
            $result['status'] = 403;
            return response()->json($result);
        }

        $request = $request->json();

        if ($request->has('company_id') && $request->has('model') && $request->has('brand_id')) {

            $machine = new Machine($request->all());

            if($machine->save()){
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
        if(!Auth::user()->hasAccess('machines_write')){
            $result['status'] = 403;
            return response()->json($result);
        }

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

    public function delete($id)
    {
        if(!Auth::user()->hasAccess('machines_write')){
            $result['status'] = 403;
            return response()->json($result);
        }

        if (!Company::exists('machines', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('machines')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        if(!Auth::user()->hasAccess('machines_read')){
            $result['status'] = 403;
            return response()->json($result);
        }
        $result['data']['machine'] = DB::table('machines')->leftJoin('companies', 'companies.id', '=', 'machines.company_id')
            ->leftJoin('brands', 'brands.id', '=', 'machines.brand_id')->select('machines.*', 'companies.name', 'brands.name as brand_name')
            ->where('machines.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

    public function searchBrandName(Request $request)
    {

        $q = $request->get('q');

        $result['data']['brands'] = DB::table('brands')->select('id', 'name')->where('name', 'LIKE', "%$q%")->get();
        $result['status'] = 200;

        return response()->json($result);
    }

    public function addBrand(Request $request)
    {
        $request = $request->json();
        $insert = ['name' => $request->get('name')];
        DB::table('brands')->insert($insert);
        $result['status'] = 200;

        return response()->json($result);
    }

    public function getBrands()
    {
        $result['data']['brands'] = DB::table('brands')->select('id', 'name')->get();
        $result['status'] = 200;

        return response()->json($result);
    }

    public function deleteBrand($id)
    {
        DB::table('brands')->where('id', '=', intval($id))->delete();
        $result['status'] = 200;
        return response()->json($result);
    }

}
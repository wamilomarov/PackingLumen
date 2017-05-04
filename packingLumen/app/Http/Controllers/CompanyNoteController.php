<?php
/**
 * Created by PhpStorm.
 * User: Shamil
 * Date: 28.04.2017
 * Time: 20:20
 */

namespace App\Http\Controllers;

use App\CompanyNote;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Auth;

class CompanyNoteController extends Controller
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
        if(!Auth::user()->hasAccess(8)){
            $result['status'] = 403;
            return response()->json($result);
        }

        $request = $request->json();

        if ($request->has('company_id') && $request->has('user_id') && $request->has('note')) {

            $note = new CompanyNote($request->all());
            if($note->save()){
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
        if(!Auth::user()->hasAccess(8)){
            $result['status'] = 403;
            return response()->json($result);
        }
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

    public function delete($id)
    {
        if(!Auth::user()->hasAccess(8)){
            $result['status'] = 403;
            return response()->json($result);
        }

        if (!Company::exists('company_notes', ['id' => intval($id)])){
            $result['status'] = 409;
            return response()->json($result);
        }
        $id = intval($id);
        $result['status'] = 200;
        DB::table('company_notes')->where('id', '=', $id)->delete();

        return response()->json($result);
    }

    public function info($id)
    {
        if(!Auth::user()->hasAccess(7)){
            $result['status'] = 403;
            return response()->json($result);
        }

        $result['data']['note'] = DB::table('company_notes')->leftJoin('companies', 'companies.id', '=', 'company_notes.company_id')->
        leftJoin('users', 'company_notes.user_id', '=', 'users.id')->
        select('company_notes.*', 'companies.name as company_name', 'users.first_name as user_first_name', 'users.last_name as user_last_name')->
        where('company_notes.id', '=', intval($id))->first();
        $result['status'] = 200;
        return response()->json($result);
    }

}
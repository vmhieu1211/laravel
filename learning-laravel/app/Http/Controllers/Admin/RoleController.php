<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Action;
use App\Models\Module;
use App\Models\Role;
use App\Http\Requests\RoleStorePost;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RoleUpdatePost;

class RoleController extends Controller
{
    public function index()
    {
        // man hanh danh sach vai tro
        $roles = Role::paginate(10);    
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        /*
            [
                ['module_id' => 1, 'module_name' => 'QLHV', 'actions' => [[],[],[]]],
                [],
            ];
        */
        $actions = Action::all()->toArray();
        $modules = Module::all()->toArray();

        return view('admin.roles.create',[
            'modules' => $modules,
            'countActions'=>count($actions),
            'actions'=>$actions
        ]);
    }
    
    public function store(RoleStorePost $request)
    {
        $data = $request->all();
        // insert roles table
        $role = new Role;
        $role->name = $request->name;
        $role->description = $request->description;
        $role->status = ACTIVE_STATUS;
        // // save data 
        $role->save();
        $roleId = $role->id; // lay ra duoc id vua insert vao database
        
        // insert data to action_module table
        $permissions = $request->permissions;
        foreach($permissions as $module_id => $actions){
            foreach($actions as $actions_id){
                $arrIdActionModule[] = DB::table('action_module')->insertGetId([
                    'action_id' => $actions_id,
                    'module_id' => $module_id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            }
        }
        // dd($arrIdActionModule);  

        // insert dât to role_action_module table
        if(!empty($arrIdActionModule) && $roleId > 0 ){
            foreach($arrIdActionModule as $id ){
                DB::table('role_action_module')->insert([
                    'role_id' =>$roleId,
                    'action_module_id' => $id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            }            
        }  
        return redirect()->back()->with(CREATE_ACTION_SUCCESS_LABEL, CREATE_ACTION_SUCCESS);
    }

    public function edit(Request $request)
    {
        $roleId = $request->id;
        $roleId = is_numeric($roleId) ? $roleId : 0;

        $infoRole = Role::find($roleId);
        if(!empty($infoRole)){
            $actions = Action::all()->toArray();
            $modules = Module::all()->toArray();

            $moduleActionId = DB::table('roles AS r')->select('ac.action_id', 'ac.module_id')
                ->join('role_action_module AS ram', 'r.id', '=', 'ram.role_id')
                ->join('action_module AS ac', 'ram.action_module_id', '=', 'ac.id')
                ->where('r.id', $roleId)
                ->get();
            $arrModuleActionId = json_decode(json_encode( $moduleActionId), true);

            return view('admin.roles.edit',compact('infoRole','actions','modules','arrModuleActionId'));
        } else {
            return view('admin.errors.404');
        }
    }
    
    public function update(RoleUpdatePost $request)
    {
        
    }
}

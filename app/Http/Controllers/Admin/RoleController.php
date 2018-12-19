<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    /** 获取角色列表
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(){
        $data = Role::all();
        return code_response(10,'获取角色列表成功',200,$data);
    }

    /** 添加角色
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        //1.角色名称不可重复
        $role = Role::where('name',$request->input('role_name'))->first();
        if(!$role){
            return code_response(20001,'角色名称已存在');
        }

        //2.新增角色
        $role=new Role();
        $role->name=$request->input('role_name');
        $msg=$role->save();

        if($msg){
            return code_response(10,'角色添加成功');
        }else{
            return code_response(20002,'角色添加失败');
        }
    }

    /** 修改角色
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $role_id = $request->input('role_id');
        //1.角色名称不可重复
        $role_name = Role::where('name',$request->input('role_name'))->first();
        if(!$role_name){
            return code_response(20001,'角色名称已存在');
        }

        //2.修改角色
        $role = Role::where('id',$role_id)->first();
        $role->name=$request->input('role_name');
        $msg=$role->save();

        if($msg){
            return code_response(10,'角色修改成功');
        }else{
            return code_response(20003,'角色修改失败');
        }
    }

    /** 删除角色
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destory(Request $request)
    {
        $role_id = $request->input('role_id');

        //判断该角色下，是否有用户存在
        $role_user = RoleUser::where('role_id',$role_id)->first();
        if($role_user){
            return code_response(20004,'该角色下有用户存在，不可删除');
        }

        //删除角色
        $role = Role::where('id',$role_id)->delete();
        if(!$role){
            return code_response(20005,'角色删除失败');
        }

        //删除角色权限
        $permission_role = PermissionRole::where('role_id',$role_id)->first();
        if($permission_role){
            $permiss_role = PermissionRole::where('role_id',$role_id)->delete();
            if(!$permiss_role){
                return code_response(20006,'角色权限删除失败');
            }
        }

        return code_response(10,'角色删除失败');
    }

}
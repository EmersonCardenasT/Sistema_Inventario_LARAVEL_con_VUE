<?php

namespace App\Http\Controllers\RolePermisos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request -> get("search");
        $roles = Role::where("name", "ilike", "%".$search."%")->orderBy("id", "desc")->get();

        // Retornamos la respuesta en formato JSON
        return response()->json([
            "roles" => $roles -> map(function($role) {
                return [
                    "id" => $role -> id,
                    "name" => $role -> name,
                    "created_at" => $role -> created_at -> format("Y/m/d h:i:s"),
                    "permissions" => $role -> permissions -> map(function($permission) {
                        return [
                            "id" => $permission -> id,
                            "name" => $permission -> name,
                        ];
                    }),
                    "permissions_pluck" => $role -> permissions -> pluck("name"),
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $exist_role = Role::where("name", $request->name)->first();
        if($exist_role){
            return response() -> json([
                "message_text" => "El rol ya existe, intente uno nuevo",
                "message" => 403,
            ]);
        }
        
        $role = Role::create([
            "name" => $request->name,
            "guard_name" => "api",
        ]);

        // ENLAZARLO CON LOS PERMISOS que tenga
        $permissions = $request -> permissions;
        foreach($permissions as $key => $permission){
            $role -> givePermissionTo($permission);
        }

        return response() -> json([
            "message" => 200,
            "message_text" => "Rol creado con exito",
            "role"  => [
                "id" => $role -> id,
                "name" => $role -> name,
                "created_at" => $role -> created_at -> format("Y/m/d h:i:s"),
                "permissions" => $role -> permissions -> map(function($permission) {
                    return [
                        "id" => $permission -> id,
                        "name" => $permission -> name,
                    ];
                }),
                "permissions_pluck" => $role -> permissions -> pluck("name"),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exist_role = Role::where("name", $request->name)->where("id", "<>", $id)->first();
        if($exist_role){
            return response() -> json([
                "message_text" => "El rol ya existe, intente uno nuevo",
                "message" => 403,
            ]);
        }
        
        $role = Role::findOrFail($id);

        $role -> update([
            "name" => $request->name,
        ]);

        // ENLAZARLO CON LOS PERMISOS que tenga
        $permissions = $request -> permissions;
        $role -> syncPermissions($permissions);

        return response() -> json([
            "message" => 200,
            "message_text" => "Rol creado con exito",
            "role"  => [
                "id" => $role -> id,
                "name" => $role -> name,
                "created_at" => $role -> created_at -> format("Y/m/d h:i:s"),
                "permissions" => $role -> permissions -> map(function($permission) {
                    return [
                        "id" => $permission -> id,
                        "name" => $permission -> name,
                    ];
                }),
                "permissions_pluck" => $role -> permissions -> pluck("name"),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role -> delete();

        return response() -> json([
            "message" => 200,
            "message_text" => "Rol eliminado con exito",
        ]);
    }
}

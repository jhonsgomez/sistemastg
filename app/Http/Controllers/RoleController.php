<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        try {
            return view('roles.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getData()
    {
        $roles = Role::query()->where('deleted_at', '=', NULL);

        return DataTables::of($roles)
            ->addColumn('actions', function ($role) {
                $buttons = '';

                if (auth()->user()->can('edit_role')) {
                    $buttons .= '<button onclick="editRole(' . $role->id . ')" class="bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg mr-2">
                        <i class="fas fa-edit"></i> Editar
                    </button>';
                }

                if (auth()->user()->can('delete_role')) {
                    $buttons .= '<button onclick="deleteRole(' . $role->id . ')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg mr-2">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>';
                }

                $buttons .= '<button onclick="viewPermissions(' . $role->id . ')" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">
                    <i class="fas fa-key"></i> Permisos
                </button>';

                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'description' => 'required'
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Este nombre de rol ya existe',
            'description.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => "web",
            'description' => $request->description
        ]);

        return response()->json(['success' => 'Rol creado exitosamente']);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id,
            'description' => 'required'
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Este nombre de rol ya existe',
            'description.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::findOrFail($id);
        $role->update($request->all());

        return response()->json(['success' => 'Rol actualizado exitosamente']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->deleted_at = now();
        $role->save();

        return response()->json(['success' => 'Rol eliminado exitosamente']);
    }

    public function getPermissions($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::where('deleted_at', '=', NULL)->orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            if (count($parts) <= 1) {
                return 'otros';
            }

            $group = end($parts);

            $groupMappings = [
                'user' => 'users',
                'users' => 'users',
                'role' => 'roles',
                'roles' => 'roles',
                'permission' => 'permissions',
                'permissions' => 'permissions',
            ];

            return $groupMappings[$group] ?? $group;
        })->map(function ($group) {
            return $group->sortBy('name');
        });

        $groupedPermissions = $groupedPermissions->sortKeys();

        return view('roles.permissions', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::whereIn('id', $request->input('permissions', []))->get();

        $role->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados exitosamente'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            return view('permissions.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getData()
    {
        $permissions = Permission::query()->where('deleted_at', '=', NULL);

        return DataTables::of($permissions)
            ->addColumn('actions', function ($permission) {
                $buttons = '';

                if (auth()->user()->can('edit_permission')) {
                    $buttons .= '<button onclick="editPermission(' . $permission->id . ')" class="bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg mr-2">
                        <i class="fas fa-edit"></i> Editar
                    </button>';
                }

                if (auth()->user()->can('delete_permission')) {
                    $buttons .= '<button onclick="deletePermission(' . $permission->id . ')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>';
                }

                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
            'description' => 'required'
        ], [
            'name.required' => 'El nombre del permiso es obligatorio',
            'name.unique' => 'Este nombre de permiso ya existe',
            'description.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Permission::create([
            'name' => $request->name,
            'guard_name' => "web",
            'description' => $request->description
        ]);

        return response()->json(['success' => 'Permiso creado exitosamente']);
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $id,
            'description' => 'required'
        ], [
            'name.required' => 'El nombre del permiso es obligatorio',
            'name.unique' => 'Este nombre de permiso ya existe',
            'description.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission = Permission::findOrFail($id);
        $permission->update($request->all());

        return response()->json(['success' => 'Permiso actualizado exitosamente']);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->deleted_at = now();
        $permission->save();

        return response()->json(['success' => 'Permiso eliminado exitosamente']);
    }
}

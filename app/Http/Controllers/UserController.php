<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\CorreoInstitucional;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        try {
            return view('users.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getData()
    {
        $users = User::query()->where('id', '!=', 1)->where('id', '!=', auth()->user()->id)->where('deleted_at', '=', NULL);

        return DataTables::of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles;
            })
            ->addColumn('actions', function ($user) {
                $buttons = '<div class="flex items-center justify-center container-actions">';

                if (auth()->user()->can('edit_user')) {
                    $buttons .= '<button onclick="editUser(' . $user->id . ')" class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg mr-2">
                        <i class="fas fa-edit"></i>
                    </button>';
                }

                if (auth()->user()->can('assign_roles')) {
                    $buttons .= '<button onclick="viewRoles(' . $user->id . ')" class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg">
                        <i class="fas fa-user-tag"></i>
                    </button>';
                }

                if (auth()->user()->can('delete_user')) {
                    $buttons .= '<button onclick="deleteUser(' . $user->id . ')" class="btn-action shadow bg-red-500 hover:bg-red-800 text-white px-3 py-1 rounded-lg mr-2 btn-delete">
                        <i class="fas fa-trash"></i>
                    </button>';
                }

                $buttons .= '</div>';

                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email', new CorreoInstitucional()],
            'password' => 'required|min:8'
        ], [
            'name.required' => 'El nombre de usuario es obligatorio',
            'name.unique' => 'Este nombre de usuario ya existe',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Ya existe un usuario con ese correo electrónico',
            'email.email' => 'El email es incorrecto'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['success' => 'Usuario creado exitosamente']);
    }

    public function edit($id)
    {
        $user = User::with(['tipo_documento', 'nivel'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email,' . $id . '', new CorreoInstitucional()],
            'password' => 'nullable|min:8'
        ], [
            'name.required' => 'El nombre de usuario es obligatorio',
            'name.unique' => 'Este nombre de usuario ya existe',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email es incorrecto',
            'email.unique' => 'Ya existe un usuario con ese correo electrónico.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->updated_at = now();
        $user->save();

        return response()->json(['success' => 'Usuario actualizado exitosamente']);
    }

    public function getRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('name', '!=', 'super_admin')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    public function updateRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('id', $request->input('roles', []))->get();

        $user->syncRoles($roles);

        return response()->json(['success' => 'Roles actualizados exitosamente']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->deleted_at = now();
        $user->save();

        return response()->json(['success' => 'Usuario eliminado exitosamente']);
    }
}

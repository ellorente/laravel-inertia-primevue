<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('User/Index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Obtener parámetros
        $page = intval(json_decode($request->page ?? 1)) ?: 1;
        $sortField = $request->sortField ?? 'id';
        $sortOrder = $request->sortOrder == 1 ? 'asc' : 'desc';
        $filters = $request->filter ?? [];

        $query = User::with('roles');
        // Filtros individuales
        if (!empty($filters)) {
            foreach ($filters as $field => $filter) {
                if (!empty($filter['value'])) {
                    $query->where($field, 'LIKE', '%' . $filter['value'] . '%');
                }
            }
        }

        // Ordenar
        if ($sortField) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginación
        $users = $query->paginate(10, ['*'], 'page', $page);
                
        return response()->json([
            
            'data' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('User/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'status' => 'required|boolean',
            'password' => 'required', 
            'role' => 'required', 
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);
    
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'status' => 'required|boolean',
            'roles' => 'required|string|exists:roles,name',
        ]);
    
        $user->update($data);
        $user->syncRoles([$data['roles']]);

        //$user->syncRoles([]);
        //$user->assignRole($request->role);
    
        return redirect()->back()->with('success', 'Contacto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = User::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }
}

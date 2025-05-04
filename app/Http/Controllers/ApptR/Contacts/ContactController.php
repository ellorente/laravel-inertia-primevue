<?php

namespace App\Http\Controllers\ApptR\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ApptR\contacts\Contact;
use App\Models\ApptR\contacts\DocumentType;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        return Inertia::render('ApptR/contacts/Index');
    }

    public function showContacts(Request $request)
    {
        // Obtener parámetros
        $page = json_decode($request->page) ?? 1;
        $sortField = $request->sortField ?? 'id';
        $sortOrder = $request->sortOrder == 1 ? 'asc' : 'desc';
        $filters = $request->filter ?? [];

        $query = Contact::with('documentType');

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
        $contacts = $query->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'data' => $contacts
        ]);
    }

    public function create()
    {
        return Inertia::render('ApptR/contacts/Create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'sometimes|string',
            'document_type_id' => 'sometimes|exists:appt_document_types,id',
            'birthdate' => 'sometimes|date',
            'full_name' => 'sometimes|string',
            'phones' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'eps' => 'nullable|string'
        ]);
    
        Contact::create($validated);
    
        return redirect()->route('contacts.index')->with('success', 'Contacto creado exitosamente.');
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'document_number' => 'sometimes|string',
            'document_type_id' => 'sometimes|exists:appt_document_types,id',
            'birthdate' => 'sometimes|date',
            'full_name' => 'sometimes|string',
            'phones' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'eps' => 'nullable|string'
        ]);
    
        $contact = Contact::findOrFail($id); // ← Esto es muy importante
    
        $contact->update($data);
    
        return redirect()->back()->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }
}

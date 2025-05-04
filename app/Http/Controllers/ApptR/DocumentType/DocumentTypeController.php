<?php

namespace App\Http\Controllers\Apptr\DocumentType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ApptR\contacts\DocumentType;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentType::all();
        return response()->json(['data' => $documentTypes]);
    }
}

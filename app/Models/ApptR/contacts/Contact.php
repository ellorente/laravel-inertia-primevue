<?php

namespace App\Models\apptr\contacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'appt_contacts';

    protected $fillable = [
        'document_number',
        'document_type_id',
        'birthdate',
        'full_name',
        'phones',
        'whatsapp',
        'address',
        'email',
        'eps'
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}

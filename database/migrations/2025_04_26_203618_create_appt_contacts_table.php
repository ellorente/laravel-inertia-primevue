<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appt_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->unsignedBigInteger('document_type_id');
            $table->date('birthdate');
            $table->string('full_name');
            $table->string('phones')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('eps')->nullable();
            $table->timestamps();

            $table->foreign('document_type_id')->references('id')->on('appt_document_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appt_contacts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('type', ['annuel', 'exceptionnel', 'maternite', 'maladie', 'autre']);
            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->integer('duree');
            $table->integer('solde_restant');
            $table->text('commentaire_manager')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('utilisateurs');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conges');
    }
};
<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('statut', ['a_faire', 'en_cours', 'terminee', 'annulee'])->default('a_faire');
            $table->date('date_echeance');
            $table->foreignId('created_by')->constrained('utilisateurs');
            $table->foreignId('assigned_to')->constrained('employes');
            $table->integer('priorite')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taches');
    }
};
<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->date('date_naissance');
            $table->string('poste');
            $table->string('departement');
            $table->date('date_embauche');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->string('numero_employe')->unique();
            $table->integer('solde_conge')->default(25);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employes');
    }
};
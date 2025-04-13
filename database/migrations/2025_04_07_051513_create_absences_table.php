<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('raison', ['maladie', 'accident', 'familiale', 'autre']);
            $table->string('justificatif')->nullable();
            $table->boolean('est_justifie')->default(false);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absences');
    }
};
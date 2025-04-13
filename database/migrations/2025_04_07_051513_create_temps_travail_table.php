<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temps_travail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->float('total_heures');
            $table->boolean('est_valide')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('utilisateurs');
            $table->timestamp('validated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temps_travail');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('employes')->onDelete('cascade');
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date_affectation');
            $table->timestamps();
            
            $table->unique(['manager_id', 'employe_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipes');
    }
};
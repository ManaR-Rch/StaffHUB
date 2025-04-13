<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->string('mois');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2);
            $table->enum('statut', ['brouillon', 'valide', 'paye'])->default('brouillon');
            $table->string('fichier_pdf')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('utilisateurs');
            $table->foreignId('validated_by')->nullable()->constrained('utilisateurs');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paies');
    }
};
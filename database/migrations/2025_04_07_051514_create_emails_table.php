<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('sujet');
            $table->text('message');
            $table->string('type');
            $table->foreignId('from_user_id')->constrained('utilisateurs');
            $table->foreignId('to_user_id')->constrained('utilisateurs');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
    }
};
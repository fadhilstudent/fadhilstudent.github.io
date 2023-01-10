<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontrak_induks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khs_id')->nullable();
            $table->string('nomor_kontrak_induk');
            $table->date('tanggal_kontrak_induk');
            // $table->foreignId('khs_id')->nullable();            
            $table->foreignId('vendor_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kontrak_induks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->text('symptoms')->nullable();
            $table->text('medication')->nullable();
            $table->boolean('visited_clinic')->default(false);
            $table->boolean('symptoms_present')->default(false);
            $table->text('clinic_notes')->nullable(); // keep this for remarks
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'symptoms',
                'medication',
                'visited_clinic',
                'symptoms_present',
                'clinic_notes',
            ]);
        });
    }
};

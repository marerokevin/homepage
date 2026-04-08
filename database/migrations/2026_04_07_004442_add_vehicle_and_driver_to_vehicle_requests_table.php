<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {

            if (!Schema::hasColumn('vehicle_requests', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('vehicle_requests', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['vehicle_id', 'driver_id']);
        });
    }
};

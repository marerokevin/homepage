<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup');
            $table->string('destination');
            $table->string('vehicle');
            $table->string('plate');
            $table->date('trip_date');
            $table->time('departure');
            $table->time('eta')->nullable();
            $table->time('return_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'pickup', 'destination', 'vehicle',
                'plate', 'trip_date', 'departure', 'eta', 'return_time'
            ]);
        });
    }
};

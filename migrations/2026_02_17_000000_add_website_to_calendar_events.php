<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return [
    'up' => function () {
        // If your install uses a different table name, update 'events' accordingly
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'website')) {
                $table->string('website')->nullable()->after('description');
            }
        });
    },
    'down' => function () {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'website')) {
                $table->dropColumn('website');
            }
        });
    }
];
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return [
    'up' => function () {
        if (Schema::hasTable('he_events') && !Schema::hasColumn('events', 'website')) {
            Schema::table('he_events', function (Blueprint $table) {
                $table->string('website')->nullable()->after('description');
            });
        }
    },

    'down' => function () {
        if (Schema::hasTable('he_events') && Schema::hasColumn('events', 'website')) {
            Schema::table('he_events', function (Blueprint $table) {
                $table->dropColumn('website');
            });
        }
    },
];
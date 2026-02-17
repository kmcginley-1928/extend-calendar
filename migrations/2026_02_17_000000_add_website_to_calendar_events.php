<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('events') && !$schema->hasColumn('events', 'website')) {
            $schema->table('events', function (Blueprint $table) {
                $table->string('website')->nullable()->after('description');
            });
        }
    },
    'down' => function (Builder $schema) {
        if ($schema->hasTable('events') && $schema->hasColumn('events', 'website')) {
            $schema->table('events', function (Blueprint $table) {
                $table->dropColumn('website');
            });
        }
    },
];
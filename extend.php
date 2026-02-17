<?php

namespace Keith\ExtendCalendar;

use Flarum\Extend;
use Webbinaro\AdvCalendar\Event;
use Webbinaro\AdvCalendar\Api\Serializers\EventSerializer;
use Illuminate\Database\Eloquent\Model;
use Flarum\User\User;
use Flarum\Event\Saving;

return [

    // 1. Add "website" to the API serializer
    (new Extend\ApiSerializer(EventSerializer::class))
        ->attribute('website', function ($serializer, Event $event, array $attributes) {
            return $event->website ?? null;
        }),

    // 2. Cast it on the Eloquent model
    (new Extend\Model(Event::class))
        ->cast('website', 'string'),

    // 3. Save "website" using Flarum 1.8's Saving event
    (new Extend\Event())
        ->listen(Saving::class, function (Saving $event) {
            $model = $event->model;
            $actor = $event->actor;
            $data  = $event->data;

            // Only handle calendar events
            if (!($model instanceof Event)) {
                return;
            }

            $attrs = $data['attributes'] ?? [];

            if (array_key_exists('website', $attrs)) {
                $value = trim((string) $attrs['website']);
                $model->website = $value !== '' ? $value : null;
            }
        }),

    // 4. FRONTEND JS will be added back later once build is correct
    // (new Extend\Frontend('forum'))->js(__DIR__.'/js/dist/forum.js'),
];
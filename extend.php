<?php

namespace Keith\ExtendCalendar;

use Flarum\Extend;
use Webbinaro\AdvCalendar\Event;
use Webbinaro\AdvCalendar\Api\Serializers\EventSerializer;
use Flarum\Event\Saving;

return [
    // API read
    (new Extend\ApiSerializer(EventSerializer::class))
        ->attribute('website', fn ($serializer, Event $event) => $event->website ?? null),

    // Cast attribute
    (new Extend\Model(Event::class))->cast('website', 'string'),

    // Save via Flarum 1.8-compatible event
    (new Extend\Event())->listen(Saving::class, function (Saving $e) {
        if (!$e->model instanceof Event) return;
        $attrs = $e->data['attributes'] ?? [];
        if (array_key_exists('website', $attrs)) {
            $val = trim((string) $attrs['website']);
            $e->model->website = $val !== '' ? $val : null;
        }
    }),

    // Forum JS only (re-enable once your js/dist/forum.js is truly ready)
    // (new Extend\Frontend('forum'))->js(__DIR__.'/js/dist/forum.js'),
];
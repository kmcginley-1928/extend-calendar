<?php

namespace Keith\ExtendCalendar;

use Flarum\Extend;

use Webbinaro\AdvCalendar\Event;
use Webbinaro\AdvCalendar\Api\Serializers\EventSerializer;
use Webbinaro\AdvCalendar\Api\Controllers\EventsCreateController;
use Webbinaro\AdvCalendar\Api\Controllers\EventsUpdateController;

return [

    // Expose website on the API
    (new Extend\ApiSerializer(EventSerializer::class))
        ->attribute('website', function ($serializer, Event $event, array $attributes) {
            return $event->website ?? null;
        }),

    // Cast on model
    (new Extend\Model(Event::class))
        ->cast([
            'website' => 'string',
        ]),

    // Save website on CREATE
    (new Extend\ApiController(EventsCreateController::class))
        ->prepareData(function ($controller, $request, $document) {
            $data  = (array) $request->getParsedBody();
            $attrs = $data['data']['attributes'] ?? [];

            if (array_key_exists('website', $attrs)) {
                $request = $request->withAttribute('calendar.website', trim((string)$attrs['website']) ?: null);
            }
        })
        ->data(function ($controller, $event, $request) {
            $website = $request->getAttribute('calendar.website');

            if ($website !== null && $event instanceof Event) {
                $event->website = $website;
                $event->save();
            }

            return $event;
        }),

    // Save website on UPDATE
    (new Extend\ApiController(EventsUpdateController::class))
        ->prepareData(function ($controller, $request, $document) {
            $data  = (array) $request->getParsedBody();
            $attrs = $data['data']['attributes'] ?? [];

            if (array_key_exists('website', $attrs)) {
                $request = $request->withAttribute('calendar.website', trim((string)$attrs['website']) ?: null);
            }
        })
        ->data(function ($controller, $event, $request) {
            $website = $request->getAttribute('calendar.website');

            if ($website !== null && $event instanceof Event) {
                $event->website = $website;
                $event->save();
            }

            return $event;
        }),

];
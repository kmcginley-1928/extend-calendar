<?php

namespace Keith\ExtendCalendar;

use Flarum\Extend;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\Document;

// Calendar plugin classes (verified)
use Webbinaro\AdvCalendar\Event;
use Webbinaro\AdvCalendar\Api\Serializers\EventSerializer;
use Webbinaro\AdvCalendar\Api\Controllers\EventsCreateController;
use Webbinaro\AdvCalendar\Api\Controllers\EventsUpdateController;

return [
    // 1) Expose website on the Event JSON:API payload
    (new Extend\ApiSerializer(EventSerializer::class))
        ->attribute('website', function ($serializer, Event $event, array $attributes) {
            return $event->website ?? null;
        }),

    // 2) Let Eloquent treat it as string
    (new Extend\Model(Event::class))
        ->cast([
            'website' => 'string',
        ]),

    // 3) Accept website on CREATE
    (new Extend\ApiController(EventsCreateController::class))
        ->prepareData(function ($controller, Request $request, Document $document) {
            // Stash the attribute on the request so we can apply it post-create
            $data  = (array) $request->getParsedBody();
            $attrs = $data['data']['attributes'] ?? [];
            if (array_key_exists('website', $attrs)) {
                $request = $request->withAttribute('calendar.website', trim((string) $attrs['website']) ?: null);
            }
        })
        ->data(function ($controller, $data, Request $request) {
            // $data is the Event model returned by the controller
            $website = $request->getAttribute('calendar.website');
            if ($website !== null && $data instanceof Event) {
                $data->website = $website;
                $data->save();
            }
            return $data;
        }),

    // 4) Accept website on UPDATE
    (new Extend\ApiController(EventsUpdateController::class))
        ->prepareData(function ($controller, Request $request, Document $document) {
            $data  = (array) $request->getParsedBody();
            $attrs = $data['data']['attributes'] ?? [];
            if (array_key_exists('website', $attrs)) {
                // Most update controllers load the Event internally and return it.
                // We’ll apply after the controller returns too, to be safe:
                $request = $request->withAttribute('calendar.website', trim((string) $attrs['website']) ?: null);
            }
        })
        ->data(function ($controller, $data, Request $request) {
            $website = $request->getAttribute('calendar.website');
            if ($website !== null && $data instanceof Event) {
                $data->website = $website;
                $data->save();
            }
            return $data;
        }),
];
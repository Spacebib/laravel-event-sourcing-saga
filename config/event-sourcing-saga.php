<?php
return [
    'rollback_tries' => env('SAGA_TRIES_BEFORE_ROLLBACK', 3),

    'saga_stored_event_repository' => Spacebib\Saga\SagaEloquentStoredEventRepository::class,

    'saga_stored_event_model' => Spacebib\Saga\EloquentSagaStoredEvent::class,
];

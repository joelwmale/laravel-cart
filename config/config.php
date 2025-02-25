<?php

return [
    /*
     * ---------------------------------------------------------------
     * Formatting
     * ---------------------------------------------------------------
     */
    'format_numbers' => env('LARAVEL_CART_FORMAT_VALUES', false),

    'decimals' => env('LARAVEL_CART_DECIMALS', 0),

    'round_mode' => env('LARAVEL_CART_ROUND_MODE', 'down'),

    /*
     * ---------------------------------------------------------------
     * Persistence
     * ---------------------------------------------------------------
     */
    'storage' => null,
    'storage_id' => null,
    'storage_items' => null,
    'storage_conditions' => null,

    /*
     * ---------------------------------------------------------------
     * Events
     * ---------------------------------------------------------------
     */
    'events' => null,
];

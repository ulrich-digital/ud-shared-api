<?php
defined('ABSPATH') || exit;

require_once __DIR__ . '/tag-api.php';

add_action('rest_api_init', function () {
    register_rest_route('ud-shared/v1', '/tags', [
        'methods'  => 'GET',
        'callback' => 'ud_shared_get_tags',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ud-shared/v1', '/tags', [
        'methods'  => 'POST',
        'callback' => 'ud_shared_add_tag',
        'permission_callback' => 'ud_shared_can_edit',
        'args' => [
            'name' => [
                'required' => true,
                'type'     => 'string',
            ],
        ],
    ]);
});

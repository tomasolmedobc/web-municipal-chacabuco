<?php

return [
    'connection' => env('WP_CONNECTION', 'wordpress'),
    'table_prefix' => env('WP_TABLE_PREFIX', 'wp_'),
    'post_type' => env('WP_POST_TYPE', 'post'),
    'post_statuses' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('WP_POST_STATUSES', 'publish'))
    ))),

    'media' => [
        'source_uploads_path' => env('WP_MEDIA_SOURCE_UPLOADS_PATH', '/wp-content/uploads'),
        'target_base_url' => env('WP_MEDIA_TARGET_BASE_URL', '/images'),
        'target_suffix' => env('WP_MEDIA_TARGET_SUFFIX', '_resultado'),
        'target_extension' => env('WP_MEDIA_TARGET_EXTENSION', 'webp'),
    ],
];

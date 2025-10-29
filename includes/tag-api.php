<?php
defined('ABSPATH') || exit;

function ud_shared_get_tags() {
    $tags = get_option('ud_shared_tags', []);
    $mode = get_option('ud_shared_tags_mode', 'ignore');
    $blacklist = get_option('ud_shared_tags_blacklist', []);
    $blacklist = is_array($blacklist) ? $blacklist : array_map('trim', explode(',', $blacklist));

    $filtered = array_values(array_filter($tags, function ($tag) use ($blacklist) {
        return !in_array($tag, $blacklist, true);
    }));

    if ($mode === 'delete') {
        update_option('ud_shared_tags', $filtered);
        return $filtered;
    }

    if ($mode === 'ignore') {
        return $filtered;
    }

    return $tags;
}



function ud_shared_add_tag($request) {
    $new_tag = sanitize_text_field($request->get_param('name'));

    if (empty($new_tag)) {
        return new WP_Error('empty_tag', 'Tag darf nicht leer sein.', ['status' => 400]);
    }

    $tags = get_option('ud_shared_tags', []);
    if (!in_array($new_tag, $tags, true)) {
        $tags[] = $new_tag;
        update_option('ud_shared_tags', $tags);
    }

    return $tags;
}

function ud_shared_can_edit() {
    return current_user_can('edit_posts');
}

<?php
// admin-tag-filter.php

defined('ABSPATH') || exit;

// Optionen registrieren
function ud_shared_register_settings() {
    register_setting('ud_shared_settings_group', 'ud_shared_tags_blacklist');
    register_setting('ud_shared_settings_group', 'ud_shared_tags_mode');
}
add_action('admin_init', 'ud_shared_register_settings');

// Nach dem Speichern oder Anlegen der Optionen prüfen, ob gelöscht werden soll
add_action('update_option_ud_shared_tags_mode', 'ud_shared_on_tags_mode_change', 10, 3);
add_action('add_option_ud_shared_tags_mode', 'ud_shared_on_tags_mode_add', 10, 2);

add_action('update_option_ud_shared_tags_blacklist', 'ud_shared_on_tags_blacklist_change', 10, 3);
add_action('add_option_ud_shared_tags_blacklist', 'ud_shared_on_tags_blacklist_add', 10, 2);

function ud_shared_on_tags_mode_change($old_value, $new_value, $option) {
    if ($new_value === 'delete') {
        ud_shared_delete_blacklisted_tags();
    }
}
function ud_shared_on_tags_mode_add($option, $value) {
    if ($value === 'delete') {
        ud_shared_delete_blacklisted_tags();
    }
}
function ud_shared_on_tags_blacklist_change($old_value, $new_value, $option) {
    $mode = get_option('ud_shared_tags_mode', 'ignore');
    if ($mode === 'delete') {
        ud_shared_delete_blacklisted_tags();
    }
}
function ud_shared_on_tags_blacklist_add($option, $value) {
    $mode = get_option('ud_shared_tags_mode', 'ignore');
    if ($mode === 'delete') {
        ud_shared_delete_blacklisted_tags();
    }
}

// Tag-Löschfunktion
function ud_shared_delete_blacklisted_tags() {
    $all_tags = get_option('ud_shared_tags', []);
    $blacklist = get_option('ud_shared_tags_blacklist', []);
    if (!is_array($blacklist)) {
        $blacklist = array_map('trim', explode(',', $blacklist));
    }
    $filtered = array_values(array_filter($all_tags, function ($tag) use ($blacklist) {
        return !in_array($tag, $blacklist, true);
    }));
    update_option('ud_shared_tags', $filtered);
    error_log("✅ [hook] Tags erfolgreich nach dem Speichern gelöscht.");
}

// Admin-Menü
function ud_shared_add_admin_menu() {
    add_management_page(
        'Globale Tags (Shared API)',        // Seitentitel (H1)
        'Shared Tags',                      // Menüname
        'manage_options',
        'ud-shared-tag-manager',
        'ud_shared_settings_page'
    );
}
add_action('admin_menu', 'ud_shared_add_admin_menu');

// Einstellungsseite
function ud_shared_settings_page() {
    $all_tags = get_option('ud_shared_tags', []);
    $blacklist = get_option('ud_shared_tags_blacklist', []);
    $blacklist_array = is_array($blacklist) ? $blacklist : array_map('trim', explode(',', $blacklist));
?>
    <div class="wrap">
        <h1>Shared Tags verwalten</h1>
        <p>
            Diese Seite ermöglicht das Bearbeiten von globalen Tags, die über die Shared API (REST) verwendet werden.<br>
            Du kannst sie ausblenden oder dauerhaft löschen.
        </p>




        <form method="post" action="options.php">
            <?php settings_fields('ud_shared_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Tags auswählen</th>
                    <td>
                        <?php if (!empty($all_tags)): ?>
                            <?php foreach ($all_tags as $tag): ?>
                                <?php $is_ignored = in_array($tag, $blacklist_array); ?>
                                <label style="<?php echo $is_ignored ? 'opacity: 0.6;' : ''; ?>">
                                    <input type="checkbox" name="ud_shared_tags_blacklist[]" value="<?php echo esc_attr($tag); ?>" <?php checked($is_ignored); ?> />

                                    <?php echo esc_html($tag); ?>
                                    <?php if ($is_ignored): ?>
                                        <em>(ignoriert)</em>
                                    <?php endif; ?>
                                </label><br>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <p><em>Keine Tags vorhanden.</em></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Aktion</th>
                    <td>
                        <?php $mode = get_option('ud_shared_tags_mode', 'ignore'); ?>
                        <label>
                            <input type="radio" name="ud_shared_tags_mode" value="ignore" <?php checked('ignore', $mode); ?> />
                            Ignorieren (nicht anzeigen, aber behalten)
                        </label><br>
                        <label>
                            <input type="radio" name="ud_shared_tags_mode" value="delete" <?php checked('delete', $mode); ?> />
                            Dauerhaft löschen
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <p style="margin-top: 4em;" class="description">
            <em>Technischer Hinweis:</em><br>
            Die folgenden Werte werden in der WordPress-Datenbank in der Tabelle <code>wp_options</code> gespeichert:<br>
            – <code>ud_shared_tags</code>: enthält alle aktuell gespeicherten Tags<br>
            – <code>ud_shared_tags_blacklist</code>: enthält Tags, die ignoriert oder gelöscht werden sollen<br>
            – <code>ud_shared_tags_mode</code>: legt fest, wie mit diesen Tags umgegangen wird

        </p>

    </div>
<?php
}

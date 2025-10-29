<?php
/**
 * Plugin Name:     UD Block-Erweiterung: Shared API
 * Description:     Gemeinsame REST-API für UD-Blöcke, z. B. zur Verwaltung globaler Tags.
 * Version:         1.0.0
 * Author:          ulrich.digital gmbh
 * Author URI:      https://ulrich.digital/
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     shared-api-ud
 */

defined('ABSPATH') || exit;

// REST-Routen registrieren
require_once __DIR__ . '/includes/register-rest-routes.php';

require_once __DIR__ . '/includes/admin-tag-filter.php';

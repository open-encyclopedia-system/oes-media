<?php

/**
 * Plugin Name: OES Media (OES Core Module)
 * Plugin URI: http://www.open-encyclopedia-system.org/
 * Description: Display media in an OES context.
 * Version: 1.2.3
 * Author: Maren Welterlich-Strobl, Freie Universität Berlin, Center für Digitale Systeme an der Universitätsbibliothek
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {

    /* check if OES Core Plugin is activated */
    if (!function_exists('OES')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>' .
                __('The OES Core Plugin is not active.', 'oes') . '</p></div>';
        });
    } else {

        /* exit early if OES Plugin was not completely initialized */
        global $oes;
        if (!$oes || !property_exists($oes, 'initialized') || !$oes->initialized) return;

        include_once(__DIR__ . '/includes/functions.php');
        add_action('wp_enqueue_scripts', 'oes_media_enqueue_scripts');

        /* include ACF media blocks */
        include_once(__DIR__ . '/includes/blocks/functions-blocks.php');
        add_action('acf/init', '\OES\Media\register_acf_blocks');
    }
}, 14);
<?php

/**
 * OES Media (Legacy OES Core Module)
 *
 * @wordpress-plugin
 * Plugin Name:        OES Media (Legacy OES Core Module)
 * Plugin URI:         https://www.open-encyclopedia-system.org/
 * Description:        Provides media display functionality in OES projects using ACF Pro.
 *                     Deprecated: This plugin has been replaced by the media blocks in OES Core as of version 2.4.0.
 * Version:            1.3.0
 * Author:             Maren Welterlich-Strobl, Freie Universität Berlin, FUB-IT
 * Author URI:         https://www.it.fu-berlin.de/die-fub-it/mitarbeitende/mstrobl.html
 * Requires at least:  6.0
 * Tested up to:       6.8.2
 * Requires PHP:       7.4
 * Tags:               oes, media, acf, deprecated, legacy, plugin-addon, encyclopedia
 * License:            GPLv2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */


namespace OES\Media;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {

    if (!function_exists('OES')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>' .
                __('The OES Core Plugin is not active.', 'oes') . '</p></div>';
        });
    } else {

        global $oes;
        if (!$oes || !property_exists($oes, 'initialized') || !$oes->initialized) return;

        add_action('acf/init', __NAMESPACE__ . '\register_acf_blocks');

        do_action('oes/media_plugin_loaded');
    }
}, 14);

/**
 * Register media blocks with ACF Pro.
 *
 * @return void
 */
function register_acf_blocks(): void
{
    if (function_exists('acf_register_block_type')) {

        acf_register_block_type([
            'name' => 'oes-panel',
            'title' => 'OES Panel (ACF Pro)',
            'render_callback' => '\OES\Media\render_panel',
            'keywords' => ['OES', 'panel', 'layout'],
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'jsx' => true,
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_oes_panel',
            'title' => 'OES Panel',
            'fields' => [
                [
                    'key' => 'block_field__panel_title',
                    'label' => 'Title',
                    'name' => 'panel_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'block_field__panel_expanded',
                    'label' => 'Expanded',
                    'name' => 'panel_expanded',
                    'type' => 'true_false',
                    'default_value' => true
                ]
            ],
            'location' => [[[
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/oes-panel',
            ]]]
        ]);

        acf_register_block_type([
            'name' => 'oes-image-panel',
            'title' => 'OES Image Panel (ACF Pro)',
            'render_callback' => '\OES\Media\render_image_panel',
            'keywords' => ['OES', 'Image', 'Panel'],
            'mode' => 'auto',
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'jsx' => true,
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_oes_image_panel',
            'title' => 'OES Image Panel',
            'fields' => [
                [
                    'key' => 'field_figure',
                    'label' => 'Image',
                    'name' => 'figure',
                    'type' => 'image',
                    'return_format' => 'array'
                ],
                [
                    'key' => 'field_figure_title',
                    'label' => 'Title',
                    'instructions' => 'Image title if empty, ignore if "none"',
                    'name' => 'figure_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_figure_number',
                    'label' => 'Number',
                    'name' => 'figure_number',
                    'instructions' => 'Include computed number in panel title',
                    'type' => 'true_false',
                    'default_value' => true
                ],
                [
                    'key' => 'field_figure_expanded',
                    'label' => 'Expanded',
                    'name' => 'figure_expanded',
                    'type' => 'true_false',
                    'default_value' => true
                ]
            ],
            'location' => [[[
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/oes-image-panel',
            ]]]
        ]);

        acf_register_block_type([
            'name' => 'oes-gallery-panel',
            'title' => 'OES Gallery Panel (ACF Pro)',
            'render_callback' => '\OES\Media\render_gallery_panel',
            'keywords' => ['OES', 'Gallery', 'Panel'],
            'mode' => 'auto',
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'jsx' => true,
            ],
        ]);

        /* @oesLegacy leave repeater field, even though gallery field would be better */
        acf_add_local_field_group([
            'key' => 'group_oes_gallery_panel',
            'title' => 'OES Gallery Panel (ACF Pro)',
            'fields' => [
                [
                    'key' => 'field_gallery_title',
                    'label' => 'Title',
                    'instructions' => 'Ignore if "none"',
                    'name' => 'gallery_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_gallery_number',
                    'label' => 'Include Numbers in Title',
                    'name' => 'gallery_number',
                    'type' => 'true_false',
                ],
                [
                    'key' => 'field_gallery_expanded',
                    'label' => 'Expanded',
                    'name' => 'gallery_expanded',
                    'type' => 'true_false',
                    'default_value' => true
                ],
                [
                    'key' => 'field_gallery_repeater',
                    'name' => 'gallery_repeater',
                    'label' => 'Images',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'collapsed' => 'field_gallery_figure_title',
                    'sub_fields' => [
                        [
                            'key' => 'field_gallery_figure',
                            'label' => 'Image',
                            'name' => 'gallery_figure',
                            'type' => 'image',
                        ]
                    ]
                ],
            ],
            'location' => [[[
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/oes-gallery-panel',
            ]]]
        ]);
    }
}

/**
 * Display an OES panel block.
 *
 * @param array $block The block data.
 * @return void
 */
function render_panel(array $block): void
{
    if ($data = $block['data'] ?? false) {
        echo '<div class="' . ($block['className'] ?? '') . '" id="' . ($block['anchor'] ?? '') . '">' .
            oes_get_panel_html('<InnerBlocks />', [
                'caption' => $data['panel_title'] ?? '',
                'active' => is_admin() ?
                    true :
                    ($data['panel_expanded'] ?? false)
            ]) .
            '</div>';
    }
}

/**
 * Display an OES image panel block.
 *
 * @param array $block The block data.
 * @param string $content The block content.
 * @param bool $is_preview Whether the block is being rendered for editing preview.
 *
 * @return void
 */
function render_image_panel(array $block, string $content, bool $is_preview): void
{
    if ($is_preview) {
        echo '<span>' . __('[Image Panel, rendered in frontend]', 'oes') . '</span>';
    }
    else {
        $image = $block['data']['figure'] ?? false;
        if (empty($image) || (is_array($image) && !isset($image['ID']))) {
            echo '<span>' . __('No valid image selected.', 'oes') . '</span>';
        }
        else {
            echo oes_get_image_panel_html(
                $image, [
                'add_number' => $block['data']['figure_number'] ?? true,
                'caption' => $block['data']['figure_title'] ?? '',
                'is_expanded' => (bool)($block['data']['figure_expanded'] ?? true)
            ]);
        }
    }
}

/**
 * Display an OES gallery panel block.
 *
 * @param array $block The block data.
 * @param string $content The block content.
 * @param bool $is_preview Whether the block is being rendered for editing preview.
 *
 * @return void
 */
function render_gallery_panel(array $block, string $content, bool $is_preview): void
{

    if ($is_preview) {
        echo '<span>' . __('[Gallery Panel, rendered in frontend]', 'oes') . '</span>';
    }
    else {

        $figures = [];
        $figureNumber = $block['data']['gallery_repeater'] ?? false;
        if ($figureNumber) {
            for ($i = 0; $i < $figureNumber; $i++) {
                if ($imageID = $block['data']['gallery_repeater_' . $i . '_gallery_figure'] ?? false) {
                    if ($figure = acf_get_attachment($imageID)) {
                        $figures[] = $figure;
                    }
                }
            }
        }

        if (empty($figures)) {
            echo '<span>' . __('No valid images selected.', 'oes') . '</span>';
        }
        else {
            echo oes_get_gallery_panel_html(
                $figures,
                [
                    'caption' => $block['data']['gallery_title'] ?? '',
                    'add_number' => $block['data']['gallery_number'] ?? false,
                    'is_expanded' => (bool)($block['data']['gallery_expanded'] ?? true)
                ]
            );
        }
    }
}
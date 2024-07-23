<?php

namespace OES\Media;


/**
 * Register media blocks with ACF Pro.
 *
 * @return void
 */
function register_acf_blocks(): void
{
    if (function_exists('acf_register_block_type')) {

        /* panel */

        acf_register_block_type([
            'name' => 'oes-panel',
            'title' => 'OES Panel',
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

        /* image panel */

        acf_register_block_type([
            'name' => 'oes-image-panel',
            'title' => 'OES Image Panel',
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

        /* gallery panel */

        acf_register_block_type([
            'name' => 'oes-gallery-panel',
            'title' => 'OES Gallery Panel',
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

        /* @oesLegacy leave repeater field, event though gallery field would be better */
        acf_add_local_field_group([
            'key' => 'group_oes_gallery_panel',
            'title' => 'OES Gallery Panel',
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
    if ($data = $block['data'] ?? false)
        echo '<div class="' . ($block['className'] ?? '') . '" id="' . ($block['anchor'] ?? '') . '">' .
            oes_get_panel_html('<InnerBlocks />', [
                'caption' => $data['panel_title'] ?? '',
                'active' => is_admin() ?
                    true :
                    ($data['panel_expanded'] ?? false)
            ]) .
            '</div>';
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
    if ($is_preview) echo '<span>' . __('[Image Panel, rendered in frontend]', 'oes') . '</span>';
    else {
        $image = $block['data']['figure'] ?? false;
        if (empty($image)) echo '<span>' . __('No valid image selected.', 'oes') . '</span>';
        else
            echo oes_get_image_panel_html(
                $image, [
                'add_number' => $block['data']['figure_number'] ?? true,
                'caption' => $block['data']['figure_title'] ?? '',
                'is_expanded' => (bool)($block['data']['figure_expanded'] ?? true)
            ]);
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

    if ($is_preview) echo '<span>' . __('[Gallery Panel, rendered in frontend]', 'oes') . '</span>';
    else {

        $figures = [];
        $figureNumber = $block['data']['gallery_repeater'] ?? false;
        if ($figureNumber)
            for ($i = 0; $i < $figureNumber; $i++) {
                if ($imageID = $block['data']['gallery_repeater_' . $i . '_gallery_figure'] ?? false)
                    if ($figure = acf_get_attachment($imageID))
                        $figures[] = $figure;
            }
        if (empty($figures)) echo '<span>' . __('No valid images selected.', 'oes') . '</span>';
        else
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
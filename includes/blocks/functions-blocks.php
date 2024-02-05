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
                    'key' => 'block_field__panel_number',
                    'label' => 'Number',
                    'name' => 'panel_number',
                    'type' => 'text',
                ],
                [
                    'key' => 'block_field__panel_number_prefix',
                    'label' => 'Number Prefix',
                    'name' => 'panel_number_prefix',
                    'type' => 'text',
                ],
                [
                    'key' => 'block_field__panel_expanded',
                    'label' => 'Expanded',
                    'name' => 'panel_expanded',
                    'type' => 'true_false',
                ],
                [
                    'key' => 'block_field__panel_class',
                    'label' => 'Class',
                    'name' => 'panel_class',
                    'type' => 'text',
                ],
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
                    'instructions' => 'Image title if empty',
                    'name' => 'figure_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_figure_number',
                    'label' => 'Number',
                    'name' => 'figure_number',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_figure_label',
                    'label' => 'Label',
                    'name' => 'figure_label',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_figure_expanded',
                    'label' => 'Expanded',
                    'name' => 'figure_expanded',
                    'type' => 'true_false',
                ],
                [
                    'key' => 'field_figure_bootstrap',
                    'label' => 'Use Bootstrap',
                    'name' => 'figure_bootstrap',
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

        acf_add_local_field_group([
            'key' => 'group_oes_gallery_panel',
            'title' => 'OES Gallery Panel',
            'fields' => [
                [
                    'key' => 'field_gallery_title',
                    'label' => 'Title',
                    'instructions' => 'image title if empty',
                    'name' => 'gallery_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_gallery_number',
                    'label' => 'Number',
                    'name' => 'gallery_number',
                    'type' => 'text',
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
                        ],
                        [
                            'key' => 'field_gallery_figure_number',
                            'instructions' => 'compute on empty',
                            'label' => 'Number',
                            'name' => 'gallery_figure_number',
                            'type' => 'text',
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
        echo '<div class="' . ($data['panel_class'] ?? '') . '">' .
            oes_get_panel_html('<InnerBlocks />', [
                'caption' => $data['panel_title'] ?? '',
                'number-prefix' => $data['panel_number_prefix'] ?? '',
                'number' => $data['panel_number'] ?? '',
                'bootstrap' => false,
                'active' => isset($_POST['post']) ?
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
    $data = $block['data'] ?? false;
    $image = $data['figure'] ?? false;
    if (empty($image) && $is_preview) echo '<span>' . __('No valid image selected.', 'oes') . '</span>';
    else {

        /* make sure image is array */
        if(is_int($image)) $image = acf_get_attachment($image);

        $panelTitle = $data['figure_title'] ?? '';
        if (empty($panelTitle) && $image['title']) $panelTitle = $image['title'];

        echo oes_get_image_panel_html([
            'figure' => $image,
            'figure_number' => $data['figure_number'] ?? '',
            'figure_include' => true
        ], [
            'label_prefix' => $data['figure_label'] ?? '',
            'panel_title' => $panelTitle,
            'bootstrap' => $data['figure_bootstrap'] ?? true,
            'active' => $data['figure_expanded'] ?? true
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
    $figures = $block['data']['gallery_repeater'] ?? false;

    if (empty($figures) && $is_preview) echo '<span>' . __('No valid images selected.', 'oes') . '</span>';
    else
        echo oes_get_gallery_panel_html(
            $figures,
            [
                'gallery_title' => $block['data']['gallery_title'] ?? '',
                'bootstrap' => false
            ]
        );
}
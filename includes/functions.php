<?php

/**
 * Include timeline assets.
 * @return void
 */
function oes_media_enqueue_scripts(): void
{
    $path = plugins_url(basename(__DIR__)) . '/../oes-media/assets/';
    wp_register_style('oes-media', $path . '/media.css');
    wp_enqueue_style('oes-media');
}


/**
 * Get the HTML representation of an OES panel.
 *
 * @param string $content The panel content.
 * @param array $args The options. Valid parameters are:
 *  'id'            :   The panel id.
 *  'caption'       :   The panel header caption.
 *  'active'        :   Boolean if the panel is active. If true, the panel is expanded.
 *  'number'        :   The panel header number.
 *  'number_prefix' :   The panel header number prefix.
 *  'bootstrap'     :   Bootstrap classes are included.
 *
 * @return string Return the html representation of the OES panel
 */
function oes_get_panel_html(string $content = '', array $args = []): string
{

    /* merge args */
    $args = array_merge([
        'id' => '',
        'caption' => '',
        'active' => true,
        'number' => true,
        'number_prefix' => '',
        'label_separator' => ' ',
        'bootstrap' => true
    ], $args);


    /**
     * Filters the panel arguments.
     *
     * @param array $args The panel arguments.
     */
    $args = apply_filters('oes/get_panel_html_args', $args);


    /* get figure number */
    if (is_bool($args['number']) && $args['number']) {

        /* get global parameters */
        global $oesListOfFigures;
        $postID = $GLOBALS['post']->ID;

        $number = isset($oesListOfFigures[$postID]['number']) ? $oesListOfFigures[$postID]['number'] + 1 : 1;

        /* update number */
        if (intval($number)) $oesListOfFigures[$postID]['number'] = $number;

        /* @var $editMode string check if in admin dashboard and edit mode (number is only computed in frontend) */
        $editMode = isset($_POST['post_id']);
        if ($editMode) $number = '%';

        $args['number'] = $number;
    }


    /* create anchor id */
    if (empty($args['id']) && !empty($args['caption'])) {
        $id = is_string($args['number']) ?
            preg_replace('/\s+/', '_', $args['number'] . '_' . $args['caption']) :
            preg_replace('/\s+/', '_', $args['caption']);
        $id = preg_replace('/[^a-zA-Z0-9_]/', '', oes_replace_umlaute($id));
        $args['id'] = 'panel_' . strtolower($id);
    }

    if ($args['bootstrap']) {
        $id = (isset($args['id']) && !empty($args['id']) ? $args['id'] : rand());
        return sprintf('<div class="oes-panel-container" id="%s">' .
            '<div class="oes-panel-wrapper">' .
            '<a href="#%s" class="oes-panel-header %s" data-toggle="collapse" role="button" ' .
            'aria-expanded="%s" aria-controls="%s">' .
            '<div class="oes-panel-title">' .
            '<span class="oes-caption-container">' .
            '<span class="oes-panel-caption-text"><label>%s%s</label></span>' .
            '<span class="oes-caption-title">%s</span>' .
            '<span class="oes-toggle-down-after oes-toggle-icon"></span>' .
            '</span>' .
            '</div></a>' .
            '<div class="oes-panel-bootstrap collapse %s" id="%s">%s</div>' .
            '</div></div>',
            $id,
            'oes-accordion-' . $id,
            $args['active'] ? '' : 'collapsed',
            $args['active'] ? 'true' : 'false',
            'oes-accordion-' . $id,
            $args['number_prefix'],
            $args['number'],
            $args['caption'],
            $args['active'] ? 'show' : '',
            'oes-accordion-' . $id,
            $content);
    } else {
        return sprintf('<div class="oes-panel-container" id="%s">' .
            '<div class="oes-accordion-wrapper">' .
            '<a class="oes-toggle-down-after oes-panel-header oes-accordion active" role="button">' .
            '<div class="oes-panel-title">' .
            '<span class="oes-caption-container oes-toggle-icon">' .
            '<span class="oes-panel-caption-text">' .
            '<label>%s%s</label>' .
            '<span class="oes-caption-title">%s</span>' .
            '</span>' .
            '</span>' .
            '</div></a>' .
            '<div class="oes-panel %s">%s</div>' .
            '</div></div>',
            $args['id'],
            $args['number_prefix'],
            $args['number'],
            $args['caption'],
            $args['active'] ? 'active' : '',
            $content);
    }
}



/**
 * Get the HTML representation of an OES gallery panel.
 *
 * @param array $figures The figures.
 * @param array $args The options. Valid parameters are:
 *  'label_prefix'      : The panel header label prefix.
 *  'gallery_title'     : The panel header.
 *  'active'        :   Boolean if the panel is active. If true, the panel is expanded.
 *
 * @return string Return the html representation of the OES panel
 */
function oes_get_gallery_panel_html(array $figures, array $args = []): string
{
    /* get global parameters */
    global $oesListOfFigures;
    $postID = $GLOBALS['post']->ID;

    /* additional args */
    $args = array_merge([
        'label_prefix' => 'Abb. ',
        'gallery_title' => '',
        'include_in_list' => false,
        'label_separator' => ' ',
        'active' => true,
        'pdf' => false,
        'pdf_title_class' => 'oes-pdf-figure-title',
        'bootstrap' => true
    ], $args);


    /**
     * Filters the gallery panel arguments.
     *
     * @param array $args The gallery panel arguments.
     */
    $args = apply_filters('oes/get_gallery_panel_html_args', $args);


    /* get figure */
    if ($figures) {

        /* prepare numbers */
        $numbers = [];
        $imageString = [];
        $itemIDs = [];

        /* count galleries */
        $galleryCount = isset($oesListOfFigures[$postID]['galleries']) ?
            (intval($oesListOfFigures[$postID]['galleries']) + 1) : 1;
        $oesListOfFigures[$postID]['galleries'] = $galleryCount;

        /* prepare gallery id */
        $galleryID = 'oes_gallery_' . $galleryCount;

        /* loop through figures */
        $validatedFigures = [];
        foreach ($figures as $key => $figureObject) {
            if (isset($figureObject['gallery_figure']) && is_array($figureObject['gallery_figure'])) {

                /* get figure number */
                $number = $figureObject['gallery_figure_number'] ?? false;
                if (!$number || empty($number))
                    $number = isset($oesListOfFigures[$postID]['number']) ? $oesListOfFigures[$postID]['number'] + 1 : 1;
                $numbers[] = $number;

                /* update number */
                if (intval($number)) $oesListOfFigures[$postID]['number'] = $number;


                /* check if included in list of figures */
                if ($args['include_in_list'] || $figureObject['gallery_figure_include'])
                    $oesListOfFigures[$postID]['figures'][] = [
                        'number' => $number,
                        'figure' => $figureObject['gallery_figure'],
                        'id' => $galleryID,
                        'type' => 'gallery'
                    ];

                /* create anchor id */
                $caption = $figureObject['gallery_figure']['title'] ?? 'Title missing';
                $id = preg_replace('/\s+/', '_', $number . '_' . $caption);
                $id = preg_replace('/[^a-zA-Z0-9_]/', '', oes_replace_umlaute($id));
                $id = 'figure_' . strtolower($id);

                /* add to figure list */
                $validatedFigures[] = [
                    'id' => $id,
                    'figure' => $figureObject['gallery_figure'],
                    'number' => $number,
                    'pagebreak' => $figureObject['gallery_figure_pagebreak'] ?? false
                ];


                /* prepare carousel string */
                $imageString[] = sprintf(
                    '<li class="%s %s"><a onclick="oesToggleGalleryPanel(%s)"><img src="%s" alt="%s"></a></li>',
                    'thumbnail-' . $id,
                    ($key === 0 ? 'oes-figure-thumbnail active' : 'oes-figure-thumbnail'),
                    $id,
                    $figureObject['gallery_figure']['url'] ?? '',
                    $figureObject['gallery_figure']['alt'] ?? ''
                );

                $itemIDs[] = $id;
            }
        }


        /* prepare slider controls */
        $nextIDs = $itemIDs;
        array_shift($nextIDs);
        $nextIDs[] = $itemIDs[0] ?? false;

        $prevIDs = $itemIDs;
        array_pop($prevIDs);
        if ($prevIDs) array_unshift($prevIDs, $itemIDs[array_key_last($itemIDs)]);


        if (sizeof($numbers) > 1) $numberString = $numbers[0] . ' - ' . end($numbers);
        else $numberString = $numbers[0] ?? '';

        /* @var $editMode string check if in admin dashboard and edit mode (number is only computed in frontend) */
        $editMode = isset($_POST['post_id']);
        if (empty($numberString) || $editMode) $numberString = '% - %';

        $galleryString = '';
        if ($args['pdf']) {

            /* prepare gallery string */
            foreach ($validatedFigures as $figureObject) {

                $imageModalData = \OES\Figures\oes_get_modal_image_data($figureObject['figure']);
                $caption = $imageModalData['caption'];

                /**
                 * Filters the image model caption.
                 *
                 * @param string $title The modal caption.
                 * @param array $table The image model table data.
                 * @param array $image The image.
                 */
                $caption = apply_filters('oes/get_modal_image_gallery_caption',
                    $caption,
                    $figureObject['figure'],
                    [],
                    $args);


                $galleryString .= '<div class="oes-pdf-figure-box">' .
                    '<div class="oes-pdf-image">' .
                    '<img src="' . ($figureObject['figure']['url'] ?? '') .
                    '" alt="' . ($figureObject['figure']['alt'] ?? '') . '">' .
                    '</div>' .
                    '<div class="oes-pdf-text">' .
                    '<div class="oes-pdf-text-wrapper">' .
                    '<span class="oes-figure-title-label">' .
                    $args['label_prefix'] . ($figureObject['number'] ?? '') . ':</span> ' .
                    $caption .
                    '</div>' .
                    '</div>' .
                    '</div>';

                /* optional pagebreak */
                if ($figureObject['pagebreak']) $galleryString .= '<pagebreak />';
            }


            return '<div class="oes-pdf-figure-container">' .
                '<div class="' . $args['pdf_title_class'] . '">' .
                $args['label_prefix'] . $numberString .
                '<span class="oes-caption-title">' . $args['label_separator'] . $args['gallery_title'] . '</span>' .
                '</div>' .
                $galleryString .
                '</div>';

        } else {

            /* prepare gallery string */
            foreach ($validatedFigures as $key => $figureObject)
                $galleryString .= oes_get_modal_image_gallery($figureObject['figure'],
                    [
                        'figure-class' => ($key === 0 ? 'oes-gallery-image active' : 'oes-gallery-image'),
                        'image-string' => (empty($imageString) ?
                            '' : '<ul>' . implode('', $imageString) . '</ul>'),
                        'figure-id' => $figureObject['id'],
                        'previous' => $prevIDs[$key],
                        'next' => $nextIDs[$key],
                        'item-id' => $itemIDs[$key],
                        'number' => $figureObject['number'] ?? '',
                        'additional-args' => $args
                    ]);

            if ($args['bootstrap']) {
                $id = (!empty($galleryID) ? $galleryID : rand());
                return sprintf('<div class="oes-panel-container" id="%s">' .
                    '<div class="oes-panel-wrapper">' .
                    '<a href="#%s" class="oes-panel-header %s" data-toggle="collapse" role="button" ' .
                    'aria-expanded="%s" aria-controls="%s">' .
                    '<div class="oes-panel-title">' .
                    '<span class="oes-caption-container">' .
                    '<span class="oes-panel-caption-text"><label>%s%s</label></span>' .
                    '<span class="oes-caption-title">%s</span>' .
                    '<span class="oes-toggle-down-after oes-toggle-icon"></span>' .
                    '</span>' .
                    '</div></a>' .
                    '<div class="oes-panel-bootstrap collapse %s" id="%s">%s</div>' .
                    '</div></div>',
                    $id,
                    'oes-accordion-' . $id,
                    $args['active'] ? '' : 'collapsed',
                    $args['active'] ? 'true' : 'false',
                    'oes-accordion-' . $id,
                    $args['label_prefix'],
                    $numberString,
                    $args['gallery_title'],
                    $args['active'] ? 'show' : '',
                    'oes-accordion-' . $id,
                    $galleryString);
            } else {
                return '<div class="oes-panel-container" id="' . $galleryID . '">' .
                    '<div class="oes-accordion-wrapper">' .
                    '<a class="oes-toggle-down-after oes-panel-header oes-accordion active" role="button">' .
                    '<div class="oes-panel-title">' .
                    '<span class="oes-caption-container oes-toggle-icon">' .
                    '<span class="oes-caption-text">' .
                    '<label>' . $args['label_prefix'] . $numberString . '</label>' .
                    '<span class="oes-caption-title">' . $args['gallery_title'] . '</span>' .
                    '</span>' .
                    '</span>' .
                    '</div>' .
                    '</a>' .
                    '<div class="oes-panel active">' . $galleryString . '</div>' .
                    '</div>' .
                    '</div>';
            }
        }
    } else
        return '<span>No valid image selected.</span>';
}



/**
 * Get the HTML representation of an OES image panel.
 *
 * @param array $image The image array, consisting of:
 *  'figure'         : The image.
 *  'figure_number'  : The figure number
 *  'figure_include' : Boolean indicating if figure is part of table of figures
 *
 * @param array $args The options. Valid parameters are:
 *  'label_prefix'  : The panel header label prefix.
 *  'panel_title'   : The panel header.
 *  'active'        : Boolean if the panel is active. If true, the panel is expanded.
 *
 * @return string Return the html representation of the OES image panel
 */
function oes_get_image_panel_html(array $image, array $args = []): string
{
    /* get global parameters */
    global $oesListOfFigures;
    $postID = $GLOBALS['post']->ID;

    /* additional args */
    $args = array_merge([
        'label_prefix' => 'Abb. ',
        'panel_title' => '',
        'label_separator' => ': ',
        'pdf_title_class' => 'oes-pdf-figure-title',
        'include_in_list' => true,
        'pdf' => false,
        'bootstrap' => true,
        'active' => true
    ], $args);


    /**
     * Filters the image panel arguments.
     *
     * @param array $args The panel arguments.
     */
    $args = apply_filters('oes/get_image_panel_html_args', $args);


    /* get figure */
    if ($image) {

        /* get figure number */
        $number = $image['figure_number'] ?? false;
        if (!$number || empty($number))
            $number = isset($oesListOfFigures[$postID]['number']) ? $oesListOfFigures[$postID]['number'] + 1 : 1;

        /* update number */
        if (intval($number)) $oesListOfFigures[$postID]['number'] = $number;

        /* check if included in list of figures */
        if ($args['include_in_list'])
            $oesListOfFigures[$postID]['figures'][] = [
                'number' => $number,
                'figure' => $image['figure'] ?? [],
                'id' => 'oes_image_' . ($image['figure']['ID'] ?? ''),
                'type' => 'image'
            ];

        /* create anchor id */
        $caption = $image['figure']['title'] ?? 'Title missing';
        $id = preg_replace('/\s+/', '_', $number . '_' . $caption);
        $id = preg_replace('/[^a-zA-Z0-9_]/', '', oes_replace_umlaute($id));
        $id = 'figure_' . strtolower($id);

        /* prepare image string */
        $imageString = oes_get_modal_image($image['figure'] ?? [],
            [
                'figure-id' => $id,
                'number' => $number ?? '',
                'additional-args' => $args
            ]);

        if($args['pdf']){

            return sprintf('<div class="oes-pdf-figure-container">' .
                '<div class="%s">%s<span class="oes-caption-title">%s</span></div>' .
                '<div class="oes-pdf-figure-box">' .
                '<div class="oes-pdf-image"><img src="%s" alt="%s"></div>' .
                '<div class="oes-pdf-text"><div class="oes-pdf-text-wrapper">%s</div></div>' .
                '</div>' .
                '</div>',
                $args['pdf_title_class'] ?? 'oes-pdf-figure-title',
                $args['label_prefix'] . $number . ': ',
                $args['panel_title'],
                ($image['figure']['url'] ?? ''),
                ($image['figure']['alt'] ?? ''),
                $caption);

        }
        elseif ($args['bootstrap']) {
            $id = 'oes_image_' . ($image['figure']['ID'] ?? rand());
            return sprintf('<div class="oes-panel-container" id="%s">' .
                '<div class="oes-panel-wrapper">' .
                '<a href="#%s" class="oes-panel-header %s" data-toggle="collapse" role="button" ' .
                'aria-expanded="%s" aria-controls="%s">' .
                '<div class="oes-panel-title">' .
                '<span class="oes-caption-container">' .
                '<span class="oes-panel-caption-text"><label>%s%s</label></span>' .
                '<span class="oes-caption-title">%s</span>' .
                '<span class="oes-toggle-down-after oes-toggle-icon"></span>' .
                '</span>' .
                '</div></a>' .
                '<div class="oes-panel-bootstrap collapse %s" id="%s">%s</div>' .
                '</div></div>',
                $id,
                'oes-accordion-' . $id,
                $args['active'] ? '' : 'collapsed',
                $args['active'] ? 'true' : 'false',
                'oes-accordion-' . $id,
                $args['label_prefix'],
                $number,
                $args['panel_title'],
                $args['active'] ? 'show' : '',
                'oes-accordion-' . $id,
                $imageString);
        } else {
            return '<div class="oes-panel-container" id="' . 'oes_image_' . ($image['figure']['ID'] ?? '') . '">' .
                '<div class="oes-accordion-wrapper">' .
                '<a class="oes-toggle-down-after oes-panel-header oes-accordion active" role="button">' .
                '<div class="oes-panel-title">' .
                '<span class="oes-caption-container oes-toggle-icon">' .
                '<span class="oes-caption-text">' .
                '<label>' . $args['label_prefix'] . $number . '</label>' .
                '<span class="oes-caption-title">' . $args['panel_title'] . '</span>' .
                '</span>' .
                '</span>' .
                '</div>' .
                '</a>' .
                '<div class="oes-panel active">' . $imageString . '</div>' .
                '</div>' .
                '</div>';
        }
    }

    return '';
}




/**
 * Get the html representation of a modal of an image for a image gallery.
 *
 * @param array $image The image post as array.
 * @param array $args Additional parameters.
 */
function oes_get_modal_image_gallery(array $image, array $args = []): string
{
    if (!$image['ID']) return '';

    /* get image data */
    $imageModalData = \OES\Figures\oes_get_modal_image_data($image);

    /* slider */
    $slider = '<a onclick="oesToggleGalleryPanel(' . ($args['previous'] ?? '') . ')" ' .
        'class="previous oes-slider-button"><span class="fa fa-angle-left"></span></a>' .
        '<a onclick="oesToggleGalleryPanel(' . ($args['next'] ?? '') . ')" class="next oes-slider-button">' .
        '<span class="fa fa-angle-right"></span></a>';

    /**
     * Filters the expand icon
     *
     * @param string $expandIcon The expand icon.
     * @param array $image The image.
     */
    $expandIcon = apply_filters('oes/modal_image_expand_image',
        '<span class="oes-expand-button oes-icon"><span class="dashicons dashicons-editor-expand"></span></span>',
        $image);

    /* modal toggle */
    $modalToggle = '<div class="oes-modal-toggle oes-modal-toggle">' .
        '<div class="oes-modal-toggle-container">' .
        '<img src="' . ($image['url'] ?? '') . '" alt="' . ($image['alt'] ?? 'empty') . '">' .
        $expandIcon .
        '</div>' .
        $slider .
        '</div>';

    /* table */
    $tableRows = '';
    if (!empty($imageModalData['table'] ?? []))
        foreach ($imageModalData['table'] as $description => $value)
            $tableRows .= sprintf('<tr><th>%s</th><td>%s</td></tr>', $description, $value);
    $table = empty($tableRows) ? '' :
        '<div class="oes-modal-content-text"><div>' .
        ($imageModalData['modal_subtitle'] ?? '') .
        '<table class="oes-table-pop-up">' .
        $tableRows . '</table></div></div>';

    /* modal */
    $modal = '<div class="oes-modal-container">' .
        '<span class="oes-modal-close dashicons dashicons-no"></span>' .
        '<div class="oes-modal-image-container">' .
        '<img alt="' . ($image['alt'] ?? 'empty') . '" src="">' .
        '</div>' . $table .
        '</div>';

    /* prepare caption */
    $caption = '';
    if (isset($args['number']) && !empty($args['number']) &&
        isset($args['include_number_in_subtitle']) && $args['include_number_in_subtitle'])
        $caption = '<span class="oes-figure-title-label">' . $args['number_prefix'] . $args['number'] . ':</span> ';
    $caption .= ($imageModalData['caption'] ?: '');


    /**
     * Filters the image model caption.
     *
     * @param string $title The modal caption.
     * @param array $image The image.
     * @param array $table The image model table data.
     * @param array $args Additional arguments.
     */
    $caption = apply_filters('oes/get_modal_image_gallery_caption',
        $caption,
        $image,
        $table,
        $args['additional-args'] ?? []);


    /* prepare image modal */
    return '<figure class="oes-expand-image ' . ($args['figure-class'] ?? '') . '"' .
        (isset($args['figure-id']) ? ' id="' . $args['figure-id'] . '"' : '') . '>' .
        $modalToggle . $modal .
        '<div class="oes-figure-slider-panel">' . ($args['image-string'] ?? '') . '</div>' .
        '<figcaption>' . $caption . '</figcaption>' .
        '</figure>';
}


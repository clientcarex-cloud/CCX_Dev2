<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'file_name',
    'visible_to_customer',
    'dateadded',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'files';
$where = ['AND rel_id=' . $patient_id . ' AND rel_type="customer"'];
$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'files.staffid',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'attachment_key', 'filetype', 'external', 'external_link', 'thumbnail_link', 'rel_id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // --- Column 1: File (Preview + Name) ---
    $path = get_upload_path_by_type('customer') . $patient_id . '/' . $aRow['file_name'];

    $is_image = false;
    $img_url = '';
    $lightBoxUrl = '';
    $attachment_url = site_url('download/file/client/' . $aRow['attachment_key']);

    if (empty($aRow['external'])) {
        $is_image = is_image($path);
        $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $aRow['filetype']);
        $lightBoxUrl = site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $aRow['filetype']);
    } elseif (!empty($aRow['external'])) {
        $attachment_url = $aRow['external_link'];
        if (!empty($aRow['thumbnail_link']) && $aRow['external'] === 'dropbox') {
            $is_image = true;
            $img_url = optimize_dropbox_thumbnail($aRow['thumbnail_link']);
        }
    }

    $fileHtml = '';
    $fileHtml .= '<div class="preview_image">';

    // Fix: Simplify Image Preview to avoid stuck loading state
    // Direct src instead of data-orig + JS loader
    if ($is_image) {
        $fileHtml .= '<a href="' . ($lightBoxUrl ?: $img_url) . '" data-lightbox="customer-profile" class="display-block mbot5">';
        $fileHtml .= '<div class="table-image"><img src="' . $img_url . '" class="img-responsive" style="max-width: 100px; max-height: 100px;"></div>';
        $fileHtml .= '</a>';
    } else {
        $fileHtml .= '<a href="' . $attachment_url . '?preview=1" target="_blank" class="display-block mbot5">';
        $fileHtml .= '<i class="' . get_mime_class($aRow['filetype']) . '"></i> ' . $aRow['file_name'];
        $fileHtml .= '</a>';
    }

    $fileHtml .= '</div>';
    $row[] = $fileHtml;

    // --- Column 2: Show to Customer (Toggle) ---
    $toggleHtml = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_attachments_show_notice') . '">';
    $toggleHtml .= '<input type="checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" class="onoffswitch-checkbox customer_file" data-switch-url="' . admin_url() . 'misc/toggle_file_visibility" ' . ($aRow['visible_to_customer'] == 1 ? 'checked' : '') . '>';
    $toggleHtml .= '<label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>';
    $toggleHtml .= '</div>';
    $row[] = $toggleHtml;

    // --- Column 3: Date Uploaded ---
    $row[] = _dt($aRow['dateadded']);

    // --- Column 4: Options ---
    $options = '<div class="tw-flex tw-items-center tw-space-x-2">';

    // Email Button (Standard Modal Trigger)
    // We cannot easily target specific modals outside the table context without extra JS, 
    // but we can simulate standard structure or just keep it simple.
    // The reference uses `data-target="#send_file"` and `send_file_modal` logic.
    // For now, let's keep it simple or comment out if complex modal is not loaded.
    // $options .= '<a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 tw-mt-1"><i class="fa-regular fa-envelope fa-lg"></i></a>';

    // Delete Button
    if (is_admin()) {
        $options .= '<a href="#" onclick="delete_file_inline(' . $aRow['id'] . '); return false;" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete"><i class="fa-regular fa-trash-can fa-lg"></i></a>';
    } else {
        // Disabled button for non-admins
        $options .= '<span class="tw-text-neutral-300 tw-cursor-not-allowed" title="' . _l('access_denied') . '"><i class="fa-regular fa-trash-can fa-lg"></i></span>';
    }
    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}

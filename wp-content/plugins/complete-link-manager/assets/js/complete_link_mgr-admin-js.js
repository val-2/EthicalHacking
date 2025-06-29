jQuery(document).ready(function($) {

    /*--------------------------- Modal Handling ---------------------------*/

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'flex';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
    }

    $(document).on('click', '#closeModal, #cancelUnlink', function () {
        closeModal($(this).closest('.custom-modal').attr('id'));
    });

    /*--------------------------- Draw DataTable Functionality ---------------------------*/

    $('#complete-links-table').DataTable({
        responsive: true,
        autoWidth: false, 
        ordering: false, 
    });

    /*--------------------------- Edit link Functionality ---------------------------*/

    $(document).on('click', '.edit-link', function() {
        const linkUrl = $(this).data('link-url');
        const linkText = $(this).data('link-text');
        const postId = $(this).data('post-id');
        const rowIndex = $(this).closest('tr').attr('row-index');
        const targetIndex = $(this).attr('target-index');

        $('#linkUrl').val(linkUrl);
        $('#linkText').val(linkText);
        $('#postId').val(postId);
        $('#old_link_url').val(linkUrl);
        $('#row_index').val(rowIndex);
        $('#target_index').val(targetIndex);

        $(this).closest('tr').attr('data-link-url', linkUrl);

        openModal('editLinkModal');
    });

    $(document).on('click', '#closeModal', function() {
        $.magnificPopup.close();
    });

    $('#editLinkForm').on('submit', function (e) {
        e.preventDefault();
    
        const linkUrl = $('#linkUrl').val();
        const linkText = $('#linkText').val();
        const postId = $('#postId').val();
        const oldLinkUrl = $('#old_link_url').val();
        const rowIndex = $('#row_index').val();
        const targetIndex = $('#target_index').val();

        const $submitButton = $('#editLinkForm .input-action-section button[type="submit"]');
        const originalButtonText = $submitButton.text();
        $submitButton.text('Updating...').prop('disabled', true);
    
        // Find the button that triggered the form opening and its parent row
        const $currentRow = $('#complete-links-table').find(`tr[row-index="${rowIndex}"]`);
    
        $.ajax({
            url: complete_link_mgr_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'complete_link_mgr_update_link',
                post_id: postId,
                link_url: linkUrl,
                link_text: linkText,
                old_link: oldLinkUrl,
                target_index: targetIndex,
                nonce: complete_link_mgr_ajax_obj.nonce
            },
            success: function (response) {
                if (response.success) {
                    const statusClass =
                        response.data.status_code === 200
                            ? 'status-green'
                            : [301, 302].includes(response.data.status_code)
                            ? 'status-blue'
                            : response.data.status_code === 404
                            ? 'status-red'
                            : 'status-orange';
    
                    const table = $('#complete-links-table').DataTable();
    
                    // Use row node directly for updating
                    const rowIndex = table.row($currentRow).index();
                    table
                        .row(rowIndex)
                        .data([
                            `<a href="${linkUrl}" target="_blank" class="link-${statusClass}">${linkUrl}</a>`,
                            linkText, // Retain the same link text
                            `<span class="${statusClass}">${response.data.status_code}</span>`,
                            `${response.data.page_name}`,
                            `<div class="clm-action-buttons">
                                <button class="button edit-link" 
                                        data-link-url="${linkUrl}" 
                                        data-link-text="${linkText}" 
                                        data-post-id="${postId}"
                                        target-index="${targetIndex}"
                                        title="Edit URL">
                                    <span class="dashicons dashicons-edit-large"></span>
                                </button>
                                <button class="button unlink" 
                                        data-link-url="${linkUrl}" 
                                        data-post-id="${postId}"
                                        target-index="${targetIndex}"
                                        title="Unlink">
                                    <span class="dashicons dashicons-editor-unlink"></span>
                                </button>
                                <a class="button edit-page" href="${response.data.edit_page_url}" target="_blank" title="Edit post/page"><span class="dashicons dashicons-edit-page"></span></button>
                            </div>`,
                        ])
                        .invalidate()
                        .draw();
    
                    // Add highlight effect
                    $currentRow.addClass(`highlight-${statusClass}`);
                    setTimeout(() => {
                        $currentRow.removeClass(`highlight-${statusClass}`);
                    }, 2000);
    
                    // Close modal
                    closeModal('editLinkModal');
                } else {
                    alert('Error updating link.');
                }
            },
            error: function () {
                alert('AJAX request failed.');
            },
            complete: function () {
                $submitButton.text(originalButtonText).prop('disabled', false);
            }
        });
    });

    /*--------------------------- Unlink Functionality ---------------------------*/

    let unlinkData = {};

    $(document).on('click', '.unlink', function () {
        const linkUrl = $(this).data('link-url');
        const postId = $(this).data('post-id');
        const linkText = $(this).data('link-text');
        const rowIndex = $(this).closest('tr').attr('row-index');
        const targetIndex = $(this).attr('target-index');

        // Store data for the unlink action
        unlinkData = { linkUrl, postId, linkText, rowIndex, targetIndex };

        // Open the confirmation popup
        openModal('unlinkConfirmationModal');
    });

    $(document).on('click', '#confirmUnlink', function () {
        const { linkUrl, postId, linkText, rowIndex, targetIndex } = unlinkData;

        // Close the popup
        closeModal('unlinkConfirmationModal');

        // Send AJAX request to unlink
        $.ajax({
            url: complete_link_mgr_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'complete_link_mgr_unlink_action',
                post_id: postId,
                link_url: linkUrl,
                link_text: linkText,
                target_index: targetIndex,
                nonce: complete_link_mgr_ajax_obj.nonce
            },
            success: function (response) {
                if (response.success) {
                    // Get DataTable instance
                    const table = $('.complete-links-table').DataTable();

                    // Find the row to be removed using the targetIndex
                    const row = table.row(`tr[row-index="${rowIndex}"]`);

                    if (row.length) {
                        console.log('Before removing row, index:', row.index());
                        row.remove().draw();
    
                        // Update target-index for rows grouped by postId
                        const postRows = table.rows().nodes().to$().filter(function () {
                            return $(this).find('button').data('post-id') === postId;
                        });

                        postRows.each(function (index) {
                            const currentRow = $(this);
                            currentRow.find('button').attr('target-index', index);
                        });

                        table.rows().every(function (index) {
                            const currentRow = this.node();
                            $(currentRow).attr('row-index', index);
                        });
                    }
                } else {
                    alert('Error removing the link.');
                }
            },
            error: function () {
                alert('AJAX request failed.');
            },
        });
    });

    $(document).on('click', '#cancelUnlink', function () {
        $.magnificPopup.close();
    });

    /*--------------------------- Pro Notification Code ---------------------------*/

    $(document).on('click', '.clm-review-notice .notice-dismiss', function () {
        $.post(complete_link_mgr_ajax_obj.ajax_url, { action: 'complete_link_mgr_dismiss_review_notice' });
    });
    
});
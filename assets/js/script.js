(function($) {
    'use strict';

    // Store modal HTML template
    const modalTemplate = `
        <div id="zio-mpt-modal" class="zio-mpt-modal">
            <div class="zio-mpt-modal-content">
                <div class="zio-mpt-modal-header">
                    <h2>${zioMPT.i18n.changePostType}</h2>
                    <span class="zio-mpt-close">&times;</span>
                </div>
                <div class="zio-mpt-modal-body">
                    <p>${zioMPT.i18n.selectNewType}</p>
                    <select id="zio-mpt-new-type" class="zio-mpt-select">
                        ${Object.entries(zioMPT.post_types)
                            .map(([value, label]) => 
                                `<option value="${value}">${label}</option>`
                            ).join('')}
                    </select>
                </div>
                <div class="zio-mpt-modal-footer">
                    <button type="button" class="button zio-mpt-cancel">
                        ${zioMPT.i18n.cancel}
                    </button>
                    <button type="button" class="button button-primary zio-mpt-apply">
                        ${zioMPT.i18n.apply}
                    </button>
                </div>
            </div>
        </div>
    `;

    class PostTypeManager {
        constructor() {
            this.bindEvents();
        }

        bindEvents() {
            // Handle bulk action selection
            $(document).on('click', '#doaction, #doaction2', this.handleBulkAction.bind(this));
            
            // Handle post type creation form
            $('#zio-mpt-create-form').on('submit', this.handlePostTypeCreation.bind(this));
            
            // Dynamic event bindings for modal
            $(document).on('click', '.zio-mpt-close, .zio-mpt-cancel', this.closeModal.bind(this));
            $(document).on('click', '.zio-mpt-apply', this.applyPostTypeChange.bind(this));
            $(document).on('click', '.zio-mpt-modal', function(e) {
                if (e.target === this) {
                    this.closeModal();
                }
            }.bind(this));
        }

        handleBulkAction(e) {
            const select = $(e.target).prev('select');
            const action = select.val();

            if (action === 'change_post_type') {
                e.preventDefault();
                
                const selectedPosts = $('input[name="post[]"]:checked');
                if (selectedPosts.length === 0) {
                    alert(zioMPT.i18n.noPostsSelected);
                    return;
                }

                this.openModal(selectedPosts);
            }
        }

        openModal(selectedPosts) {
            // Store selected post IDs
            this.selectedPosts = $.makeArray(selectedPosts.map(function() {
                return $(this).val();
            }));

            // Add modal to page
            $('body').append(modalTemplate);
            
            // Show modal with animation
            setTimeout(() => {
                $('#zio-mpt-modal').addClass('show');
            }, 10);
        }

        closeModal() {
            const modal = $('#zio-mpt-modal');
            modal.removeClass('show');
            setTimeout(() => {
                modal.remove();
            }, 300);
        }

        applyPostTypeChange() {
            const newType = $('#zio-mpt-new-type').val();
            const loadingButton = $('.zio-mpt-apply').text(zioMPT.i18n.processing);

            $.ajax({
                url: zioMPT.ajaxurl,
                type: 'POST',
                data: {
                    action: 'zio_change_post_type',
                    nonce: zioMPT.nonce,
                    post_ids: this.selectedPosts,
                    new_post_type: newType
                },
                beforeSend: () => {
                    loadingButton.prop('disabled', true);
                }
            })
            .done((response) => {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.data || zioMPT.i18n.errorOccurred);
                }
            })
            .fail(() => {
                alert(zioMPT.i18n.errorOccurred);
            })
            .always(() => {
                loadingButton.prop('disabled', false);
                this.closeModal();
            });
        }

        handlePostTypeCreation(e) {
            e.preventDefault();
            const form = $(e.target);
            const submitButton = form.find('[type="submit"]');

            $.ajax({
                url: zioMPT.ajaxurl,
                type: 'POST',
                data: form.serialize(),
                beforeSend: () => {
                    submitButton.prop('disabled', true)
                        .text(zioMPT.i18n.creating);
                }
            })
            .done((response) => {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.data || zioMPT.i18n.errorOccurred);
                }
            })
            .fail(() => {
                alert(zioMPT.i18n.errorOccurred);
            })
            .always(() => {
                submitButton.prop('disabled', false)
                    .text(zioMPT.i18n.createPostType);
            });
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        new PostTypeManager();
    });

})(jQuery);
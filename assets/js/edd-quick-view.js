(function($) {
    // Quick view click
    $('body').on('click.eddQuickView', '.edd-quick-view-button', function (e) {
        e.preventDefault();

        var $this = $(this);
        var download = $this.data('download-id');
        var download_container = $this.closest('.edd_download');

        if( $('#edd-quick-view-' + download).length == 0 ) {
            // Disable button, preventing more clicks during ajax request
            $this.prop('disabled', true);

            // Show the spinner
            $this.attr('data-edd-loading', '');

            var data = {
                action: 'edd_quick_view',
                nonce: edd_quick_view.nonce,
                download_id: download
            };

            $.ajax({
                url: edd_quick_view.ajax_url,
                type: "POST",
                data: data,
                dataType: "json",
                success: function (response) {
                    $('<div id="edd-quick-view-' + download + '" class="edd-quick-view-dialog" style="display: none;">' + response.html + '</div>').insertAfter( download_container );

                    // Create and open dialog
                    $('#edd-quick-view-' + download).dialog({
                        width: 600,
                        resizable: false,
                        modal: true,
                        draggable: false,
                        dialogClass: 'edd-quick-view-dialog',
                    });

                    // Re-enable the quick view button
                    $this.prop('disabled', false);

                    // remove spinner
                    $this.removeAttr('data-edd-loading');
                }
            });
        } else {
            // If downloads has been already quick viewed, then show stored html
            $('#edd-quick-view-' + download).dialog( 'open' );
        }

        return false;
    });
})(jQuery);
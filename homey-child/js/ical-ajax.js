jQuery(document).ready(function ($) {
    "use strict";
    if (typeof Homey_Listing !== "undefined") {
        var ajaxurl = Homey_Listing.ajaxURL;
        var process_loader_spinner = Homey_Listing.process_loader_spinner;
        /*---------------------------------------------------------------------------
        *  iCalendar
        *--------------------------------------------------------------------------*/
        jQuery('#sultan_import_ical_feeds').on('click', function (e) {
            e.preventDefault();

            var $this = jQuery(this);
            var ical_feed_name = '';
            var ical_feed_url = '';

            var listing_id = jQuery('input[name="listing_id"]').val();

            jQuery('.sultan_ical_feed_name').each(function () {
                ical_feed_name = jQuery(this).val()
            });

            jQuery('.sultan_ical_feed_url').each(function () {
                ical_feed_url = jQuery(this).val()
            });

            if (ical_feed_name == '' || ical_feed_url == '') {
                alert(Homey_Listing.add_ical_feeds);
                return;
            }

            jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                dataType: 'html',
                data: {
                    'action': 'sultan_scrap_dates',
                    'sultan_listing_id': listing_id,
                    'sultan_ical_feed_name': ical_feed_name,
                    'sultan_scrap_link': ical_feed_url,
                },

                beforeSend: function () {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left ' + process_loader_spinner + '"></i>');
                },
                success: function (response) {
                    $this.children('i').remove();
                    jQuery('body').append(response);
                },
                error: function (xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function () {
                    //$this.children('i').removeClass(process_loader_spinner);
                }
            });

        }) // end #import_ical_feeds

        jQuery('#sultan_add_more_feed').on('click', function (e) {
            e.preventDefault();

            var ical_feed_name = jQuery('.sultan_enter_ical_feed_name').val();
            var ical_feed_url = jQuery('.sultan_enter_ical_feed_url').val();

            if (ical_feed_name == '' || ical_feed_url == '') {
                alert(Homey_Listing.both_required);
                return;
            }

            var numVal = jQuery(this).data("increment") + 1;
            jQuery(this).data('increment', numVal);
            jQuery(this).attr({
                "data-increment": numVal
            });

            var newFeed = '' +
                '<div class="imported-calendar-row clearfix">' +
                '<div class="imported-calendar-50">' +
                '<input type="text" name="sultan_ical_feed_name[]" class="form-control sultan_ical_feed_name" value="' + ical_feed_name + '">' +
                '</div>' +
                '<div class="imported-calendar-50">' +
                '<input type="text" name="sultan_ical_feed_url[]" class="form-control sultan_ical_feed_url" value="' + ical_feed_url + '">' +
                '</div>';
            '</div>';

            jQuery('#sultan_ical-feeds-container').append(newFeed);
            removeICalFeed();
            jQuery('.ical-dummy').val('');
        });

        var removeICalFeed = function () {

            jQuery('.sultan-remove-ical-feed').on('click', function (event) {
                event.preventDefault();

                var $this = jQuery(this);
                var listing_id = jQuery('input[name="listing_id"]').val();
                var remove_index = $this.data('remove');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    dataType: "JSON",
                    data: {
                        'action': 'sultan_homey_remove_ical_feeds',
                        'listing_id': listing_id,
                        'remove_index': remove_index
                    },

                    beforeSend: function () {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left ' + process_loader_spinner + '"></i>');
                    },
                    success: function (response) {
                        console.log(response.message);
                        if (response.success) {
                            $this.closest('.imported-calendar-row').remove();
                            var reloadWindow = setInterval(function () {
                                window.location.reload();
                                clearInterval(reloadWindow);
                            }, 1000);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                });

                var numVal = jQuery('#sultan_add_more_feed').data("increment")
                jQuery('#sultan_add_more_feed').attr({
                    "data-increment": numVal - 1
                });
            });
        }
        removeICalFeed();
    }
});

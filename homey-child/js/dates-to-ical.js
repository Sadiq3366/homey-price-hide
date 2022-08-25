function ical_download(arrayData, ajax_url, sultan_listing_id, sultan_ical_feed_name)  {
    
    //helper functions

    //iso date for ical formats
    this._isofix = function (d) {
        var offset = ("0" + ((new Date()).getTimezoneOffset() / 60)).slice(-2);

        if (typeof d == 'string') {
            return d.replace(/\-/g, '') + 'T' + offset + '0000Z';
        } else {
            return d.getFullYear() + this._zp(d.getMonth() + 1) + this._zp(d.getDate()) + 'T' + this._zp(d.getHours()) + "0000Z";
        }
    }

    //zero padding for data fixes
    this._zp = function (s) { return ("0" + s).slice(-2); }

    var now = new Date();
    var ics_lines = [];
    var data = [];
    var fn = [];
    arrayData.forEach(o => {
        fn.push(o[0]);
        var i = 0;
        for (key in o[3]) {
            if (o[3].hasOwnProperty(key)) {
                let _name = o[0];
                let site_url = o[1];
                let scrap_url = o[2];
                let start = o[3][key];
                let end = o[4][key];
                if (i !== o[3].length - 1) {
                    data.push({
                        UID: "event-" + now.getTime() + "@addroid.com",
                        DTSTAMP: this._isofix(now),
                        DTSTART: this._isofix(start),
                        DTEND: this._isofix(end),
                        DESCRIPTION: site_url + '\t' + scrap_url,
                        SUMMARY: _name,
                        END_VEVENTBEGIN: "VEVENT"
                    });
                } else {
                    data.push({
                        UID: "event-" + now.getTime() + "@addroid.com",
                        DTSTAMP: this._isofix(now),
                        DTSTART: this._isofix(start),
                        DTEND: this._isofix(end),
                        DESCRIPTION: site_url + '\t' + scrap_url,
                        SUMMARY: _name,
                    });
                }
            }
            i++;
        }
        ics_lines.push({
            BEGIN: "VCALENDAR",
            VERSION: "2.0",
            PRODID: "-//Addroid Inc.//iCalAdUnit//EN",
            METHOD: "REQUEST",
            BEGIN: "VEVENT",
            EVENTS: data,
            SEQUENCE: "0",
            END_VEVENT: "VEVENT",
            END: "VCALENDAR"
        });
        data = [];
    });

    var n = 0;
    var t_url = [];
    ics_lines.forEach(obj => {
        try {
            t_url.push(obj);
        } catch (e) {
            console.log(e);
        }
        n++;
    });

    jQuery.ajax({
        url: ajax_url,
        dataType: 'JSON',
        method: 'POST',
        data: {
            'action': 'sultan_homey_insert_icalendar_feeds',
            'test_ical': t_url[0],
            'sultan_listing_id': sultan_listing_id,
            'sultan_ical_feed_name': sultan_ical_feed_name,
        },
        success: function (res) {
            if (res.success) {
                jQuery('#modal-scrap-dates').closest('.imported-calendar-row').remove();
                var reloadWindow = setInterval(function () {
                    window.location.reload();
                    clearInterval(reloadWindow);
                }, 1000);
            } else {
                console.log(res);
            }
        },
        error: function (xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log(err.Message);
        }
    });

}
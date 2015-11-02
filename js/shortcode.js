jQuery(document).ready(function ($) {
    var page_id = $("#page_filter_input").val();

    $("#tag_filter_input").autoComplete(
        {
            minChars: 0,
            delay: 10,
            source: function (term, response) {
                $.getJSON('/wp-admin/admin-ajax.php?action=spri_fb_page_tag_list&page_id='+page_id, {q: term}, function (data) {
                    suggestions = [];
                    for (i = 0; i < data.length; i++)
                        if (~data[i].toLowerCase().indexOf(term)) suggestions.push(data[i]);
                    response(suggestions);
                });
            }
        }
    );

});
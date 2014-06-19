jQuery(document).ready(function ($) {
    $('.ct-demo-import').click(function () {
        var $a = $(this);
        $('.importDescription').fadeOut('fast', function () {
            $('.importLoader').fadeIn('fast');

            $.post(ajaxurl, {'action': $a.attr('data-action'), 'dir': $a.attr('data-dir')}, function (html) {
                $('.importLoader').fadeOut('fast', function () {
                    $('.importDescription').html(html).show();
                });
            });
        });
        return false;
    });
});
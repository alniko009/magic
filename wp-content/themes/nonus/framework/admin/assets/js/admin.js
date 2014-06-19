jQuery(document).ready(function ($) {

    jQuery('select.ct-toggler').change(function () {
        var $s = jQuery(this).find(":selected");
        var g = $s.attr('data-group');
        var target = $s.attr('data-toggle');
        if (g) {
            jQuery(g).hide();
        }
        jQuery(target).fadeIn();
    }).change();


    $(document).on('click', '#visual_composer_content .controls_row a.child_clone', function () {
        var $a = $(this).closest('.wpb_content_holder').find('.wpb_element_wrapper .wpb_content_element:last a.column_clone');
        $a.click();
        return false;
    });
});
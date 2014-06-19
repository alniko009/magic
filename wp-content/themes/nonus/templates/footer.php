<footer>
    <div class="container">
        <div class="row-fluid">
            <div class="span3">
	            <?php dynamic_sidebar('sidebar-footer1'); ?>
            </div>
            <div class="span3">
	            <?php dynamic_sidebar('sidebar-footer2'); ?>
            </div>
            <div class="span3">
	            <?php dynamic_sidebar('sidebar-footer3'); ?>
            </div>
            <div class="span3">
	            <?php dynamic_sidebar('sidebar-footer4'); ?>
            </div>
        </div>
    </div>
    <h4>
	    <?php echo strtr(ct_get_option('general_footer_text', ''), array('%year%' => date('Y'), '%name%' => get_bloginfo('name', 'display')))?>
    </h4>
</footer>
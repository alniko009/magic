<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<?php if (have_posts()) : ?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name') ?> Feed" href="<?php echo home_url() ?>/feed/">
	<?php endif; ?>

	<?php wp_head(); ?>

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
 <!--[if lt IE 9]>
 <script src="<?php echo CT_THEME_ASSETS ?>/js/html5shiv.js"></script>
 <![endif]-->

    <script src="<?php echo CT_THEME_ASSETS ?>/js/touch.timeline.min.js"></script>
    <script src="<?php echo CT_THEME_ASSETS ?>/js/jquery.timer.js"></script>
    <link rel="stylesheet" href="<?php echo CT_THEME_ASSETS ?>/css/touch.timeline.light.min.css" type="text/css" />
	
	<script>	
	jQuery( document ).ready(function() {
		var slider_timer = jQuery.timer(slider_loop);
		var tcount = jQuery(".timeline-title").size();
		var current = 1;
		slider_timer.set({ time : 5000, autostart : true });
		
		function slider_loop(){
			jQuery(".timeline-title").css('color','#333333');
			jQuery(".timeline-title").css('font-weight','normal');
			jQuery(".timeline-title").eq(current).click();
			if(current < tcount-1){
				current++;
			} else {
				current = 0;
			}
		}
		
		jQuery(".timeline-title").eq(0).css('color','#2d91ff');
		jQuery(".timeline-title").css('font-weight','normal');
		jQuery(".timeline-title").click(function(e){
			jQuery(".timeline-title").css('color','#333333');
			jQuery(this).css('color','#2d91ff');
			jQuery(this).css('font-weight','bold');
		});		
	});
	</script>
</head>

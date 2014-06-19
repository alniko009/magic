<?php

//fix for empty <p></p>
function shortcode_empty_paragraph_fix($content)
{
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);

	return $content;
}
add_filter('the_content', 'shortcode_empty_paragraph_fix');

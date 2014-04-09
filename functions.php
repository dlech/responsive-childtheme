<?php

function image_shortcode($atts, $content = null) {
    extract( shortcode_atts( array(
    'name' => '',
    'align' => 'right',
    'ext' => 'png',
    'path' => '/wp-content/uploads/',
    'url' => ''
    ), $atts ) );
    $file=ABSPATH."$path$name.$ext";
    if (file_exists($file)) {
        $size=getimagesize($file);
        if ($size!==false) $size=$size[3];
        $output = "<img src='".get_option('siteurl')."$path$name.$ext' alt='$name' $size align='$align' class='align$align' />";
        if ($url) $output = "<a href='$url' title='$name'>".$output.'</a>';
        return $output;
    }
    else {
        trigger_error("'$path$name.$ext' image not found", E_USER_WARNING);
        return '';
    }
}
add_shortcode('image','image_shortcode');


function page_modified_date($args) {
	if ( is_page() && !get_post_meta( get_the_ID(), 'hide_page_modified_date', true ) )
		echo '<span class="modified-date">Updated: ' . get_the_modified_date() . '</span>';
}
add_action('responsive_entry_top', 'page_modified_date');

function render_hide_page_modified_date_meta_box( $page )
{
	wp_nonce_field( 'render_hide_page_modified_date_meta_box', 'render_hide_page_modified_date_meta_box_nonce' );
	$value = get_post_meta( $page->ID, 'hide_page_modified_date', true );

	echo '<input type="checkbox" id="hide_page_modified_date" name="hide_page_modified_date" ' . ( $value ? 'checked' : '' ) . ' />';
	echo '<label for="hide_page_modified_date">Hide.</label>';
}

function hide_page_modified_date_meta_box( $post ) {
	add_meta_box(
		'hide-page-modified-date-meta-box',
		__( 'Page Updated Date' ),
		'render_hide_page_modified_date_meta_box',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_page', 'hide_page_modified_date_meta_box' );

function hide_page_modified_date_save_postdata ( $post_id )
{
	if ( ! isset( $_POST['render_hide_page_modified_date_meta_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['render_hide_page_modified_date_meta_box_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'render_hide_page_modified_date_meta_box' ) )
		return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;

	update_post_meta( $post_id, 'hide_page_modified_date', isset( $_POST['hide_page_modified_date'] ) );
}
add_action( 'save_post', 'hide_page_modified_date_save_postdata' );

function lechnology_favicon ( $arg )
{
	echo '<link rel="shortcut icon" href="' . get_stylesheet_directory_uri() . '/favicon.png" />';
} 
add_action( 'wp_head', 'lechnology_favicon');

function category_rss_link_filter ( $arg1, $arg2 )
{
	global $wp_query;
	if ( $arg2 == 'url' && $wp_query->is_category )
		$arg1 .= '/' . $wp_query->query['category_name'];
	return ( ent2ncr( $arg1 ) );
}
add_filter('get_bloginfo_rss', 'category_rss_link_filter', 10, 2);
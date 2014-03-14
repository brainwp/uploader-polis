<?php
// we need memory and time
 
ini_set( 'memory limit', -1);
set_time_limit( 0 );
 
// WordPress environment
 
include( 'wp-load.php' );
 
// database to import posts from
 
global $wpdb;
include( 'login.php' );
$counter = 0;
foreach ( $external_db->get_results( "SELECT * FROM CLD_COLLECTIONDATA") as $p ) {
 
    // insert a post

	$new_slug = sanitize_title( $p->cld_title );
		
    $args = array(
		'ID'             	=> $p->cld_id,
        'post_content' 		=> $p->cld_description,
		'post_name'     	=> $new_slug,
        'post_title' 		=> $p->cld_title,
		'post_status'    	=> 'draft',
		'post_type'      	=> 'post',
		'post_author'    	=> 1,
		'ping_status'    	=> 'open',
		'post_parent'    	=> '',
		'menu_order'     	=> '',
		'to_ping'        	=> '',
		'pinged'         	=> '',
		'post_password'  	=> '',
		//'guid'           	=> // Skip this and let Wordpress handle it, usually.
		//'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
		'post_excerpt'   	=> '',
		'post_date'      	=> $p->cld_date,
		'post_date_gmt'  	=> '',
		'comment_status' 	=> 'closed',
		//'post_category'  => [ array(<category id>, ...) ] // Default empty.
		//'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
		//'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
		'page_template'  => '',
    );
    $post_id = wp_insert_post( $args );
 
    // parsing the post content for images
 
    $content = $p->cld_description;
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $tags = $doc->getElementsByTagName('img');
 
    // import images as attachments

    foreach ($tags as $tag) {
 
        $url = $tag->getAttribute('src');
        $image_id = media_sideload_image($url, $post_id);
 
        // update post URLs
 
        $image_url = wp_get_attachment_image_src($image_id);
        $content = str_replace($url, $image_url, $content);

    }

 
    // update post content
 
    $args['ID'] = $post_id;
    $args['post_content'] = $content;
	echo '<pre>'.var_dump($args).'</pre>';
    //0($args);
		$counter++;		
		if( $counter >= 5 ) {
			break;
		}

}

?>

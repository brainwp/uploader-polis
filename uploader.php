<?php
// we need memory and time
 
ini_set( 'memory limit', -1);
set_time_limit( 0 );
 
// WordPress environment
 
include( 'wp-load.php' );
 
// database to import posts from
 
global $wpdb;
include( 'login.php' );

foreach ( $external_db->get_results( "SELECT * FROM CLD_COLLECTIONDATA") as $p ) {
 
    // insert a post
 
    $args = array(
        'post_title' => $p->cld_title,
        'post_content' => $p->cld_description,
        /* other database fields that match posts's columns */
    );
    $post_id = wp_insert_post( $args );
 
    // parsing the post content for images
 
    $content = $p->cld_description;
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $tags = $doc->getElementsByTagName('img');
 
    // import images as attachments

	$count = 0;
 
    foreach ($tags as $tag) {
 
        $url = $tag->getAttribute('src');
        $image_id = media_sideload_image($url, $post_id);
 
        // update post URLs
 
        $image_url = wp_get_attachment_image_src($image_id);
        $content = str_replace($url, $image_url, $content);

		$counter++;		
		if( $counter >= 5 ){
			break;
		}
 
    }

 
    // update post content
 
    $args['ID'] = $post_id;
    $args['post_content'] = $content;
	var_dump( $args );
    //wp_update_post($args);
 
}

?>

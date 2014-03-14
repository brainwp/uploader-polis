<!-- xml version="1.0" encoding="UTF-8" --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head></head> 
<body> 

<?php
// we need memory and time
 
ini_set( 'memory limit', -1);
set_time_limit( 0 );
 
// WordPress environment
 
include( 'wp-load.php' );
 
// database to import posts from
 
global $wpdb;
include( 'login.php' );

// Tabela PGD_PAGEDATA

echo '<h1>' . "Tabela PGD_PAGEDATA" . '</h1>';

foreach ( $external_db->get_results( "SELECT * FROM PGD_PAGEDATA") as $p ) {
 
    // insert a post
	
	$pgd_slug = sanitize_title( $p->pgd_title );
    $args = array(
		//'ID'             	=> $p->pgd_id,
        'post_content' 		=> $p->pgd_html,
		'post_name'     	=> $pgd_slug,
        'post_title' 		=> $p->pgd_title,
		'post_status'    	=> 'publish',
		'post_type'      	=> 'page',
		'post_author'		=> 1,
		'post_excerpt'   	=> $p->pgd_description,
    );

    $post_id = wp_insert_post( $args );

}

?>
</body> 
</html> 

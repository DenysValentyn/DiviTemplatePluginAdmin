<?php

// Allow from any origin Added By Denys
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS request early, if needed Added By Denys
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if(!empty($_GET['termid'])){

    include('../../../../wp-load.php');

    $parent_term_id = $_GET['termid'];
    $taxonomy = 'layoutscategories';
    $custom_post_type = 'divilayouts';
    
    $child_terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'child_of'   => $parent_term_id,
        'hide_empty' => false, // Change to true if you want to hide empty terms
    ));
    $child_term_ids = wp_list_pluck($child_terms, 'term_id');
    if(count($child_term_ids) == 0) { 
        $child_term_ids[] = $parent_term_id; 
    }
    // Pagination parameters
    $posts_per_page = 10; // Number of posts per page
    if($parent_term_id == 6) {
		$posts_per_page = 10;
	} else if($parent_term_id == 8) {
		$posts_per_page = 10;
	} else {
		$posts_per_page = 15;
	}
	
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1; // Current page number

    $args = array(
        'post_type'      => $custom_post_type,
        'post_status'    => 'publish', // Or any other status you need
        'posts_per_page' => $posts_per_page, // Retrieve limited posts per page
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $child_term_ids,
                'operator' => 'IN',
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'ASC',
    );

    $custom_query = new WP_Query($args);

    if ($custom_query->have_posts()) {

        $sectionslist = array();
        $counts = 0;
        while ($custom_query->have_posts()){

            $custom_query->the_post();

            $post_id = get_the_ID();
            $post_title = get_the_title();
            $post_description = get_the_content();
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $demo_url = get_post_meta($post_id, "demo_url", true);
            $download_link = get_post_meta($post_id, "download_link", true);
            $json_template_file = get_field('json_template_file', $post_id);
            $fileurls = $json_template_file['url'];

            $termsss = wp_get_post_terms($post_id, 'layoutscategories' );
            $tarmstags = '';
            foreach ( $termsss as $keyterm ) {
                if($keyterm->slug != $parentslug){
                    $tarmstags .= $keyterm->term_id . " ";
                }
            }
            array_push($sectionslist, array("post_id"=>$post_id, "post_title"=>$post_title, "post_description"=>$post_description, 'termids'=>$tarmstags, 'featured_image_url'=>$featured_image_url, 'demo_url'=>$demo_url, 'download_link'=>$download_link, 'json_template_file'=>$fileurls));
            $counts ++;
        }

        wp_reset_postdata();
        
        // Calculate total pages
        $total_pages = $custom_query->max_num_pages;

        $data = array();
        $data['status'] = 'success';
        $data['totalrecords'] = $custom_query->found_posts;
        $data['totalpages'] = $total_pages;
        $data['currentpage'] = $paged;
        $data['categories'] = $sectionslist;

        echo json_encode($data);

    } else {

        // No posts found
        $data = array();
        $data['status'] = 'sections not found';
        echo json_encode($data);

    }

} else {

    $data = array();
    $data['status'] = 'provide valid term id';
    echo json_encode($data);

}
?>

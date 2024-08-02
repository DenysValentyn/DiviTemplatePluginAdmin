<?php

// Allow from any origin Added By Denys
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if(!empty($_GET['postname'])) {
    include('../../../../wp-load.php');
    header("Content-Type: application/json");

    $postname = sanitize_text_field($_GET['postname']); // Sanitize input
    $parent_id = !empty($_GET['parent_id']) ? intval($_GET['parent_id']) : 0; // Get parent_id if provided, default to 0

    // Pagination parameters
    $posts_per_page = 10; // Number of posts per page
    if($parent_term_id == 6) {
		$posts_per_page = 10;
	} else if($parent_term_id == 8) {
		$posts_per_page = 10;
	} else {
		$posts_per_page = 15;
	}

    $args = array(
        's' => $postname, // Search by title
        'post_type' => 'divilayouts', // Post type
        'post_status' => 'publish', // Post status
        'posts_per_page' => $posts_per_page, // Retrieve limited posts per page
        'paged'          => $paged,
        'posts_per_page' => -1, // Retrieve all posts
        'orderby' => 'date',
        'order' => 'ASC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'layoutscategories', // Ensure this is the correct taxonomy
                'field'    => 'term_id',
                'terms'    => $parent_id,
            ),
        ),
    );

    // Add post_parent argument if parent_id is provided and greater than 0
    
    $custom_query = new WP_Query($args);

    if ($custom_query->have_posts()) {
        $sectionslist = array();
        while ($custom_query->have_posts()) {
            $custom_query->the_post();
            $post_id = get_the_ID();
            $post_title = get_the_title();
            $post_description = get_the_content();
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $demo_url = get_post_meta($post_id, "demo_url", true);
            $download_link = get_post_meta($post_id, "download_link", true);
            $json_template_file = get_field('json_template_file', $post_id);
            $fileurls = $json_template_file['url'];

            array_push($sectionslist, array(
                "post_id" => $post_id,
                "post_title" => $post_title,
                "post_description" => $post_description,
                'featured_image_url' => $featured_image_url,
                'demo_url' => $demo_url,
                'download_link' => $download_link,
                'json_template_file' => $fileurls
            ));
        }
        wp_reset_postdata();
        $total_pages = $custom_query->max_num_pages;

        $data = array();
        $data['status'] = 'success';
        $data['totalrecords'] = $custom_query->found_posts;
        $data['totalpages'] = $total_pages;
        $data['currentpage'] = $paged;

        $data['postdetail'] = $sectionslist;
        echo json_encode($data);
    } else {
        // No posts found
        $data = array();
        $data['status'] = 'sections not found';
        echo json_encode($data);
    }
} else {
    $data = array();
    $data['status'] = 'provide valid POST name';
    echo json_encode($data);
}
?>

<?php

include('../../../../wp-load.php');
header("Content-Type: application/json");
if (!empty($_GET['termid'])) {    
    $termid = intval($_GET['termid']); // Ensure term ID is an integer

    // Debugging: Check the term ID

    $pagenumber = !empty($_GET['postlimit']) ? intval($_GET['postlimit']) : 4; // Ensure post limit is an integer

    // Debugging: Check the page number
    error_log("Page Number: $pagenumber");

    $args = array(
        'post_type'      => 'divilayouts',
        'posts_per_page' => $pagenumber, // Get the last posts based on limit
        'orderby'        => 'date', // Order by date
        'order'          => 'DESC', // Descending order
        'tax_query'      => array(
            array(
                'taxonomy' => 'layoutscategories', // Ensure this is the correct taxonomy
                'field'    => 'term_id',
                'terms'    => $termid,
            ),
        ),
    );

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
            $json_template_file = get_field('json_template_file', $post_id); // Ensure ACF is being used
            $fileurls = $json_template_file['url'];

            // Debugging: Check the post data
            error_log("Post ID: $post_id, Title: $post_title");

            $sectionslist[] = array(
                "post_id" => $post_id,
                "post_title" => $post_title,
                "post_description" => $post_description,
                "featured_image_url" => $featured_image_url,
                "demo_url" => $demo_url,
                "download_link" => $download_link,
                "json_template_file" => $fileurls
            );
        }
        wp_reset_postdata();

        $data = array(
            'status' => 'success',
            'postdata' => $sectionslist
        );
        echo json_encode($data);
    } else {
        // No posts found
        $data = array(
            'status' => 'sections not found'
        );
        echo json_encode($data);
    }
} else {
    $data = array(
        'status' => 'invalid term id'
    );
    echo json_encode($data);
}

?>

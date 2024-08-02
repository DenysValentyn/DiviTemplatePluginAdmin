<?php
if(!empty($_GET['postid'])){
include('../../../../wp-load.php');
header("Content-Type: application/json");

	
	
$postid =$_GET['postid'];
$args = array(
    'p' => $postid, // Post ID to retrieve
    'post_type' => 'divilayouts', // Post type
    'post_status' => 'publish', // Post status
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
        $demo_url = get_post_meta($post_id,"demo_url", true);
        $download_link = get_post_meta($post_id,"download_link", true);
        //$json_template_file = get_post_meta($post_id,"json_template_file", true);
        $json_template_file = get_field('json_template_file', $post_id);
		$fileurls = $json_template_file['url'];
	
		array_push($sectionslist,array("post_id"=>$post_id,"post_title"=>$post_title,"post_description"=>$post_description,'featured_image_url'=>$featured_image_url,'demo_url'=>$demo_url,'download_link'=>$download_link,'json_template_file'=>$fileurls));
    }
    wp_reset_postdata();
	$data = array();
	$data['status']= 'success';		
	$data['postdetail']= $sectionslist;		
	echo json_encode($data);
} else {
    // No posts found
$data = array();
$data['status']= 'sections not found';		
echo json_encode($data);
}
	
}else{
$data = array();
$data['status']= 'provide valid POST id';		
echo json_encode($data);		
}
?>

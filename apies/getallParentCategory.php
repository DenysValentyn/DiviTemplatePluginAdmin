<?php

include('../../../../wp-load.php');

header("Content-Type: application/json");

$custom_post_type = 'divilayouts';

$parent_categories = get_terms(array(

    'taxonomy' => 'layoutscategories', 

    'parent' => 0, // Get only the parent categories

    'hide_empty' => false, // Show empty categories as well

));

$categorylist = array();	 

if (!empty($parent_categories) && !is_wp_error($parent_categories)) {

    foreach ($parent_categories as $parent_category) {
       
		$termid = $parent_category->term_id;
		$categoryname = $parent_category->name;
		$slug = $parent_category->slug;
		$description = $parent_category->description;
        
        
        $imageicon = get_field('layoutscategories_image', 'layoutscategories_' .$termid);
        if(!empty($imageicon['url'])){ $iocnurl  = $imageicon['url']; }else{ $iocnurl = 'none';}
		$count = $parent_category->count;	array_push($categorylist,array("term_id"=>$termid,"categoryname"=>$categoryname,"slug"=>$slug,'productcount'=>$count,"description"=>$description,"iconurl"=>$iocnurl));

    }

  
$data = array();

$data['status']= 'success';		

$data['categories']= $categorylist;		

echo json_encode($data);
    
    
}else{
    
 $data = array();
$data['status']= '404';		
$data['categories']= $categorylist;		
echo json_encode($data); 
}





?>
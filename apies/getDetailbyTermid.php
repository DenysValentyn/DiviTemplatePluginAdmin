<?php

if(!empty($_GET['termid'])){

include('../../../../wp-load.php');

header("Content-Type: application/json");


$parent_term_id = $_GET['termid'];
$term = get_term($parent_term_id,"layoutscategories");    
    
if(!empty($term)){    
//////
	$data = array();
	$data['status']= 'success';		
	$data['term_id']= $term->term_id;		
	$data['term_name']= $term->name;		
	$data['term_slug']=$term->slug;			
	$data['term_count']=$term->count;			
		
	echo json_encode($data);

}else{
$data = array();
$data['status']= '404';		
echo json_encode($data);   
}

}else{

$data = array();
$data['status']= 'provide valid term id';		
echo json_encode($data);		

}

?>


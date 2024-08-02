<?php
/*
Plugin Name: DiviTemp admin
Description: The Ultimate Divi Template Library
Version: 1.1.3
Author: DiviTemp
Author URI: https://divitemptemplates.xyz
Text Domain: https://divitemptemplates.xyz
*/

if ( !defined( 'ABSPATH' ) ) exit;

define ('File_URI',plugins_url('',__FILE__));

define ('File_ROOT',__DIR__);

//include(File_ROOT . "/pages/register-pages.php");



// Act on plugin activation

register_activation_hook( __FILE__, "activate_myplugin" );

// Act on plugin de-activation

register_deactivation_hook( __FILE__, "deactivate_myplugin" );



// Activate Plugin

function activate_myplugin(){		

	 $role = get_role( 'editor' );

   	 $role->add_cap( 'manage_options' ); // capability

}

// De-activate Plugin

function deactivate_myplugin(){

	// Execute tasks on Plugin de-activation

	global $table_prefix, $wpdb;

	$customerTable = $table_prefix . 'student_profile';

	$wpdb->query( "DROP TABLE IF EXISTS ".$customerTable."" );

	$student_docs = $table_prefix . 'student_docs';

	$wpdb->query( "DROP TABLE IF EXISTS ".$student_docs."" );

	$student_applycourses = $table_prefix . 'student_applycourses';

	$wpdb->query( "DROP TABLE IF EXISTS ".$student_applycourses."" );

    

}



//////////////////////////////////////////////



// Custom post



//////////////////////////////////////////////



// Creating a Deals Custom Post Type

function crunchify_deals_custom_post_type() {

	$labels = array(

		'name'                => __( 'DiviTemp Admin' ),

		'singular_name'       => __( 'divi_layouts'),

		'menu_name'           => __( 'DiviTemp Admin'),

		'parent_item_colon'   => __( 'Parent Layouts'),

		'all_items'           => __( 'All Layouts'),

		'view_item'           => __( 'View Layout'),

		'add_new_item'        => __( 'Add New Layout'),

		'add_new'             => __( 'Add New Layout'),

		'edit_item'           => __( 'Edit Layout'),

		'update_item'         => __( 'Update Layout'),

		'search_items'        => __( 'Search Layout'),

		'not_found'           => __( 'Not Found'),

		'not_found_in_trash'  => __( 'Not found in Trash')

	);



	$args = array(

		'label'               => __( 'divilayouts'),

		'description'         => __( 'Divi Layouts'),

		'labels'              => $labels,

		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),

		'public'              => true,

		'hierarchical'        => false,

		'show_ui'             => true,

		'show_in_menu'        => true,

		'show_in_nav_menus'   => true,

		'show_in_admin_bar'   => true,

		'has_archive'         => true,

		'can_export'          => true,

		'exclude_from_search' => false,

		'yarp_support'        => true,

		'publicly_queryable'  => true,

		'capability_type'     => 'page'

	);



	register_post_type( 'divilayouts', $args );

}

add_action( 'init', 'crunchify_deals_custom_post_type', 0 );



// Let us create Taxonomy for Custom Post Type

add_action( 'init', 'crunchify_create_deals_custom_taxonomy', 0 );

//create a custom taxonomy name it "type" for your posts

function crunchify_create_deals_custom_taxonomy() {

  $labels = array(

    'name' => _x( 'Category List', 'taxonomy general name' ),

    'singular_name' => _x( 'layoutscategories', 'taxonomy singular name' ),

    'search_items' =>  __( 'Search Types' ),

    'all_items' => __( 'All Types' ),

    'parent_item' => __( 'Parent Type' ),

    'parent_item_colon' => __( 'Parent Type:' ),

    'edit_item' => __( 'Edit Type' ), 

    'update_item' => __( 'Update Type' ),

    'add_new_item' => __( 'Add New Category' ),

    'new_item_name' => __( 'New Type Category' ),

    'menu_name' => __( 'Category List' ),

  ); 	



  register_taxonomy('layoutscategories',array('divilayouts'), array(

    'hierarchical' => true,

    'labels' => $labels,

    'show_ui' => true,

    'show_admin_column' => true,

    'query_var' => true,

    'rewrite' => array( 'slug' => 'layoutscategories' ),

  ));

}



// Check if ACF plugin is installed

if (function_exists('acf_add_local_field_group')) {

    // Define a new ACF field

    function my_acf_field_setup() {

        acf_add_local_field_group(array(

            'key' => 'group_1',

            'title' => 'Divi Layout Fields',

            'fields' => array(

                array(

                    'key' => 'demo_url',

                    'label' => 'Demo URL',

                    'name' => 'demo_url',

                    'type' => 'text',

                ),

				array(

                    'key' => 'download_link',

                    'label' => 'Download URL',

                    'name' => 'download_link',

                    'type' => 'text',

                ),

				array(

                'key' => 'json_template_file',

                'label' => 'Template File (json Formate)',

                'name' => 'json_template_file',

                'type' => 'file',

                'return_format' => 'array',

                'library' => 'all',

                'mime_types' => 'json', // Restrict to JSON files only

            	)

                // Add more fields as needed

            ),

            'location' => array(

                array(

                    array(

                        'param' => 'post_type',

                        'operator' => '==',

                        'value' => 'divilayouts', // Adjust post type as needed

                    ),

                ),

            ),

        ));

    }


    // Add custom fields to the 'layoutscategories' taxonomy
    acf_add_local_field_group(array(
        'key' => 'group_layoutscategories',
        'title' => 'Layout Categories Fields',
        'fields' => array(
            array(
                'key' => 'field_layoutscategories_image',
                'label' => 'Icon Image',
                'name' => 'layoutscategories_image',
                'type' => 'image',
                'instructions' => 'Add an image for this category.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'layoutscategories',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    // Add custom fields to the 'divilayouts' post type
    acf_add_local_field_group(array(
        'key' => 'group_divilayouts',
        'title' => 'Divi Layouts Fields',
        'fields' => array(
            array(
                'key' => 'field_divilayouts_image',
                'label' => 'Image',
                'name' => 'divilayouts_image',
                'type' => 'image',
                'instructions' => 'Add an image for this layout.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'divilayouts',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    // Hook into ACF initialization

    add_action('acf/init', 'my_acf_field_setup');

}
//////////////////////



////////////////////////
function allow_json_upload( $mime_types ) {

    $mime_types['json'] = 'application/json';

    return $mime_types;

}
add_filter( 'upload_mimes', 'allow_json_upload' );
/////////
function custom_acf_upload_dir( $dir ) {

    // Define your custom upload directory path

    $custom_dir = ABSPATH . '../templatefiles/';



    // Update the upload directory path

    $dir['path'] = $custom_dir . $dir['subdir'];

    

    return $dir;

}

add_filter( 'acf/upload_dir', 'custom_acf_upload_dir', 20 );

?>








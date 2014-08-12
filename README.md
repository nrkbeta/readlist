# Readlist: Easily add links to your Wordpress site

Readlist enables you to easily post links to a custom Wordpress post type from a bookmarklet.

First you have to add a link post type to your themes function.php:

	function link_type() {

		$labels = array(
			'name'                => _x( 'Links', 'Post Type General Name', 'text_domain' ),
			'singular_name'       => _x( 'Link', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'           => __( 'Links', 'text_domain' ),
			'parent_item_colon'   => __( 'Parent Link:', 'text_domain' ),
			'all_items'           => __( 'All Links', 'text_domain' ),
			'view_item'           => __( 'View Link', 'text_domain' ),
			'add_new_item'        => __( 'Add New Link', 'text_domain' ),
			'add_new'             => __( 'Add New', 'text_domain' ),
			'edit_item'           => __( 'Edit Link', 'text_domain' ),
			'update_item'         => __( 'Update Link', 'text_domain' ),
			'search_items'        => __( 'Search Links', 'text_domain' ),
			'not_found'           => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
		);
		$args = array(
			'label'               => __( 'link_type', 'text_domain' ),
			'description'         => __( 'Readlist links', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', ),
			'taxonomies'          => array( 'anbefalt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'link', $args );

	}

	// Hook into the 'init' action
	add_action( 'init', 'link_type', 0 );

Then register for an API account at Readability: https://www.readability.com/developers/api/parser
Then edit some properties in the top of readlist.php, specifically the readability token and the readlist url endpoint.

Afterwards you can visit your blog url with the GET param ?bookmarklet_bookmark=1, and you'll get the bookmarklet needed to post links to your blog. This file will be deleted after you visit the URL.
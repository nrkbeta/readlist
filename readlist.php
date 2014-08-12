<?php
/*
Plugin Name: ReadList
Plugin URI: https://nrkbeta.no
Description: An easy way to publish links to a linklist.
Author: Henrik Lied
Version: 1.0
Requires at least: 2.1
Author URI: https://nrkbeta.no
License: GPL
*/

// Get you token at https://www.readability.com/developers/api/parser
define("READABILITY_TOKEN", "your_token");

// The custom defined URL endpoint where this plugin should be invoked.
// Keep this a secret. Or else everyone can post to your blog.
define("READLIST_URL_ENDPOINT", "your_readlist_endpoint");

function parse_url_domain ($url) {
	$url = str_replace("http://", "", $url);
	$url = str_replace("https://", "", $url);
	$url = explode("/", $url);
	$url = $url[0];
	$bits = explode(".", $url);
	if (count($bits) > 2) {
		$arr = array();
		$arr[] = array_pop($bits);
		$arr[] = array_pop($bits);
		return implode(".", array_reverse($arr));
	}
	return $url;
}

function get_bookmarklet_url() {
	return "javascript:(function(){location.href='".get_site_url()."/?".READLIST_URL_ENDPOINT."='+encodeURIComponent(location.href);})();";
}


function parse_readability($u) {
	if (!$u) {
		return;
	}
	$u = explode("#", $u);
	$u = $u[0];
	$url = "https://www.readability.com/api/content/v1/parser?url=";
	$url .= $u;
	$url .= '&token='.READABILITY_TOKEN;
	$content = file_get_contents($url);
	$content = json_decode($content);
	$title = $content->title;
	if (empty($title)) {
		return "";
	}
	$time = ceil(($content->word_count)/130);
	$url = $content->url;
	$type = 'link';
	$excerpt = '<p>'.trim($content->excerpt);
	$image = $content->lead_image_url;

	
	// Create post object
	$my_post = array(
	  'post_title'    => $title,
	  'post_content'  => $excerpt,
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'	  => $type,
	);


	// Insert the post into the database
	$post_id = wp_insert_post( $my_post );
	add_post_meta($post_id, "link_url", $content->url);
	add_post_meta($post_id, "read_time", $time);

	if ($image) {
		// Add Featured Image to Post
		$image_url  = $image; // Define the image URL here
		$upload_dir = wp_upload_dir(); // Set upload folder
		$image_data = file_get_contents($image_url); // Get image data
		$filename   = basename($image_url); // Create image file name

		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
		    $file = $upload_dir['path'] . '/' . $filename;
		} else {
		    $file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title'     => sanitize_file_name( $filename ),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		// Include image.php
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );

	}
	echo '<style>body { font-family: Helvetica, Arial; }</style>';
	echo "<h1>The link has been posted</h1>";
	echo <<<END
<script>
	function r() {
		window.location="$u";
	}
	setTimeout('r()', 2000);
</script>
END;

}

add_action('parse_request', 'bookmarklet_post');
function bookmarklet_post() {
	$dir = plugin_dir_path( __FILE__ );
	if (file_exists($dir."add_bookmarklet.php") || isset($_GET['bookmarklet_bookmark'])) {
		$content = file_get_contents($dir."add_bookmarklet.php");
		$content = str_replace("BOOKURL", get_bookmarklet_url(), $content);
		echo $content;
		unlink($dir."add_bookmarklet.php");
		exit();
	}
  if( isset($_GET[READLIST_URL_ENDPOINT]) ) {
    parse_readability($_GET[READLIST_URL_ENDPOINT]);
    exit();
  }
}


class readlist extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = __('Betalinks'));
	}
	function form() {
	}
	function update() {
	}
	function widget($args, $instance) {
		// if (!is_user_logged_in()) {
		// 	return '';
		// }
		echo '<aside id="betalinks" class="widget widget_betalinks">';
		echo '<h1 class="widget-title">&#9733; Readlist &nbsp; <a href="'. get_post_type_archive_link( 'link' ) .'">›</a></h1>';
		$a = array('post_type' => 'link', 'posts_per_page' => 5);
		$links = new WP_Query($a);
		if ($links->have_posts()) {
			while ($links->have_posts()) {
				$links->the_post();
				$meta = get_post_meta(get_the_ID());
				echo '<div class="betalink">';
				echo "<!-- ";
				echo $meta['link_url'][0];
				echo " -->";
				echo '<a href="'.$meta['link_url'][0].'">';
				if (has_post_thumbnail()) {
					echo the_post_thumbnail();
				}				
				else {
					echo '<img src="';
					echo bloginfo('template_url').'/images/icon-link.png"';
					echo '>';

				}
				echo '<h3>';
				the_title();
				echo '</a>';
				echo '</h3>';
				echo '<span>'.parse_url_domain($meta['link_url'][0]).' – '.$meta['read_time'][0].' minutes long</span>';	
				echo '</div>';
			}
		} else {
			echo "No posts";
		}
		echo '</aside>';

	}
}
function register_readlist()
{
    register_widget( 'readlist' );
}
add_action( 'widgets_init', 'register_readlist');

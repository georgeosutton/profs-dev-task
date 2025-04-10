<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

function list_recent_posts_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'number' => 5,
            'title'  => 'Recent Posts',
        ),
        $atts,
        'list_recent_posts'
    );

    $number_of_posts = absint( $atts['number'] );
    $list_title      = esc_html( $atts['title'] );

    $recent_posts_query = new WP_Query(
        array(
            'post_type'      => 'post',
            'posts_per_page' => $number_of_posts,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    if ( $recent_posts_query->have_posts() ) {
        $output = '<div>';
        $output .= '<h2>' . $list_title . '</h2>';
        $output .= '<ul>';
        while ( $recent_posts_query->have_posts() ) {
            $recent_posts_query->the_post();
            $output .= '<li>';
            $output .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
            $output .= '<div>';
            $output .= get_the_excerpt();
            $output .= '</div>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        wp_reset_postdata();
        return $output;
    } else {
        return '<p>No recent posts found.</p>';
    }
}
add_shortcode( 'list_recent_posts', 'list_recent_posts_shortcode' );

function like_post_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'post' => get_the_ID(),
		),
		$atts,
		'like_post'
	);

	$post_id = absint( $atts['post'] );

	wp_enqueue_script( 'like-post-script', get_stylesheet_directory_uri() . '/assets/js/like-post.js', array( 'jquery' ), '1.0', true );
	wp_localize_script(
		'like-post-script',
		'likePostData',
		array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'like_post_nonce' ),
			'post_id'   => $post_id,
			'liked_text' => 'Liked!',
		)
	);

	$likes_count = get_post_meta( $post_id, '_post_likes', true );
	$likes_count = $likes_count ? intval( $likes_count ) : 0;

	$output = '<div class="like-post-container">';
	$output .= '<button class="like-button" data-post-id="' . $post_id . '">Like</button>';
	$output .= '<span class="likes-count">(';
	if ( $likes_count == 1 ) {
		$output .= esc_html( $likes_count ) . ' like';
	} else {
		$output .= esc_html( $likes_count ) . ' likes';
	}
	$output .= ')</span>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'like_post', 'like_post_shortcode' );

/**
 * AJAX handler to process the like action.
 */
function process_like_post() {
	check_ajax_referer( 'like_post_nonce', 'nonce' );

	if ( isset( $_POST['post_id'] ) ) {
		$post_id = absint( $_POST['post_id'] );

		$likes_count = get_post_meta( $post_id, '_post_likes', true );
		$likes_count = $likes_count ? intval( $likes_count ) + 1 : 1;

		update_post_meta( $post_id, '_post_likes', $likes_count );

		wp_send_json_success( array( 'likes' => $likes_count ) );
	} else {
		wp_send_json_error( 'No post ID provided.' );
	}

	die();
}
add_action( 'wp_ajax_like_post', 'process_like_post' );
add_action( 'wp_ajax_nopriv_like_post', 'process_like_post' );
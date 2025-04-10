<?php

// Problem #1 - related content
function display_related_content_widget() {

    // Get the current post ID in a more reliable way
    $current_post_id = get_queried_object_id();
    if (!is_numeric($current_post_id) || $current_post_id < 1) {
        return;
    }

    $cats = wp_get_post_categories($current_post_id);

    if ( $cats ) {
        $args = [
            'post_type'             => 'post',
            'posts_per_page'        => 2,
            'category__in'          => $cats,
            'post__not_in'          => [$current_post_id],
        ];
        $related_query = new WP_Query($args);

        if ($related_query->have_posts()) {
            echo '<h4>Related Content</h4>';
            echo '<ul>';
            while ($related_query->have_posts()) {
                $related_query->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
            // Restore original post data
            wp_reset_postdata();
        }
        // Condition to handle cases with no related posts
        else {
            echo '<p>No related content found.</p>';
        }
    }
    // Add an else condition if the post has no categories
    else {
        echo '<p>No categories for this post.</p>';
    }
}

// Problem #2 - script enqueueing
function enqueue_theme_scripts() {
    wp_enqueue_script(
        'my-script',
        get_template_directory_uri(). '/js/my-script.js',
        array( 'jquery' ), // Depends on jquery
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_theme_scripts' ); // Use the correct hook

// Problem #3 - pre_get_posts
function modify_portfolio_archive_query( $query ) {
	if ( $query->is_main_query() && ! is_admin() && is_post_type_archive( 'portfolio' ) ) {
		$query->set( 'post_type', 'portfolio' );
		$query->set( 'posts_per_page', 20 );
	}

}
add_action( 'pre_get_posts', 'modify_portfolio_archive_query' );

?>
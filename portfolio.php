<?php
if ( ! function_exists('portfolio_shortcode') ) {

    function portfolio_shortcode() {
    	$args   =   array(
                	'post_type'         =>  'portfolio',
                	'post_status'       =>  'publish',
                	'order' => 'ASC',
                	'posts_per_page' => 10,
    	            );
    	            
        $postslist = new WP_Query( $args );
        global $post;

        if ( $postslist->have_posts() ) :
        $portfolio   .= '<ul class="portfolio-lists">';
		
            while ( $postslist->have_posts() ) : $postslist->the_post();         
                $portfolio    .= '<li class="items">';
                $portfolio    .= '<a href="'. get_permalink() .'">'. get_the_title() .'</a>';
                $portfolio    .= '</li>';            
            endwhile;
            wp_reset_postdata();
            $portfolio  .= '</ul>';			
        endif;    
        return $portfolio;
    }
    add_shortcode( 'portfolio', 'portfolio_shortcode' );
}
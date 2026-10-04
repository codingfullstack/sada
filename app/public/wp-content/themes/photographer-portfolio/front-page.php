<?php
/**
 * Front page template.
 *
 * @package Photographer_Portfolio
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/front-page/content' );
	endwhile;
endif;

get_footer();

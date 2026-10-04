<?php
/**
 * Single post template.
 *
 * @package Photographer_Portfolio
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="content-section">
		<div class="content-section__inner">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content' );

				the_post_navigation(
					array(
						'prev_text' => esc_html__( 'Ankstesnis', 'photographer-portfolio' ),
						'next_text' => esc_html__( 'Kitas', 'photographer-portfolio' ),
					)
				);
			endwhile;
			?>
		</div>
	</section>
</main>

<?php
get_footer();

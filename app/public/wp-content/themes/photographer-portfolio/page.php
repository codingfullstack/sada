<?php
/**
 * Page template.
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
			endwhile;
			?>
		</div>
	</section>
</main>

<?php
get_footer();

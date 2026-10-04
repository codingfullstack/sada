<?php
/**
 * Archive template.
 *
 * @package Photographer_Portfolio
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="content-section">
		<div class="content-section__inner">
			<header class="archive-header">
				<?php the_archive_title( '<h1 class="archive-header__title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="archive-header__description">', '</div>' ); ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content' );
				endwhile;

				the_posts_navigation(
					array(
						'prev_text' => esc_html__( 'Ankstesni darbai', 'photographer-portfolio' ),
						'next_text' => esc_html__( 'Naujesni darbai', 'photographer-portfolio' ),
					)
				);
				?>
			<?php else : ?>
				<p class="editorial-text editorial-text--short"><?php esc_html_e( 'Archyvo įrašų nerasta.', 'photographer-portfolio' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();

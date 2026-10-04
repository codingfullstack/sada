<?php
/**
 * Default content template part.
 *
 * @package Photographer_Portfolio
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="entry__header">
		<?php
		if ( is_singular() ) {
			the_title( '<h1 class="entry__title">', '</h1>' );
		} else {
			the_title( '<h2 class="entry__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry__media">
			<?php the_post_thumbnail( 'large' ); ?>
		</figure>
	<?php endif; ?>

	<div class="entry__content editorial-text">
		<?php
		if ( is_singular() ) {
			the_content();
		} else {
			the_excerpt();
		}

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Puslapio nuorodos', 'photographer-portfolio' ) . '">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>
</article>

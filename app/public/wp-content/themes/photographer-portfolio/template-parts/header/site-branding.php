<?php
/**
 * Site branding template part.
 *
 * @package Photographer_Portfolio
 */

?>
<div class="site-branding">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<a class="site-branding__title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>
	<?php endif; ?>
</div>

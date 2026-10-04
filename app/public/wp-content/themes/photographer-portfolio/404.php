<?php
/**
 * 404 template.
 *
 * @package Photographer_Portfolio
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="content-section">
		<div class="content-section__inner content-section__inner--narrow">
			<p class="section-kicker"><?php esc_html_e( '404', 'photographer-portfolio' ); ?></p>
			<h1 class="entry__title"><?php esc_html_e( 'Kadras nerastas', 'photographer-portfolio' ); ?></h1>
			<p class="editorial-text editorial-text--short"><?php esc_html_e( 'Ieškomas puslapis nepasiekiamas. Grįžkite į pradžios puslapį ir tęskite iš ten.', 'photographer-portfolio' ); ?></p>
			<p>
				<a class="text-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Grįžti į pradžią', 'photographer-portfolio' ); ?>
				</a>
			</p>
		</div>
	</section>
</main>

<?php
get_footer();

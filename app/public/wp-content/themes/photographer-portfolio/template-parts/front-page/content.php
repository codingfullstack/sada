<?php
/**
 * Editable front page content.
 *
 * @package Photographer_Portfolio
 */

$front_content            = is_page() ? get_the_content() : '';
$front_title              = __( 'sada', 'photographer-portfolio' );
$front_preview_image_ids = array_slice( photographer_portfolio_get_editor_image_ids( $front_content ), 0, 3 );
?>

<main id="primary" class="site-main site-main--front">
	<section class="front-demo" aria-labelledby="front-demo-title">
		<div class="front-demo__inner">
			<div class="front-demo__copy">
				<h1 id="front-demo-title" class="front-demo__heading"><?php esc_html_e( 'Fotografė Lietuvoje.', 'photographer-portfolio' ); ?></h1>
				<p class="front-demo__intro editorial-text editorial-text--short">
					<?php esc_html_e( 'Istorijos, pagaunamos', 'photographer-portfolio' ); ?><br>
					<?php esc_html_e( 'šviesa ir emocija.', 'photographer-portfolio' ); ?>
				</p>

				<a class="front-demo__contact" href="<?php echo esc_url( photographer_portfolio_get_page_url( 'kontaktai', 'page-contact.php' ) ); ?>">
					<?php esc_html_e( 'Susisiekite', 'photographer-portfolio' ); ?>
					<span aria-hidden="true">-></span>
				</a>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="front-demo__media" aria-hidden="true">
					<?php
					the_post_thumbnail(
						'portfolio_hero',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(max-width: 760px) calc(100vw - 28px), (max-width: 1200px) 46vw, 720px',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="front-demo__title" aria-hidden="true"><?php echo esc_html( $front_title ); ?></div>
		</div>
	</section>

	<?php if ( $front_preview_image_ids ) : ?>
		<section class="front-preview" aria-label="<?php esc_attr_e( 'Portfelio peržiūra', 'photographer-portfolio' ); ?>">
			<div class="front-preview__inner">
				<?php foreach ( $front_preview_image_ids as $front_preview_image_id ) : ?>
					<figure class="front-preview__item">
						<?php
						echo wp_get_attachment_image(
							$front_preview_image_id,
							'portfolio_hero',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 760px) calc(100vw - 28px), 31vw',
							)
						);
						?>
					</figure>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
</main>

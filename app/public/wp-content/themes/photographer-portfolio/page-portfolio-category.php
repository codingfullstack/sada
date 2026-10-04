<?php
/**
 * Template Name: Portfolio Category
 * Template Post Type: page
 *
 * Reusable editorial gallery template for Portfolio child categories.
 *
 * @package Photographer_Portfolio
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id     = get_the_ID();
	$title       = get_the_title();
	$has_intro   = '' !== trim( wp_strip_all_tags( get_the_content() ) );
	$gallery_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $post_id, 'gallery_ids' ) );
	$contact_url = photographer_portfolio_get_page_url( 'kontaktai', 'page-contact.php' );
	?>

	<main id="primary" class="site-main site-main--portfolio-page">
		<article class="portfolio-category-page" aria-labelledby="portfolio-category-title">
			<header class="portfolio-category-hero">
				<div class="portfolio-page-shell portfolio-category-hero__inner">
					<div class="portfolio-category-hero__copy">
						<h1 id="portfolio-category-title" class="portfolio-category-hero__title"><?php the_title(); ?></h1>
						<?php if ( $has_intro ) : ?>
							<div class="portfolio-category-hero__description editorial-text">
								<?php the_content(); ?>
								<p class="portfolio-category-hero__cta-wrap">
									<a class="portfolio-category-hero__cta" href="<?php echo esc_url( $contact_url ); ?>">
										<span><?php esc_html_e( 'Susisiekite dėl fotografavimo', 'photographer-portfolio' ); ?></span>
										<span class="portfolio-category-hero__cta-arrow" aria-hidden="true">-></span>
									</a>
								</p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</header>

			<?php if ( $gallery_ids ) : ?>
				<section class="portfolio-category-gallery-section" aria-label="<?php echo esc_attr( sprintf( __( '%s fotografijų galerija', 'photographer-portfolio' ), $title ) ); ?>">
					<div class="portfolio-page-shell">
						<div class="portfolio-category-gallery" data-portfolio-lightbox-gallery>
							<?php foreach ( $gallery_ids as $index => $attachment_id ) : ?>
								<?php
								$full_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );

								if ( ! $full_image_url ) {
									continue;
								}

								$image_title = get_the_title( $attachment_id );
								?>
								<figure class="portfolio-category-gallery__item">
									<a href="<?php echo esc_url( $full_image_url ); ?>" data-portfolio-lightbox data-portfolio-lightbox-title="<?php echo esc_attr( $image_title ); ?>">
										<?php
										echo wp_get_attachment_image(
											$attachment_id,
											'large',
											false,
											array(
												'loading' => $index < 2 ? 'eager' : 'lazy',
												'sizes'   => '(max-width: 700px) calc(100vw - 28px), (max-width: 1024px) calc((100vw - 7vw - 12px) / 2), calc((100vw - 7vw - 28px) / 3)',
											)
										);
										?>
									</a>
									<?php if ( $image_title ) : ?>
										<figcaption><?php echo esc_html( $image_title ); ?></figcaption>
									<?php endif; ?>
								</figure>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
			<?php elseif ( current_user_can( 'edit_post', $post_id ) ) : ?>
				<section class="portfolio-category-gallery-section">
					<div class="portfolio-page-shell">
						<p class="portfolio-category-empty"><?php esc_html_e( 'Portfelio galerija tuščia. Pridėkite nuotraukas šio puslapio redagavimo lange.', 'photographer-portfolio' ); ?></p>
					</div>
				</section>
			<?php endif; ?>
		</article>
	</main>

	<?php
endwhile;

get_footer();

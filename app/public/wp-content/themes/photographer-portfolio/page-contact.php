<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact page slug template.
 *
 * @package Photographer_Portfolio
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<main id="primary" class="site-main site-main--contact-page">
		<article class="contact-page" aria-labelledby="contact-page-title">
			<div class="contact-page-shell">
				<section class="contact-panel" aria-label="<?php esc_attr_e( 'Kontaktų puslapis', 'photographer-portfolio' ); ?>">
					<div class="contact-panel__content">
						<h1 id="contact-page-title" class="contact-panel__title"><?php esc_html_e( 'KONTAKTAI', 'photographer-portfolio' ); ?></h1>

						<div class="contact-panel__grid">
							<div class="contact-panel__intro">
								<p class="editorial-text editorial-text--short"><?php esc_html_e( 'Dėl fotosesijų, renginių, koncertų, švenčių ar kūrybinių projektų fotografavimo parašykite ir papasakokite, ką kuriate.', 'photographer-portfolio' ); ?></p>
							</div>

							<div class="contact-panel__details" aria-label="<?php esc_attr_e( 'Kontaktinė informacija', 'photographer-portfolio' ); ?>">
								<div class="contact-detail">
									<h2 class="contact-detail__label"><?php esc_html_e( 'Bendros užklausos', 'photographer-portfolio' ); ?></h2>
									<a class="contact-detail__value" href="mailto:sada.photographyy@gmail.com"><?php esc_html_e( 'sada.photographyy@gmail.com', 'photographer-portfolio' ); ?></a>
									<a class="contact-detail__value" href="tel:+37062392255"><?php esc_html_e( '+37062392255', 'photographer-portfolio' ); ?></a>
								</div>

								<div class="contact-detail">
									<h2 class="contact-detail__label"><?php esc_html_e( 'Atvira darbams', 'photographer-portfolio' ); ?></h2>
									<nav class="contact-detail__value contact-detail__services" aria-label="<?php esc_attr_e( 'Fotografijos paslaugos', 'photographer-portfolio' ); ?>">
										<?php
										$fotosesijos_page = get_page_by_path( 'fotosesijos' );
										$renginiai_page   = get_page_by_path( 'renginiai' );
										$koncertai_page   = get_page_by_path( 'koncertai' );

										$service_links = array(
											'Fotosesijos' => $fotosesijos_page instanceof WP_Post && 'publish' === $fotosesijos_page->post_status ? get_permalink( $fotosesijos_page ) : '',
											'Renginiai'  => $renginiai_page instanceof WP_Post && 'publish' === $renginiai_page->post_status ? get_permalink( $renginiai_page ) : '',
											'Koncertai'  => $koncertai_page instanceof WP_Post && 'publish' === $koncertai_page->post_status ? get_permalink( $koncertai_page ) : '',
										);

										$service_items = array(
											'Fotosesijos',
											'Renginiai',
											'Koncertai',
											'Projektai',
											'Šventės',
										);

										foreach ( $service_items as $index => $service_item ) :
											if ( 0 < $index ) :
												?>
												<span aria-hidden="true"> / </span>
												<?php
											endif;

											if ( ! empty( $service_links[ $service_item ] ) ) :
												?>
												<a href="<?php echo esc_url( $service_links[ $service_item ] ); ?>"><?php echo esc_html( $service_item ); ?></a>
												<?php
											else :
												echo esc_html( $service_item );
											endif;
										endforeach;
										?>
									</nav>
								</div>

								<div class="contact-detail contact-detail--social">
									<h2 class="contact-detail__label"><?php esc_html_e( 'Sekite', 'photographer-portfolio' ); ?></h2>
									<p class="contact-detail__social">
										<a href="https://www.instagram.com/sada.photography__" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'SADA Photography Instagram', 'photographer-portfolio' ); ?>">
											<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
												<path d="M7.75 2h8.5C19.42 2 22 4.58 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm5.25-2.55a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z" />
											</svg>
										</a>
										<a href="https://www.facebook.com/profile.php?id=61586519211741" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'SADA Photography Facebook', 'photographer-portfolio' ); ?>">
											<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
												<path d="M14.2 8.1V6.6c0-.72.48-.89.82-.89h2.08V2.2L14.23 2.19c-3.18 0-3.9 2.38-3.9 3.9v2.01H7.9v3.95h2.43V22h3.97v-9.95h2.96l.39-3.95H14.2Z" />
											</svg>
										</a>
									</p>
								</div>
							</div>
						</div>
					</div>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="contact-panel__media">
							<?php
							the_post_thumbnail(
								'portfolio_hero',
								array(
									'class'         => 'contact-panel__image',
									'loading'       => 'eager',
									'fetchpriority' => 'high',
									'sizes'         => '(max-width: 760px) calc(100vw - 28px), (max-width: 1200px) 34vw, 560px',
								)
							);
							?>
						</figure>
					<?php endif; ?>
				</section>
			</div>
		</article>
	</main>

	<?php
endwhile;

get_footer();

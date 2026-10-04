<?php
/**
 * Template Name: Portfolio
 * Template Post Type: page
 *
 * Editorial admin-managed portfolio gallery.
 *
 * @package Photographer_Portfolio
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gallery_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( get_the_ID(), 'gallery_ids' ) );
	$hero_id     = photographer_portfolio_get_portfolio_hero_image_id( get_the_ID() );
	$intro_text  = has_excerpt() ? get_the_excerpt() : trim( wp_strip_all_tags( get_the_content() ) );
	$intro_text  = $intro_text ? $intro_text : esc_html__( 'Vizuali kelionė per žmones, vietas ir emocijas.', 'photographer-portfolio' );
	$category_pages = photographer_portfolio_get_category_pages();
	$category_links = array();
	$portfolio_categories = array();

	foreach ( $category_pages as $category_page ) {
		$category_url   = get_permalink( $category_page );
		$category_title = get_the_title( $category_page );

		if ( $category_url && $category_title ) {
			$category_links[ sanitize_title( $category_title ) ]      = $category_url;
			$category_links[ sanitize_title( $category_page->post_name ) ] = $category_url;
		}

		$preview_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $category_page->ID, 'featured_ids' ) );

		if ( ! $preview_ids ) {
			$preview_ids = array_slice(
				photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $category_page->ID, 'gallery_ids' ) ),
				0,
				3
			);
		}

		if ( ! $preview_ids ) {
			continue;
		}

		$portfolio_categories[] = array(
			'page'        => $category_page,
			'preview_ids' => array_slice( $preview_ids, 0, 3 ),
		);
	}

	$render_category_nav = static function ( $items, $aria_label ) use ( $category_links ) {
		?>
		<nav class="portfolio-inline-nav editorial-text editorial-text--short" aria-label="<?php echo esc_attr( $aria_label ); ?>">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$label = $item['label'];
				$keys  = array_unique(
					array_filter(
						array_map(
							'sanitize_title',
							array_merge( array( $label ), isset( $item['slugs'] ) ? (array) $item['slugs'] : array() )
						)
					)
				);
				$url   = '';

				foreach ( $keys as $key ) {
					if ( isset( $category_links[ $key ] ) ) {
						$url = $category_links[ $key ];
						break;
					}
				}
				?>
				<?php if ( 0 < $index ) : ?>
					<span aria-hidden="true"> / </span>
				<?php endif; ?>
				<?php if ( $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php else : ?>
					<span><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>
		<?php
	};
	?>

	<main id="primary" class="site-main site-main--portfolio-page">
		<article class="portfolio-index">
			<header class="portfolio-editorial-hero">
				<div class="portfolio-page-shell">
					<figure class="portfolio-editorial-hero__figure">
						<?php
						if ( $hero_id ) {
							echo wp_get_attachment_image(
								$hero_id,
								'portfolio_hero',
								false,
								array(
									'class'   => 'portfolio-editorial-hero__image',
									'loading' => 'eager',
									'sizes'   => '(max-width: 1920px) calc(100vw - 7vw), 1920px',
								)
							);
						}
						?>
						<span class="portfolio-editorial-hero__mark" aria-hidden="true"></span>
						<p class="portfolio-editorial-hero__side-note"><?php esc_html_e( 'Žmonės vietos istorijos tarp jų', 'photographer-portfolio' ); ?></p>
					</figure>
				</div>
			</header>

			<section class="portfolio-intro-panel" aria-label="<?php esc_attr_e( 'Portfelio įžanga', 'photographer-portfolio' ); ?>">
				<div class="portfolio-page-shell portfolio-intro-panel__grid">
					<div class="portfolio-intro-panel__statement">
						<h1 class="portfolio-intro-panel__title"><?php esc_html_e( 'Fotografijos portfolio', 'photographer-portfolio' ); ?></h1>
						<p class="editorial-text"><?php echo esc_html( $intro_text ); ?></p>
					</div>
					<div class="portfolio-intro-panel__meta">
						<h2><?php esc_html_e( 'Atrinkti darbai', 'photographer-portfolio' ); ?></h2>
						<?php
						$render_category_nav(
							array(
								array(
									'label' => __( 'Fotosesijos', 'photographer-portfolio' ),
									'slugs' => array( 'fotosesijos' ),
								),
								array(
									'label' => __( 'koncertai', 'photographer-portfolio' ),
									'slugs' => array( 'koncertai' ),
								),
								array(
									'label' => __( 'renginiai', 'photographer-portfolio' ),
									'slugs' => array( 'renginiai' ),
								),
								array(
									'label' => __( 'užkulisiai', 'photographer-portfolio' ),
									'slugs' => array( 'uzkulisiai', 'uzkuliusiai' ),
								),
								array(
									'label' => __( 'vestuvės', 'photographer-portfolio' ),
									'slugs' => array( 'vestuves' ),
								),
							),
							__( 'Atrinktų darbų kategorijos', 'photographer-portfolio' )
						);
						?>
					</div>
				</div>
			</section>

			<?php if ( $portfolio_categories ) : ?>
				<div class="portfolio-category-previews">
					<?php foreach ( $portfolio_categories as $category ) : ?>
						<?php
						$category_page = $category['page'];
						$category_url  = get_permalink( $category_page );
						?>
						<section class="portfolio-featured-section" aria-labelledby="portfolio-category-preview-<?php echo esc_attr( $category_page->post_name ); ?>">
							<div class="portfolio-page-shell">
								<div class="portfolio-featured-section__header">
									<h2 id="portfolio-category-preview-<?php echo esc_attr( $category_page->post_name ); ?>"><?php echo esc_html( get_the_title( $category_page ) ); ?></h2>
									<a href="<?php echo esc_url( $category_url ); ?>">
										<span><?php esc_html_e( 'Peržiūrėti visus', 'photographer-portfolio' ); ?></span>
										<span aria-hidden="true">-&gt;</span>
									</a>
								</div>

								<div class="portfolio-featured-grid">
									<?php foreach ( $category['preview_ids'] as $index => $attachment_id ) : ?>
										<?php
										$image_title = get_the_title( $attachment_id );
										?>
										<figure class="portfolio-featured-grid__item">
											<a href="<?php echo esc_url( $category_url ); ?>">
												<?php
												echo wp_get_attachment_image(
													$attachment_id,
													'portfolio_hero',
													false,
													array(
														'loading' => 'lazy',
														'sizes'   => '(max-width: 700px) calc(100vw - 28px), calc((min(100vw, 1920px) - 7vw - 32px) / 3)',
													)
												);
												?>
												<span class="portfolio-gallery__caption">
													<span><?php echo esc_html( $image_title ); ?></span>
													<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
												</span>
											</a>
										</figure>
									<?php endforeach; ?>
								</div>
							</div>
						</section>
					<?php endforeach; ?>
				</div>
			<?php elseif ( current_user_can( 'edit_post', get_the_ID() ) ) : ?>
				<section class="portfolio-featured-section">
					<div class="portfolio-page-shell">
						<p class="portfolio-category-empty">
							<?php
							esc_html_e(
								'Sukurkite portfelio kategorijų puslapius ir parinkite po 3 pagrindines nuotraukas jų redagavimo lange.',
								'photographer-portfolio'
							);
							?>
						</p>
					</div>
				</section>
			<?php endif; ?>
		</article>
	</main>

	<?php
endwhile;

get_footer();

<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * Editorial about page template.
 *
 * @package Photographer_Portfolio
 */

get_header();

while ( have_posts() ) :
	the_post();

	$portfolio_url = photographer_portfolio_get_portfolio_url();
	?>

	<main id="primary" class="site-main site-main--about-page">
		<article class="about-page" aria-labelledby="about-page-title">
			<header class="about-hero">
				<div class="about-page-shell">
					<h1 id="about-page-title" class="about-hero__title">
						<?php esc_html_e( 'Apie mane', 'photographer-portfolio' ); ?>
					</h1>
				</div>
			</header>

			<section class="about-editorial" aria-label="<?php esc_attr_e( 'Apie SADA fotografiją', 'photographer-portfolio' ); ?>">
				<div class="about-page-shell about-editorial__grid">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="about-editorial__portrait">
							<?php
							the_post_thumbnail(
								'portfolio_cover',
								array(
									'class'   => 'about-editorial__image',
									'loading' => 'lazy',
									'sizes'   => '(max-width: 720px) calc(100vw - 32px), (max-width: 1200px) 44vw, 620px',
								)
							);
							?>
						</figure>
					<?php endif; ?>

					<div class="about-editorial__copy">
						<h2 class="about-editorial__eyebrow"><?php esc_html_e( 'Julija Teresė Sadauskytė / vizualinis archyvas', 'photographer-portfolio' ); ?></h2>

						<div class="about-editorial__content editorial-text">
							<?php if ( trim( wp_strip_all_tags( get_the_content() ) ) ) : ?>
								<?php the_content(); ?>
							<?php else : ?>
								<p><?php esc_html_e( 'Esu Julija Teresė Sadauskytė — pradedančioji fotografė, dirbanti su žmonėmis, judesiu, spalvomis, muzika ir tuo mažu chaosu, kuris atsiranda prieš akimirkai tampant prisiminimu.', 'photographer-portfolio' ); ?></p>
								<p><?php esc_html_e( 'Man patinka tai, kas ryšku, įdomu ir šiek tiek paradoksalu. Fotografijoje labiau ieškau charakterio nei tobulumo — tikrų išraiškų, įdomios šviesos ir detalių, kurios netikėtai sukuria visą vaizdą.', 'photographer-portfolio' ); ?></p>
								<p><?php esc_html_e( 'Spalvos man yra viena svarbiausių fotografijos dalių. Kartais noriu, kad kadras būtų ryškus ir gyvas, o kartais – turėtų tapybai būdingą estetiką.', 'photographer-portfolio' ); ?></p>
								<p><?php esc_html_e( 'Sàda – yra mano vizualinis archyvas, kuriame susitinka portretai, muzika, renginiai ir kasdienybės fragmentai. Čia viskas gali būti apgalvota, bet nebūtinai surežisuota. Man įdomiausia tai, kas atrodo tikra, netikėta ir gyva.', 'photographer-portfolio' ); ?></p>
								<p><?php esc_html_e( 'Vis dar mokausi, eksperimentuoju ir ieškau savo žvilgsnio. Visada laukiu įdomių žmonių, komandų, projektų ir istorijų, kurias būtų galima įamžinti savaip...', 'photographer-portfolio' ); ?></p>
							<?php endif; ?>
							<p class="about-editorial__cta-wrap">
								<a class="about-editorial__cta" href="<?php echo esc_url( $portfolio_url ); ?>">
									<span><?php esc_html_e( 'PERŽIŪRĖTI DARBUS', 'photographer-portfolio' ); ?></span>
									<span class="about-editorial__cta-arrow" aria-hidden="true">-></span>
								</a>
							</p>
						</div>
					</div>
				</div>
			</section>

			<footer class="about-closing" aria-label="<?php esc_attr_e( 'SADA baigiamoji mintis', 'photographer-portfolio' ); ?>">
				<div class="about-page-shell">
					<blockquote class="about-closing__quote">
						<p><?php esc_html_e( 'Kiekviena fotografija išsaugo akimirką gyvą.', 'photographer-portfolio' ); ?></p>
					</blockquote>
					<p class="about-closing__brand">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html_e( 'SADA', 'photographer-portfolio' ); ?></a>
					</p>
				</div>
			</footer>
		</article>
	</main>

	<?php
endwhile;

get_footer();

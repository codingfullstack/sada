<?php
/**
 * Rank Math compatibility.
 *
 * @package Photographer_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds theme-rendered template content to Rank Math's Content Analysis API.
 *
 * Rank Math loads this file only when the plugin is active. The script runs in
 * the editor and does not change the public front-end markup.
 *
 * @param string $hook_suffix Current admin page.
 */
function photographer_portfolio_rank_math_enqueue_content_analysis( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$analysis_data = photographer_portfolio_rank_math_get_analysis_data( $post );

	if ( empty( $analysis_data['content'] ) ) {
		return;
	}

	wp_enqueue_script(
		'photographer-portfolio-rank-math-content-analysis',
		get_theme_file_uri( 'assets/js/rank-math-content-analysis.js' ),
		array( 'wp-hooks', 'rank-math-analyzer' ),
		photographer_portfolio_asset_version( 'assets/js/rank-math-content-analysis.js' ),
		true
	);

	wp_localize_script(
		'photographer-portfolio-rank-math-content-analysis',
		'photographerPortfolioRankMath',
		$analysis_data
	);
}
add_action( 'admin_enqueue_scripts', 'photographer_portfolio_rank_math_enqueue_content_analysis', 20 );

/**
 * Uses template-rendered page text as a fallback for Rank Math meta descriptions.
 *
 * @param string $description Current Rank Math description.
 * @return string
 */
function photographer_portfolio_rank_math_frontend_description( $description ) {
	if ( $description || ! is_singular( 'page' ) ) {
		return $description;
	}

	$post = get_queried_object();

	if ( ! $post instanceof WP_Post ) {
		return $description;
	}

	$analysis_data = photographer_portfolio_rank_math_get_analysis_data( $post );
	$content       = $analysis_data['content'] ?? '';

	if ( ! $content ) {
		return $description;
	}

	return wp_html_excerpt( wp_strip_all_tags( $content ), 155, '...' );
}
add_filter( 'rank_math/frontend/description', 'photographer_portfolio_rank_math_frontend_description' );

/**
 * Removes article schema from non-article editorial pages.
 *
 * @param array $data Rank Math JSON-LD graph data.
 * @return array
 */
function photographer_portfolio_rank_math_page_json_ld( $data ) {
	$post = get_queried_object();

	if (
		! is_singular( 'page' )
		|| ! $post instanceof WP_Post
		|| (
			! is_page( array( 'contact', 'kontaktai', 'portfolio' ) )
			&& 'page-portfolio.php' !== get_page_template_slug( $post )
		)
	) {
		return $data;
	}

	foreach ( $data as $key => $entity ) {
		if ( empty( $entity['@type'] ) ) {
			continue;
		}

		$types = (array) $entity['@type'];

		if ( array_intersect( $types, array( 'Article', 'BlogPosting', 'NewsArticle' ) ) ) {
			unset( $data[ $key ] );
		}
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'photographer_portfolio_rank_math_page_json_ld', 100 );

/**
 * Returns the content that PHP templates add outside the page editor body.
 *
 * @param WP_Post $post Current page post.
 * @return array
 */
function photographer_portfolio_rank_math_get_analysis_data( $post ) {
	$template = get_page_template_slug( $post );
	$slug     = $post->post_name;
	$content  = array();

	if ( (int) get_option( 'page_on_front' ) === (int) $post->ID || 'page-front-page.php' === $template || 'front-page' === $slug ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_front_page_content( $post ) );
	}

	if ( 'page-portfolio.php' === $template || 'portfolio' === $slug ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_portfolio_content( $post ) );
	}

	if ( function_exists( 'photographer_portfolio_is_category_page' ) && photographer_portfolio_is_category_page( $post ) ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_portfolio_category_content( $post ) );
	}

	if ( 'page-about.php' === $template || in_array( $slug, array( 'about', 'apie-mane' ), true ) ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_about_content( $post ) );
	}

	if ( 'page-contact.php' === $template || in_array( $slug, array( 'contact', 'kontaktai' ), true ) ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_contact_content() );
	}

	$content = array_filter( array_map( 'photographer_portfolio_rank_math_clean_content_fragment', $content ) );

	return array(
		'content' => implode( "\n\n", array_unique( $content ) ),
	);
}

/**
 * Returns front-page template content generated from editor blocks and fallbacks.
 *
 * @param WP_Post $post Current page post.
 * @return array
 */
function photographer_portfolio_rank_math_get_front_page_content( $post ) {
	$post_content = (string) $post->post_content;
	$content      = array();
	$title        = photographer_portfolio_get_first_editor_block_text( $post_content, 'core/heading' );
	$intro        = photographer_portfolio_get_first_editor_block_text( $post_content, 'core/paragraph' );

	$content[] = $title ? $title : __( 'sada', 'photographer-portfolio' );
	$content[] = $intro ? $intro : __( 'Fotografija Lietuvoje. Istorijos, pagaunamos šviesa ir emocija.', 'photographer-portfolio' );

	if ( ! photographer_portfolio_get_first_editor_block( $post_content, 'core/button' ) ) {
		$content[] = __( 'Susisiekite', 'photographer-portfolio' );
	}

	$content[] = __( 'Portfelio peržiūra', 'photographer-portfolio' );
	$content   = array_merge( $content, photographer_portfolio_rank_math_get_attachment_texts( photographer_portfolio_get_editor_image_ids( $post_content ) ) );

	return $content;
}

/**
 * Returns portfolio index template content.
 *
 * @param WP_Post $post Current page post.
 * @return array
 */
function photographer_portfolio_rank_math_get_portfolio_content( $post ) {
	$content = array();
	$hero_id = function_exists( 'photographer_portfolio_get_portfolio_hero_image_id' )
		? photographer_portfolio_get_portfolio_hero_image_id( $post->ID )
		: 0;

	if ( has_excerpt( $post ) ) {
		$content[] = get_the_excerpt( $post );
	} elseif ( ! trim( wp_strip_all_tags( $post->post_content ) ) ) {
		$content[] = __( 'Vizuali kelionė per žmones, vietas ir emocijas.', 'photographer-portfolio' );
	}

	if ( $hero_id ) {
		$content = array_merge( $content, photographer_portfolio_rank_math_get_attachment_texts( array( $hero_id ) ) );
	}

	$content[] = __( 'Žmonės vietos istorijos tarp jų', 'photographer-portfolio' );
	$content[] = __( 'Fotografijos portfolio', 'photographer-portfolio' );
	$content[] = __( 'Portfelio įžanga', 'photographer-portfolio' );
	$content[] = __( 'Atrinkti darbai', 'photographer-portfolio' );
	$content[] = __( 'Fotosesijos / koncertai / renginiai / užkulisiai / vestuvės', 'photographer-portfolio' );

	foreach ( photographer_portfolio_get_category_pages() as $category_page ) {
		$content[] = get_the_title( $category_page );
		$content[] = __( 'Peržiūrėti visus', 'photographer-portfolio' );
		$content[] = photographer_portfolio_get_page_description( $category_page );

		$preview_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $category_page->ID, 'featured_ids' ) );

		if ( ! $preview_ids ) {
			$preview_ids = array_slice(
				photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $category_page->ID, 'gallery_ids' ) ),
				0,
				3
			);
		}

		$content = array_merge( $content, photographer_portfolio_rank_math_get_attachment_texts( array_slice( $preview_ids, 0, 3 ) ) );
	}

	return $content;
}

/**
 * Returns portfolio category template content.
 *
 * @param WP_Post $post Current page post.
 * @return array
 */
function photographer_portfolio_rank_math_get_portfolio_category_content( $post ) {
	$gallery_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $post->ID, 'gallery_ids' ) );
	$contact_url = function_exists( 'photographer_portfolio_get_page_url' )
		? photographer_portfolio_get_page_url( 'kontaktai', 'page-contact.php' )
		: home_url( '/kontaktai/' );

	return array_merge(
		array(
			get_the_title( $post ),
			sprintf(
				/* translators: %s: Portfolio category title. */
				__( '%s fotografijų galerija', 'photographer-portfolio' ),
				get_the_title( $post )
			),
			sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $contact_url ),
				esc_html__( 'Susisiekite dėl fotografavimo', 'photographer-portfolio' )
			),
		),
		photographer_portfolio_rank_math_get_attachment_texts( $gallery_ids )
	);
}

/**
 * Returns about page template fallback/static content.
 *
 * @param WP_Post $post Current page post.
 * @return array
 */
function photographer_portfolio_rank_math_get_about_content( $post ) {
	$image_alt = function_exists( 'photographer_portfolio_get_about_image_alt' )
		? photographer_portfolio_get_about_image_alt()
		: __( 'Julija Teresė Sadauskytė – SADA fotografė', 'photographer-portfolio' );
	$content = array(
		__( 'Apie mane', 'photographer-portfolio' ),
		__( 'Julija Teresė Sadauskytė / vizualinis archyvas', 'photographer-portfolio' ),
	);

	if ( has_post_thumbnail( $post ) ) {
		$content[] = sprintf(
			'<img src="%1$s" alt="%2$s">',
			esc_url( get_the_post_thumbnail_url( $post, 'portfolio_cover' ) ),
			esc_attr( $image_alt )
		);
	}

	if ( ! trim( wp_strip_all_tags( $post->post_content ) ) ) {
		$content[] = __( 'Esu Julija Teresė Sadauskytė — pradedančioji fotografė, dirbanti su žmonėmis, judesiu, spalvomis, muzika ir tuo mažu chaosu, kuris atsiranda prieš akimirkai tampant prisiminimu.', 'photographer-portfolio' );
		$content[] = __( 'Man patinka tai, kas ryšku, įdomu ir šiek tiek paradoksalu. Fotografijoje labiau ieškau charakterio nei tobulumo — tikrų išraiškų, įdomios šviesos ir detalių, kurios netikėtai sukuria visą vaizdą.', 'photographer-portfolio' );
		$content[] = __( 'Spalvos man yra viena svarbiausių fotografijos dalių. Kartais noriu, kad kadras būtų ryškus ir gyvas, o kartais – turėtų tapybai būdingą estetiką.', 'photographer-portfolio' );
		$content[] = __( 'Sàda – yra mano vizualinis archyvas, kuriame susitinka portretai, muzika, renginiai ir kasdienybės fragmentai. Čia viskas gali būti apgalvota, bet nebūtinai surežisuota. Man įdomiausia tai, kas atrodo tikra, netikėta ir gyva.', 'photographer-portfolio' );
		$content[] = __( 'Vis dar mokausi, eksperimentuoju ir ieškau savo žvilgsnio. Visada laukiu įdomių žmonių, komandų, projektų ir istorijų, kurias būtų galima įamžinti savaip...', 'photographer-portfolio' );
	}

	$content[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( function_exists( 'photographer_portfolio_get_portfolio_url' ) ? photographer_portfolio_get_portfolio_url() : home_url( '/portfolio/' ) ),
		esc_html__( 'PERŽIŪRĖTI DARBUS', 'photographer-portfolio' )
	);
	$content[] = __( 'Kiekviena fotografija išsaugo akimirką gyvą.', 'photographer-portfolio' );
	$content[] = __( 'SADA', 'photographer-portfolio' );

	return $content;
}

/**
 * Returns contact page template content.
 *
 * @return array
 */
function photographer_portfolio_rank_math_get_contact_content() {
	return array(
		__( 'KONTAKTAI', 'photographer-portfolio' ),
		__( 'Kontaktų puslapis', 'photographer-portfolio' ),
		__( 'Dėl fotosesijų, renginių, koncertų, švenčių ar kūrybinių projektų fotografavimo parašykite ir papasakokite, ką kuriate.', 'photographer-portfolio' ),
		__( 'Kontaktinė informacija', 'photographer-portfolio' ),
		__( 'Bendros užklausos', 'photographer-portfolio' ),
		'sada.photographyy@gmail.com',
		'+37062392255',
		__( 'Atvira darbams', 'photographer-portfolio' ),
		__( 'Fotosesijos / Renginiai / Koncertai / Projektai / Šventės', 'photographer-portfolio' ),
		__( 'Sekite', 'photographer-portfolio' ),
		'Instagram',
		'Facebook',
	);
}

/**
 * Returns SEO-relevant text attached to images shown by templates.
 *
 * @param array $attachment_ids Attachment IDs.
 * @return array
 */
function photographer_portfolio_rank_math_get_attachment_texts( $attachment_ids ) {
	$content = array();

	foreach ( photographer_portfolio_sanitize_id_list( $attachment_ids ) as $attachment_id ) {
		$attachment = get_post( $attachment_id );

		if ( ! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type ) {
			continue;
		}

		$alt       = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$image_url = wp_get_attachment_image_url( $attachment_id, 'large' );

		$content[] = get_the_title( $attachment );

		if ( $alt && $image_url ) {
			$content[] = sprintf(
				'<img src="%1$s" alt="%2$s">',
				esc_url( $image_url ),
				esc_attr( $alt )
			);
		}

		$content[] = $alt;
		$content[] = $attachment->post_excerpt;
	}

	return $content;
}

/**
 * Normalizes template content before it is passed to Rank Math analysis.
 *
 * @param mixed $content Raw content.
 * @return string
 */
function photographer_portfolio_rank_math_clean_content_fragment( $content ) {
	$content = trim( preg_replace( '/\s+/', ' ', (string) $content ) );

	return wp_kses(
		$content,
		array(
			'a'   => array(
				'href' => array(),
			),
			'img' => array(
				'alt' => array(),
				'src' => array(),
			),
		)
	);
}

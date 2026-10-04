<?php
/**
 * The header template.
 *
 * @package Photographer_Portfolio
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary">
	<?php esc_html_e( 'Pereiti prie turinio', 'photographer-portfolio' ); ?>
</a>

<header class="site-header" role="banner">
	<div class="site-header__inner">
		<?php get_template_part( 'template-parts/header/site-branding' ); ?>

		<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Pagrindinis meniu', 'photographer-portfolio' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'primary-navigation__menu',
					'container'      => false,
					'fallback_cb'    => 'photographer_portfolio_primary_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<div class="language-switcher" aria-label="<?php esc_attr_e( 'Kalbos pasirinkimas', 'photographer-portfolio' ); ?>">
			<a class="language-switcher__link is-active" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-current="true"><?php esc_html_e( 'LT', 'photographer-portfolio' ); ?></a>
			<span class="language-switcher__separator" aria-hidden="true">|</span>
			<a class="language-switcher__link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'EN', 'photographer-portfolio' ); ?></a>
		</div>

		<a class="site-header__cta" href="mailto:sada.photographyy@gmail.com">
			<?php esc_html_e( 'Susisiekite', 'photographer-portfolio' ); ?>
		</a>
	</div>
</header>

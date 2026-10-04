<?php
/**
 * Site footer information.
 *
 * @package Photographer_Portfolio
 */

?>
<div class="site-footer__inner">
	<p class="site-footer__credit">
		<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		<span aria-hidden="true">/</span>
		<span><?php echo esc_html( date_i18n( 'Y' ) ); ?></span>
	</p>

	<nav class="site-footer__social" aria-label="<?php esc_attr_e( 'Socialiniai tinklai', 'photographer-portfolio' ); ?>">
		<a href="https://www.instagram.com/sada.photography__" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'photographer-portfolio' ); ?>">
			<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
				<path d="M7.75 2h8.5C19.42 2 22 4.58 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm5.25-2.55a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z" />
			</svg>
		</a>
		<a href="https://www.facebook.com/profile.php?id=61586519211741" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'photographer-portfolio' ); ?>">
			<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
				<path d="M14.2 8.1V6.6c0-.72.48-.89.82-.89h2.08V2.2L14.23 2.19c-3.18 0-3.9 2.38-3.9 3.9v2.01H7.9v3.95h2.43V22h3.97v-9.95h2.96l.39-3.95H14.2Z" />
			</svg>
		</a>
	</nav>

	<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Poraštės meniu', 'photographer-portfolio' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'menu_class'     => 'footer-navigation__menu',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
		?>
	</nav>
</div>

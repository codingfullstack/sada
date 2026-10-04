<?php
/**
 * Theme functions.
 *
 * @package Photographer_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PHOTOGRAPHER_PORTFOLIO_VERSION' ) ) {
	define( 'PHOTOGRAPHER_PORTFOLIO_VERSION', '0.1.0' );
}

/**
 * Returns an asset version based on file modification time.
 *
 * @param string $relative_path Asset path relative to the theme directory.
 * @return string
 */
function photographer_portfolio_asset_version( $relative_path ) {
	$file_path = get_theme_file_path( $relative_path );

	if ( file_exists( $file_path ) ) {
		return (string) filemtime( $file_path );
	}

	return PHOTOGRAPHER_PORTFOLIO_VERSION;
}

/**
 * Sets up theme defaults and WordPress feature support.
 */
function photographer_portfolio_setup() {
	load_theme_textdomain( 'photographer-portfolio', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );
	add_image_size( 'portfolio_cover', 900, 1200, true );
	add_image_size( 'portfolio_hero', 1800, 1200, false );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'html5',
		array(
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'navigation-widgets',
			'search-form',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Pagrindinis meniu', 'photographer-portfolio' ),
			'footer'  => esc_html__( 'Poraštės meniu', 'photographer-portfolio' ),
		)
	);
}
add_action( 'after_setup_theme', 'photographer_portfolio_setup' );

/**
 * Portfolio page meta keys used by the theme.
 *
 * @return array
 */
function photographer_portfolio_page_meta_keys() {
	return array(
		'hero_image_id' => '_portfolio_page_hero_image_id',
		'gallery_ids'   => '_portfolio_page_gallery_ids',
		'featured_ids'  => '_portfolio_page_featured_ids',
		'categories'    => '_portfolio_page_categories',
	);
}

/**
 * Resolves a portfolio child page from the current request path.
 *
 * @return WP_Post|null
 */
function photographer_portfolio_get_request_category_page() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return null;
	}

	$path = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
	$path = trim( (string) $path, '/' );

	if ( ! preg_match( '#^portfolio/([^/]+)$#', $path, $matches ) ) {
		return null;
	}

	$page = get_page_by_path( 'portfolio/' . sanitize_title( $matches[1] ) );

	return $page instanceof WP_Post ? $page : null;
}

/**
 * Checks whether a page should use portfolio category gallery controls.
 *
 * @param WP_Post $post Page post object.
 * @return bool
 */
function photographer_portfolio_is_category_page( $post ) {
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return false;
	}

	if ( 'page-portfolio-category.php' === get_page_template_slug( $post ) ) {
		return true;
	}

	if ( ! $post->post_parent ) {
		return false;
	}

	$parent = get_post( $post->post_parent );

	return $parent instanceof WP_Post && 'portfolio' === $parent->post_name;
}

/**
 * Returns pages managed by the reusable Portfolio Category template.
 *
 * @return WP_Post[]
 */
function photographer_portfolio_get_category_pages() {
	$pages = get_pages(
		array(
			'meta_key'    => '_wp_page_template',
			'meta_value'  => 'page-portfolio-category.php',
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
		)
	);

	$portfolio_page = get_page_by_path( 'portfolio' );

	if ( $portfolio_page instanceof WP_Post ) {
		$pages = array_merge(
			$pages,
			get_pages(
				array(
					'child_of'    => $portfolio_page->ID,
					'post_status' => 'publish',
					'sort_column' => 'menu_order,post_title',
					'sort_order'  => 'ASC',
				)
			)
		);
	}

	$unique_pages = array();

	foreach ( $pages as $page ) {
		if ( $page instanceof WP_Post ) {
			$unique_pages[ $page->ID ] = $page;
		}
	}

	return array_values(
		array_filter(
			$unique_pages,
			static function ( $page ) {
				return $page instanceof WP_Post;
			}
		)
	);
}

/**
 * Returns short display text from a portfolio page's admin-managed content.
 *
 * @param WP_Post|int $post Page post or ID.
 * @return string
 */
function photographer_portfolio_get_page_description( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		return get_the_excerpt( $post );
	}

	return trim( wp_strip_all_tags( $post->post_content ) );
}

/**
 * Resolves a published page URL by slug and optional template.
 *
 * @param string $path     Page path.
 * @param string $template Optional page template file.
 * @return string
 */
function photographer_portfolio_get_page_url( $path, $template = '' ) {
	$page = get_page_by_path( $path );

	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}

	if ( $template ) {
		$pages = get_pages(
			array(
				'meta_key'    => '_wp_page_template',
				'meta_value'  => $template,
				'number'      => 1,
				'post_status' => 'publish',
			)
		);

		if ( ! empty( $pages[0] ) && $pages[0] instanceof WP_Post ) {
			return get_permalink( $pages[0] );
		}
	}

	return home_url( '/' . trim( $path, '/' ) . '/' );
}

/**
 * Returns the main portfolio page URL.
 *
 * @return string
 */
function photographer_portfolio_get_portfolio_url() {
	return photographer_portfolio_get_page_url( 'portfolio', 'page-portfolio.php' );
}

/**
 * Resolves the portfolio hero image with the standard Featured Image first.
 *
 * @param int $post_id Page ID.
 * @return int
 */
function photographer_portfolio_get_portfolio_hero_image_id( $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );

	if ( $thumbnail_id ) {
		return absint( $thumbnail_id );
	}

	$hero_id = absint( photographer_portfolio_get_page_meta( $post_id, 'hero_image_id' ) );

	if ( $hero_id ) {
		return $hero_id;
	}

	$gallery_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $post_id, 'gallery_ids' ) );

	return isset( $gallery_ids[0] ) ? absint( $gallery_ids[0] ) : 0;
}

/**
 * Returns the default semantic ALT text for the about page portrait.
 *
 * @return string
 */
function photographer_portfolio_get_about_image_alt() {
	return __( 'Julija Teresė Sadauskytė – SADA fotografė', 'photographer-portfolio' );
}

/**
 * Provides a page-specific ALT fallback without overriding Media Library values.
 *
 * @param array        $attr       Image attributes.
 * @param WP_Post      $attachment Attachment post object.
 * @param string|array $size       Requested image size.
 * @return array
 */
function photographer_portfolio_about_featured_image_alt( $attr, $attachment, $size ) {
	if ( is_admin() || ! is_page( array( 'about', 'apie-mane' ) ) ) {
		return $attr;
	}

	if ( ! empty( $attr['alt'] ) || empty( $attr['class'] ) || false === strpos( $attr['class'], 'about-editorial__image' ) ) {
		return $attr;
	}

	$attr['alt'] = photographer_portfolio_get_about_image_alt();

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'photographer_portfolio_about_featured_image_alt', 10, 3 );

/**
 * Collects image attachment IDs from Image and Gallery blocks in editor content.
 *
 * @param string $content Post content.
 * @return array
 */
function photographer_portfolio_get_editor_image_ids( $content ) {
	if ( ! has_blocks( $content ) ) {
		return array();
	}

	$image_ids = array();
	$blocks    = parse_blocks( $content );

	$walk_blocks = static function ( $blocks ) use ( &$walk_blocks, &$image_ids ) {
		foreach ( $blocks as $block ) {
			$block_name = $block['blockName'] ?? '';
			$attrs      = $block['attrs'] ?? array();

			if ( 'core/image' === $block_name && ! empty( $attrs['id'] ) ) {
				$image_ids[] = absint( $attrs['id'] );
			}

			if ( 'core/gallery' === $block_name && ! empty( $attrs['ids'] ) && is_array( $attrs['ids'] ) ) {
				$image_ids = array_merge( $image_ids, array_map( 'absint', $attrs['ids'] ) );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk_blocks( $block['innerBlocks'] );
			}
		}
	};

	$walk_blocks( $blocks );

	$image_ids = array_filter( $image_ids );

	return array_values( array_unique( $image_ids ) );
}

/**
 * Returns the first block of a selected type from editor content.
 *
 * @param string $content    Post content.
 * @param string $block_name Block name, for example core/paragraph.
 * @return array|null
 */
function photographer_portfolio_get_first_editor_block( $content, $block_name ) {
	if ( ! has_blocks( $content ) ) {
		return null;
	}

	$blocks = parse_blocks( $content );
	$found  = null;

	$walk_blocks = static function ( $blocks ) use ( &$walk_blocks, &$found, $block_name ) {
		foreach ( $blocks as $block ) {
			if ( $found ) {
				return;
			}

			if ( $block_name === ( $block['blockName'] ?? '' ) ) {
				$found = $block;
				return;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk_blocks( $block['innerBlocks'] );
			}
		}
	};

	$walk_blocks( $blocks );

	return $found;
}

/**
 * Extracts plain text from the first editor block of a selected type.
 *
 * @param string $content    Post content.
 * @param string $block_name Block name.
 * @return string
 */
function photographer_portfolio_get_first_editor_block_text( $content, $block_name ) {
	$block = photographer_portfolio_get_first_editor_block( $content, $block_name );

	if ( ! $block || empty( $block['innerHTML'] ) ) {
		return '';
	}

	return trim( wp_strip_all_tags( $block['innerHTML'] ) );
}

/**
 * Uses the reusable category template for portfolio child pages.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function photographer_portfolio_category_template_include( $template ) {
	$request_page = photographer_portfolio_get_request_category_page();

	if ( $request_page && photographer_portfolio_is_category_page( $request_page ) ) {
		global $wp_query;

		if ( $wp_query instanceof WP_Query ) {
			$wp_query->is_404           = false;
			$wp_query->is_page          = true;
			$wp_query->queried_object   = $request_page;
			$wp_query->queried_object_id = $request_page->ID;
			$wp_query->posts            = array( $request_page );
			$wp_query->post             = $request_page;
			$wp_query->post_count       = 1;
		}

		status_header( 200 );

		$category_template = locate_template( 'page-portfolio-category.php' );

		return $category_template ? $category_template : $template;
	}

	$page = get_queried_object();

	if ( ! photographer_portfolio_is_category_page( $page ) ) {
		return $template;
	}

	$category_template = locate_template( 'page-portfolio-category.php' );

	return $category_template ? $category_template : $template;
}
add_filter( 'template_include', 'photographer_portfolio_category_template_include' );

/**
 * Keeps portfolio category URLs from being canonicalized to top-level pages.
 *
 * @param string|false $redirect_url Canonical redirect URL.
 * @return string|false
 */
function photographer_portfolio_category_redirect_canonical( $redirect_url ) {
	$request_page = photographer_portfolio_get_request_category_page();

	return $request_page && photographer_portfolio_is_category_page( $request_page ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'photographer_portfolio_category_redirect_canonical' );

/**
 * Gets a portfolio page meta value.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Short meta key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function photographer_portfolio_get_page_meta( $post_id, $key, $default = '' ) {
	$keys = photographer_portfolio_page_meta_keys();

	if ( ! isset( $keys[ $key ] ) ) {
		return $default;
	}

	$value = get_post_meta( $post_id, $keys[ $key ], true );

	return '' === $value ? $default : $value;
}

/**
 * Converts a comma-separated ID list to sanitized attachment/post IDs.
 *
 * @param mixed $value Raw value.
 * @return array
 */
function photographer_portfolio_sanitize_id_list( $value ) {
	if ( is_array( $value ) ) {
		$ids = $value;
	} else {
		$ids = explode( ',', (string) $value );
	}

	$ids = array_map( 'absint', $ids );
	$ids = array_filter( $ids );

	return array_values( array_unique( $ids ) );
}

/**
 * Adds portfolio page edit screen meta boxes.
 *
 * @param WP_Post $post Current post.
 */
function photographer_portfolio_add_page_meta_boxes( $post ) {
	$template    = get_page_template_slug( $post );
	$is_overview = 'page-portfolio.php' === $template || 'portfolio' === $post->post_name;

	if ( ! $is_overview && ! photographer_portfolio_is_category_page( $post ) ) {
		return;
	}

	add_meta_box(
		'photographer-portfolio-page-content',
		esc_html__( 'Portfelio puslapio turinys', 'photographer-portfolio' ),
		'photographer_portfolio_render_page_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'photographer_portfolio_add_page_meta_boxes' );

/**
 * Renders portfolio page media controls.
 *
 * @param WP_Post $post Current post.
 */
function photographer_portfolio_render_page_meta_box( $post ) {
	wp_nonce_field( 'photographer_portfolio_save_page_meta', 'photographer_portfolio_page_meta_nonce' );

	$hero_id      = absint( photographer_portfolio_get_page_meta( $post->ID, 'hero_image_id' ) );
	$gallery_ids  = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $post->ID, 'gallery_ids' ) );
	$featured_ids = photographer_portfolio_sanitize_id_list( photographer_portfolio_get_page_meta( $post->ID, 'featured_ids' ) );
	$template     = get_page_template_slug( $post );
	$is_overview  = 'page-portfolio.php' === $template || 'portfolio' === $post->post_name;
	$categories   = photographer_portfolio_get_page_meta(
		$post->ID,
		'categories',
		''
	);
	?>
	<div class="portfolio-admin-media">
		<?php if ( $is_overview ) : ?>
			<p class="portfolio-admin-field">
				<label for="portfolio-page-categories"><?php esc_html_e( 'Portfelio kategorijos', 'photographer-portfolio' ); ?></label>
				<textarea id="portfolio-page-categories" name="portfolio_page_categories" rows="4"><?php echo esc_textarea( $categories ); ?></textarea>
				<span class="description"><?php esc_html_e( 'Papildomas tekstinis sąrašas suderinamumui. Portfelio kategorijų kortelės generuojamos iš puslapių, kuriems priskirtas portfelio kategorijos šablonas.', 'photographer-portfolio' ); ?></span>
			</p>
		<?php endif; ?>

		<div class="portfolio-admin-media__section">
			<h3><?php esc_html_e( 'Viršelio nuotrauka', 'photographer-portfolio' ); ?></h3>
			<input class="portfolio-admin-hero-id" name="portfolio_page_hero_image_id" type="hidden" value="<?php echo esc_attr( $hero_id ); ?>">
			<div class="portfolio-admin-hero-preview">
				<?php
				if ( $hero_id ) {
					echo wp_kses_post( wp_get_attachment_image( $hero_id, 'medium' ) );
				}
				?>
			</div>
			<button class="button portfolio-admin-select-hero" type="button"><?php esc_html_e( 'Pasirinkti viršelio nuotrauką', 'photographer-portfolio' ); ?></button>
			<button class="button portfolio-admin-remove-hero" type="button"><?php esc_html_e( 'Pašalinti', 'photographer-portfolio' ); ?></button>
		</div>

		<div class="portfolio-admin-media__section">
			<h3><?php esc_html_e( 'Portfelio galerija', 'photographer-portfolio' ); ?></h3>
			<input class="portfolio-admin-gallery-ids" name="portfolio_page_gallery_ids" type="hidden" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>">
			<ul class="portfolio-admin-gallery">
				<?php foreach ( $gallery_ids as $attachment_id ) : ?>
					<li data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
						<?php echo wp_kses_post( wp_get_attachment_image( $attachment_id, 'thumbnail' ) ); ?>
						<button class="button-link-delete portfolio-admin-remove-gallery-image" type="button"><?php esc_html_e( 'Pašalinti', 'photographer-portfolio' ); ?></button>
					</li>
				<?php endforeach; ?>
			</ul>
			<button class="button portfolio-admin-select-gallery" type="button"><?php esc_html_e( 'Pridėti nuotraukas', 'photographer-portfolio' ); ?></button>
			<span class="description"><?php esc_html_e( 'Nuotraukas galima perrikiuoti tempiant jas pele.', 'photographer-portfolio' ); ?></span>
		</div>

		<div class="portfolio-admin-media__section">
			<h3>
				<?php
				echo esc_html(
					$is_overview
						? __( 'Atrinkti projektai', 'photographer-portfolio' )
						: __( 'Portfelio puslapio kategorijos nuotraukos', 'photographer-portfolio' )
				);
				?>
			</h3>
			<input class="portfolio-admin-featured-ids" name="portfolio_page_featured_ids" type="hidden" value="<?php echo esc_attr( implode( ',', $featured_ids ) ); ?>">
			<ul class="portfolio-admin-featured">
				<?php foreach ( $featured_ids as $attachment_id ) : ?>
					<li data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
						<?php echo wp_kses_post( wp_get_attachment_image( $attachment_id, 'thumbnail' ) ); ?>
						<button class="button-link-delete portfolio-admin-remove-featured-image" type="button"><?php esc_html_e( 'Pašalinti', 'photographer-portfolio' ); ?></button>
					</li>
				<?php endforeach; ?>
			</ul>
			<button class="button portfolio-admin-select-featured" type="button"><?php esc_html_e( 'Pasirinkti 3 nuotraukas', 'photographer-portfolio' ); ?></button>
			<span class="description">
				<?php
				echo esc_html(
					$is_overview
						? __( 'Šis laukas paliktas suderinamumui su ankstesniu Portfolio valdymu.', 'photographer-portfolio' )
						: __( 'Šios trys nuotraukos rodomos portfelio puslapyje prie šios kategorijos. Jas galima perrikiuoti tempiant pele.', 'photographer-portfolio' )
				);
				?>
			</span>
		</div>
	</div>
	<?php
}

/**
 * Saves portfolio page meta box data.
 *
 * @param int $post_id Post ID.
 */
function photographer_portfolio_save_page_meta( $post_id ) {
	if ( ! isset( $_POST['photographer_portfolio_page_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photographer_portfolio_page_meta_nonce'] ) ), 'photographer_portfolio_save_page_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$keys = photographer_portfolio_page_meta_keys();

	if ( isset( $_POST['portfolio_page_hero_image_id'] ) ) {
		$hero_image_id = absint( $_POST['portfolio_page_hero_image_id'] );
		update_post_meta( $post_id, $keys['hero_image_id'], $hero_image_id );
	}

	if ( isset( $_POST['portfolio_page_gallery_ids'] ) ) {
		$gallery_ids = photographer_portfolio_sanitize_id_list( wp_unslash( $_POST['portfolio_page_gallery_ids'] ) );
		update_post_meta( $post_id, $keys['gallery_ids'], implode( ',', $gallery_ids ) );
	}

	if ( isset( $_POST['portfolio_page_featured_ids'] ) ) {
		$featured_ids = photographer_portfolio_sanitize_id_list( wp_unslash( $_POST['portfolio_page_featured_ids'] ) );
		update_post_meta( $post_id, $keys['featured_ids'], implode( ',', array_slice( $featured_ids, 0, 3 ) ) );
	}

	if ( isset( $_POST['portfolio_page_categories'] ) ) {
		$categories = sanitize_textarea_field( wp_unslash( $_POST['portfolio_page_categories'] ) );
		update_post_meta( $post_id, $keys['categories'], $categories );
	}
}
add_action( 'save_post_page', 'photographer_portfolio_save_page_meta' );

/**
 * Keeps the homepage navigation item pointing to the actual front page.
 *
 * @param array    $items Menu item objects.
 * @param stdClass $args  Menu arguments.
 * @return array
 */
function photographer_portfolio_link_home_menu_item( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$home_url       = untrailingslashit( home_url( '/' ) );
	$front_page_id  = absint( get_option( 'page_on_front' ) );
	$home_url_slash = home_url( '/' );

	foreach ( $items as $item ) {
		$item_url       = isset( $item->url ) ? untrailingslashit( $item->url ) : '';
		$object_id      = absint( $item->object_id ?? 0 );
		$title          = isset( $item->title ) ? wp_strip_all_tags( $item->title ) : '';
		$is_home_url    = $home_url === $item_url;
		$is_front_page  = $front_page_id && $front_page_id === $object_id;
		$is_home_label  = in_array( $title, array( 'Pradžia', 'Pradžiai', 'Pradzia', 'Pradziai' ), true );

		if ( $is_home_url || $is_front_page || $is_home_label ) {
			$item->url = $home_url_slash;
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'photographer_portfolio_link_home_menu_item', 9, 2 );

/**
 * Adds portfolio category pages as submenu items below the Portfolio menu item.
 *
 * @param array    $items Menu item objects.
 * @param stdClass $args  Menu arguments.
 * @return array
 */
function photographer_portfolio_add_category_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$portfolio_page   = get_page_by_path( 'portfolio' );
	$category_pages   = photographer_portfolio_get_category_pages();
	$portfolio_item   = null;
	$existing_page_ids = array();
	$is_current_portfolio_category = false;

	if ( ! $portfolio_page instanceof WP_Post || ! $category_pages ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( isset( $item->object_id ) ) {
			$existing_page_ids[] = absint( $item->object_id );
		}

		$item_url = isset( $item->url ) ? untrailingslashit( $item->url ) : '';

		if (
			absint( $item->object_id ?? 0 ) === absint( $portfolio_page->ID )
			|| untrailingslashit( home_url( '/portfolio/' ) ) === $item_url
		) {
			$portfolio_item = $item;
		}
	}

	if ( ! $portfolio_item ) {
		return $items;
	}

	$inserted_items = array();

	foreach ( $category_pages as $index => $category_page ) {
		if ( is_page( $category_page->ID ) ) {
			$is_current_portfolio_category = true;
		}

		if ( in_array( absint( $category_page->ID ), $existing_page_ids, true ) ) {
			continue;
		}

		$menu_item                   = new stdClass();
		$menu_item->ID               = 0 - absint( $category_page->ID );
		$menu_item->db_id            = $menu_item->ID;
		$menu_item->menu_item_parent = (string) $portfolio_item->ID;
		$menu_item->object_id        = (string) $category_page->ID;
		$menu_item->object           = 'page';
		$menu_item->type             = 'post_type';
		$menu_item->type_label       = __( 'Page', 'photographer-portfolio' );
		$menu_item->title            = get_the_title( $category_page );
		$menu_item->url              = get_permalink( $category_page );
		$menu_item->target           = '';
		$menu_item->attr_title       = '';
		$menu_item->description      = '';
		$menu_item->classes          = array( 'menu-item', 'menu-item-type-post_type', 'menu-item-object-page', 'portfolio-category-menu-item' );
		$menu_item->xfn              = '';
		$menu_item->current          = is_page( $category_page->ID );
		$menu_item->current_item_ancestor = false;
		$menu_item->current_item_parent   = false;
		$menu_item->menu_order       = absint( $portfolio_item->menu_order ?? 0 ) + $index + 1;

		$inserted_items[] = $menu_item;
	}

	if ( $is_current_portfolio_category ) {
		$portfolio_item->classes[] = 'current-menu-ancestor';
	}

	return array_merge( $items, $inserted_items );
}
add_filter( 'wp_nav_menu_objects', 'photographer_portfolio_add_category_menu_items', 10, 2 );

/**
 * Outputs a minimal primary menu fallback for the theme preview state.
 */
function photographer_portfolio_primary_menu_fallback() {
	$portfolio_categories = photographer_portfolio_get_category_pages();
	$menu_items = array(
		array(
			'label'  => esc_html__( 'Pradžia', 'photographer-portfolio' ),
			'url'    => home_url( '/' ),
			'active' => is_front_page(),
		),
		array(
			'label'  => esc_html__( 'Portfolio', 'photographer-portfolio' ),
			'url'    => photographer_portfolio_get_portfolio_url(),
			'active' => is_page( 'portfolio' ) || photographer_portfolio_is_category_page( get_queried_object() ),
			'children' => $portfolio_categories,
		),
		array(
			'label'  => esc_html__( 'Apie', 'photographer-portfolio' ),
			'url'    => photographer_portfolio_get_page_url( 'apie-mane', 'page-about.php' ),
			'active' => is_page( 'apie-mane' ),
		),
		array(
			'label'  => esc_html__( 'Kontaktai', 'photographer-portfolio' ),
			'url'    => photographer_portfolio_get_page_url( 'kontaktai', 'page-contact.php' ),
			'active' => is_page( array( 'contact', 'kontaktai' ) ),
		),
	);
	?>
	<ul class="primary-navigation__menu">
		<?php foreach ( $menu_items as $menu_item ) : ?>
			<li class="<?php echo $menu_item['active'] ? esc_attr( 'current-menu-item current_page_item' ) : ''; ?><?php echo ! empty( $menu_item['children'] ) ? esc_attr( ' menu-item-has-children' ) : ''; ?>">
				<a href="<?php echo esc_url( $menu_item['url'] ); ?>"<?php echo $menu_item['active'] ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $menu_item['label'] ); ?>
				</a>
				<?php if ( ! empty( $menu_item['children'] ) ) : ?>
					<ul class="sub-menu">
						<?php foreach ( $menu_item['children'] as $child_page ) : ?>
							<li class="<?php echo is_page( $child_page->ID ) ? esc_attr( 'current-menu-item current_page_item' ) : ''; ?>">
								<a href="<?php echo esc_url( get_permalink( $child_page ) ); ?>">
									<?php echo esc_html( get_the_title( $child_page ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Enqueues theme styles and scripts.
 */
function photographer_portfolio_enqueue_assets() {
	wp_enqueue_style(
		'photographer-portfolio-fonts',
		'https://fonts.googleapis.com/css2?family=Anton&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'photographer-portfolio-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array( 'photographer-portfolio-fonts' ),
		photographer_portfolio_asset_version( 'assets/css/main.css' )
	);

	if ( is_page_template( 'page-portfolio.php' ) || is_page_template( 'page-portfolio-category.php' ) || photographer_portfolio_is_category_page( get_queried_object() ) ) {
		wp_enqueue_style(
			'photographer-portfolio-portfolio',
			get_theme_file_uri( 'assets/css/portfolio.css' ),
			array( 'photographer-portfolio-main' ),
			photographer_portfolio_asset_version( 'assets/css/portfolio.css' )
		);

		wp_enqueue_script(
			'photographer-portfolio-portfolio',
			get_theme_file_uri( 'assets/js/portfolio.js' ),
			array(),
			photographer_portfolio_asset_version( 'assets/js/portfolio.js' ),
			true
		);
	}

	if ( is_page_template( 'page-about.php' ) || is_page( array( 'about', 'apie-mane' ) ) ) {
		wp_enqueue_style(
			'photographer-portfolio-about',
			get_theme_file_uri( 'assets/css/about.css' ),
			array( 'photographer-portfolio-main' ),
			photographer_portfolio_asset_version( 'assets/css/about.css' )
		);
	}

	if ( is_page_template( 'page-contact.php' ) || is_page( array( 'contact', 'kontaktai' ) ) ) {
		wp_enqueue_style(
			'photographer-portfolio-contact',
			get_theme_file_uri( 'assets/css/contact.css' ),
			array( 'photographer-portfolio-main' ),
			photographer_portfolio_asset_version( 'assets/css/contact.css' )
		);
	}

	wp_enqueue_script(
		'photographer-portfolio-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		photographer_portfolio_asset_version( 'assets/js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'photographer_portfolio_enqueue_assets' );

/**
 * Enqueues portfolio admin media controls.
 *
 * @param string $hook_suffix Current admin page.
 */
function photographer_portfolio_enqueue_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'photographer-portfolio-admin',
		get_theme_file_uri( 'assets/css/admin-portfolio.css' ),
		array(),
		photographer_portfolio_asset_version( 'assets/css/admin-portfolio.css' )
	);
	wp_enqueue_script(
		'photographer-portfolio-admin',
		get_theme_file_uri( 'assets/js/admin-portfolio.js' ),
		array( 'jquery', 'jquery-ui-sortable' ),
		photographer_portfolio_asset_version( 'assets/js/admin-portfolio.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'photographer_portfolio_enqueue_admin_assets' );

/**
 * Enqueues editor-only font styles for Gutenberg.
 */
function photographer_portfolio_enqueue_editor_assets() {
	wp_enqueue_style(
		'photographer-portfolio-editor-fonts',
		'https://fonts.googleapis.com/css2?family=Anton&display=swap',
		array(),
		null
	);
}
add_action( 'enqueue_block_editor_assets', 'photographer_portfolio_enqueue_editor_assets' );

/**
 * Adds Google Fonts resource hints.
 *
 * @param array  $urls          Resource hint URLs.
 * @param string $relation_type Hint relation type.
 * @return array
 */
function photographer_portfolio_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = 'https://fonts.gstatic.com';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'photographer_portfolio_resource_hints', 10, 2 );

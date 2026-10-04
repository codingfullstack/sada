<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'jdX=e3kgs^Q8lRudG)h>~}Zi]~)K}%..:8Xy$cP2A_5$w|/|cs1tUjZxbDl%m*x6' );
define( 'SECURE_AUTH_KEY',   'Xz.@CR)R(7LA{U~Mbvo%XGzzist+ Si|m J0>p!uich+nI_at:m8/:_7ntD&I2=<' );
define( 'LOGGED_IN_KEY',     'G?D0D/J`4D>q(,]D0m1dZa1,QUH!fJ%x`88LNy%+4_8CMfLVVG_eH,8Xpy#rmA3]' );
define( 'NONCE_KEY',         'Jl+T[+Xmz`lVyb(:`5ss1]Al;Bj6GP;Bz,/c{4ijPJ<-WOqk+E.t4i8+BzUK&EU9' );
define( 'AUTH_SALT',         'DXGi@PS:Qc3re:20Kgxro ov^A{u;XAiO5jbeVM}j9(PkH$Nl@_8^.y@n+?4XwVN' );
define( 'SECURE_AUTH_SALT',  'kHY(1V~drqRYm_<,z_}ou!T.=bv251jF1%b7DIG@s0l5aTkzbx9n/u$biT 86#OC' );
define( 'LOGGED_IN_SALT',    'secCHWfAWT,NR{7{}LM/U!G<u*)c[KqXw}/%|oCO_|_W&%NrQW/*l@jFPv%l4b/?' );
define( 'NONCE_SALT',        'F|W1BbW&1V@:L&v&+mD a/J6LTkNV 3s0MWV7!_FM$?d!t;NO}n4Bz&WyKxv?nMQ' );
define( 'WP_CACHE_KEY_SALT', 'e<)^lGXg3j}K+-oC/q3iW~R{*jTidRy5S}+eGk@ZyZuAJEC6OY%.td^$T8j-.Xl-' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

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
define( 'AUTH_KEY',          '-g-b)poy3(s]D@FGd&3Ms|VBRP?AC##9QUTt4fy5W}0ybfLXu(g:#HT:[Mczf7G^' );
define( 'SECURE_AUTH_KEY',   'g1M}RKu]-9tl=9(?O/z&4^RC41_0PdJS,:e7hb8cGv!=OEANmHdiR]]@%}U`h5}r' );
define( 'LOGGED_IN_KEY',     'FIG$[<w!00N(X/,=S1#F$HR`Z#4a^9cW61)#~r`?nGIU7<Mbx;grh3=l^Dl&y^<I' );
define( 'NONCE_KEY',         'itaO,OhF=^^~HW-Xe%vX@R%}<aE#4yemR|{A/Cdy]fm:`]_F!M2Lvn/0]p;:Zd/w' );
define( 'AUTH_SALT',         'U>&8W%9M8a+1jk_!;*A}$OR>hUd9t,WZL3`g)938@72>/g@ 96&&+$JC VE{2u]*' );
define( 'SECURE_AUTH_SALT',  'T=34XFmR5D]p(poV(K=u%?=^dgR4}GDLQL#VffJZ2t0Lm@A1m|b{M>4.2VSBXu=B' );
define( 'LOGGED_IN_SALT',    '3q$C-$fe&l:iZ7#fD2SgS8eR}<Nc,xrN~GMlNDquXY/Hee9Si3BIXsXs,o8aW_VM' );
define( 'NONCE_SALT',        'xe*-w^5+lvg<R/:K5_zLd-@4DY+x-,*EIG(=Egcc}B$r&779vp=tC)=VVkxY1qCM' );
define( 'WP_CACHE_KEY_SALT', 'jn6NJ$2x=~qc7NcX$eBcP+[8&yQ-Ag0DTu;l%uy7%nEo?>V/,<Fp|Da>].;,?[/K' );


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

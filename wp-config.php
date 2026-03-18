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
define( 'DB_NAME', 'cliset' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Admin@123#@!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
define( 'FS_METHOD', 'direct' );

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
define( 'AUTH_KEY',          '>-;jyN;466GXH}J_VJ@R(C_l=M2+G^h&VPDV1$e!#)79E*a,tYK=qaa~GJU_,kbz' );
define( 'SECURE_AUTH_KEY',   '?<3k<%~4q/cq<Z=Tu(Gm+@pwqc?:)U^n~!ac:M3b^CC3]%)L4}]<h#]Y-rf$WrRM' );
define( 'LOGGED_IN_KEY',     'qy=_~ @Yn6.n&jH=3?g`}k~=wOCi6wo=-uTGO;al2OjamE9UAmJ|l-p-W`*CbP]p' );
define( 'NONCE_KEY',         'm cnyh&}<dO&Ji<~X8^s.P;{J6U{whmB_JLGr^wC9htg9 NX7FOoOM2&x/,>bef#' );
define( 'AUTH_SALT',         '@8+6Sus+rRUF@C034f^vq$Ny0#jY$6(1:K}K1>!m:n=UKe#Ol)h.vozK.L9Xv;Ci' );
define( 'SECURE_AUTH_SALT',  'L.!_KRG)GF6N*|xGg<;nPzNQ^yXCv|Ev5XmbNd}AU?+@cv^fQP5l;swDeFdFnx:V' );
define( 'LOGGED_IN_SALT',    '46XQhxzFRLilc<AWWmxT2wDid{Nq%/=IC>;O}Q^K~~6;7{QcJEj7:)TLfVn#> aO' );
define( 'NONCE_SALT',        'G(4<K4[*g4E.]Nl%PUO9}2hh2v8IqYbbx~wnVQW_+eepR5Kh)4SG%FjV|>B-YC3O' );
define( 'WP_CACHE_KEY_SALT', '?2_IBV:IZgW%boJ{#2k,.JE5*-5JBtK}:?VWtKP%wZ|hf5&4Z=LWsy0D?;,meoqw' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

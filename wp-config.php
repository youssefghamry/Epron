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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'Epron' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         ')R*7?.tm t49?+&Kuom.8?4Ec{G0N3o*eudX:D:YSse[fx&4XNEw9L.bzCGr2L*~' );
define( 'SECURE_AUTH_KEY',  'mRdun1ZqH8WL*J<4D3-j+6Y0}LiQ#~xi}FtXM&<:SpD->L2p9bdQoK3`qn9b 8I=' );
define( 'LOGGED_IN_KEY',    'KYulEZchac>!Adp<,4~XzSoS}2a>bib{Jr0jb!CnC]u%2)t}n8kN^*|u9pNPY]gb' );
define( 'NONCE_KEY',        '&g6zu{k@@1QYCVt5L){b(!nj3%;<4PM)uJX*!raTc3lD8%-3,*9~TmUc}^>MQKv9' );
define( 'AUTH_SALT',        'gO[-M$1=6ek#>~$DMb$#LS<An4_OCiR=IC+=BqwiF<M:Lrf<{;:<%)rTo3 9>U;c' );
define( 'SECURE_AUTH_SALT', 'RbK;l!5t7~TOkaSi*O_V%a$+w#;iDc2Vp+OG+[{^M=xA!Zn)5e d|-Y]mV@lQ4^4' );
define( 'LOGGED_IN_SALT',   ';71 ov,?wwxxYxl|TW|ie?wim6`1|fEIC C4a4Yt5dA4|U}H?`dA{;7Q-{k@W8B[' );
define( 'NONCE_SALT',       'W>S(=n5IEJRWj}z>5%D,FVeafeghl1V](?DDIT.8(?O1odtE,wrb}$FhSvVAAD{w' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_Epron';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

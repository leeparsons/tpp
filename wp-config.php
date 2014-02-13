<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'tpp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'organic');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '!ev ]7|(~P;Yj=CIv,y /i[n!QDVA*{u{{g.n5L&|kSY=yb8?Y%I?l Hw-T7b5,)');
define('SECURE_AUTH_KEY',  'yYM:U,t@}^3}?t|=XXIBOC[:7plx)^,lNlo`yZedHyO$y58eDJc;,%|TKyqQ-6L*');
define('LOGGED_IN_KEY',    'h 4 ,kbAxT((&r[$Q1=_,*?W3)8WBw^GP;^c@HX[$Q|1+)+svS~Or2C8ouD`1-w-');
define('NONCE_KEY',        '-r]#0O$A{DzuRfVVb{R4/]+8GGqXxGAi^J)MJ[<@tKQkL]x`+/%6z{K|jj./qtDF');
define('AUTH_SALT',        'E`1#Y0e+}FTn$tG[=+,+G@#;^Xr@jH9N+|`!~R  r0|50fkcl}r=(f-3ydE=}D %');
define('SECURE_AUTH_SALT', ' +S|;jU|oCEqYc%_MoweMcvyV>xeZLUl|}^q@:_f:uo/)^3<RjL=Q&7OLy]11c|K');
define('LOGGED_IN_SALT',   'QdVc<NPfF5D::9;gH2+)q!Kee`@AF u4n.cf>L!rgUo#3;nSCJ1{/V:zv])2%).g');
define('NONCE_SALT',       '#=&KU,[)d~8{cD^(cb[sDaQ;$3+NiObhLG0|gqAfP*eu<BDx+$JDTm>5N3:|vqOU');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'p';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

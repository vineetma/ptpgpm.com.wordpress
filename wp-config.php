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
define('DB_NAME', 'ptpgpm2013');

/** MySQL database username */
define('DB_USER', 'ptpgpm2013');

/** MySQL database password */
define('DB_PASSWORD', 'pgpm@1234');

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
define('AUTH_KEY',         'Yz@?N!hCgFmP&bgg%|RPs;ffQ%mTg=0mrY*Z8&8G+ 5Ua-,-3pG@-@*`JO2l>l~e');
define('SECURE_AUTH_KEY',  'bGLV+AnA9d4uy&k5_4$IC1fX-|_1C_F$3+BQ~f=2jtY$I>hX;-k>l,!%+#A j]Z<');
define('LOGGED_IN_KEY',    'paX^Wx+>c^n,$Yc0ax/ dL.3L[bfeH@?;7~U*:hLJ(WVQ|Lxr^&rFS+<Te*; [-m');
define('NONCE_KEY',        'I,}gq[[H[-OCKk|(n2w;c^|f8xdeK:Yx&)-@|u{=eQZhW^V3M?To^&uCHA&X&#{y');
define('AUTH_SALT',        '8|WqFEOAC]{q9{S/[*.r1CqwMeOS*<sRkv|zJO#Qe-+OCM<]D#:^Bey#}hJQAN:=');
define('SECURE_AUTH_SALT', '2F*]scm!ec[8YN#)X*ADgRLR&1act(3fb}]mZXp=2/Z?Fc1s:78|^GVb,UURI0.Z');
define('LOGGED_IN_SALT',   'Z6Wi|50_mIMR2o+3_ `$#LX[|vmx<LP`[987n/U$Jch[+l0!wCp^)H>Cl+A[pV}^');
define('NONCE_SALT',       'J;~3.bpmIM0]ikog+Y)aeH^<HT&#`l[4b?2f+Q,T_?0E;dj7l6.y#]4V6lg%@RGO');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ptpg_';

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


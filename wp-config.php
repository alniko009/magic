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
define('DB_NAME', 'magicseeds');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '199WmWcSpYBJKqZ2');

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
define('AUTH_KEY',         '];yBvXY?RT1`#UNu^f- Ey.y:z!dR:tm.!x&q-kiFm}:8*/-L_N706e:1^>R~E55');
define('SECURE_AUTH_KEY',  '-V=1Q8$^hM[H<MuQi[: wuQAN6SuHEE%q5>4_8#sS%e,I}Q{bLzte{].xnYextgz');
define('LOGGED_IN_KEY',    '525AF!@lwv!z5X-KE5ZG}!,Y-^.R9;2(#uqbL+V11y/Q(J{KAJ$=KBBh4(fE9TcC');
define('NONCE_KEY',        '%d0|F+$3z %/-AA%doZvSM.Y_T1dX1J_QiLla-!vhEAPvY_}@a>}U:]$h1s>PQk6');
define('AUTH_SALT',        'Xs~F3*V*M|{)YKc1u&}/[*ukg.~>ve&c(cOc^/2p!MbteE%~dkLlL>::06D_<m}h');
define('SECURE_AUTH_SALT', '@l@Vg)>7gBU&<V0CM=`Dm`Eo@]F7DulW^oG#sfG{3K,NM/ ~1KebmN~O3rn)$q|-');
define('LOGGED_IN_SALT',   'rH>30M>O~LF,SRDKU?iSB4?*!*A6tfJmc:zx(<xfV/!x}x+Vzgk-!B`gF/Ppqm_p');
define('NONCE_SALT',       'Zx[lO4077QLy!&vB)8`Y|wJ,C>o0xXRL?xP7}Yu4B?WHZ<qi_,{N(1dj^z!%-A+}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

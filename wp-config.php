<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '5mo_dev');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '`w(]nj|gi#eW#7yGRJ)C>JQ6:=$#~--mqO]M3{twMwKo<C0rg#3[B}2vzm@zvP}v');
define('SECURE_AUTH_KEY',  'a][;LbG5j(=1~;`?%eI0<.3({]Tidz9V{?{pCvcTb~>3mMV%*f<3etKBg5IR8(<L');
define('LOGGED_IN_KEY',    '831BZ,Lmp)|vumrHV)!NNhHU?0lBxo~FucM=@a<C/Ih2Z94=oDK*@FE_Gz,w2 (Y');
define('NONCE_KEY',        's3;]`P-+J+d_5e*.Tt}~}aqw`><!QNoJ_.v,l^P5>@67%PjL,KX--XHBKNUx^lmQ');
define('AUTH_SALT',        ' |_fk>Pieca!hgd~A~Eatb?|*qr82l%}Our1O+Ac5B6!}JTfaj|x&:AE$:Sl{h][');
define('SECURE_AUTH_SALT', 'Y=f,C!ovXp<B|y]n1-`B.R(GUBp)D&MsvErWd?n+r__L.%*?z6 Z$aZx&YM&@[kc');
define('LOGGED_IN_SALT',   'p4q?UtI^A|^P-/w7-8dlcnS7kITcXF#h-W!E8Cf^;JD=Q,u}Q5*nv{#ZIqc<BI^3');
define('NONCE_SALT',       'j;bvI7zY^,v7Idw&?4TLkO-[Qx&3`4CSEJ3kk6e&#>4I{iPvT;eUE[H`)LK.v!S+');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

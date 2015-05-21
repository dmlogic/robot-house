<?php
define('ENVIRONMENT','production'); // or local
define('ENCRYPT_KEY','set');  // 32 chars should do
define('AUTH_COOKIE_NAME','set');   // 32 chars should do
define('AUTH_USERNAME','set');   // You log in with this
define('AUTH_SALT','set'); // 32 chars should do
define('AUTH_PWD','set'); // md5('your-password-that-isnt-saved-anywhere'.AUTH_SALT);
define('MEMCACHED_HOST', '127.0.0.1');
define('MEMCACHED_PORT', '11211');

define('MIOS_UNAME','set');
define('MIOS_PWD','set');
define('MIOS_VERAID','set');
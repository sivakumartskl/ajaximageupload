<?php

define('SERVER_NAME', 'localhost');
define('DATABASE', 'imageuploads');
define('USER_NAME', 'root');
define('PASSWORD', '78421n');

define('DEBUG_MODE', true);
define('UPLOAD_FILE_SIZE', 2000000); // Accepting only image having size less than 2MB
define('FILE_PERMISSIONS', 0777);
define('IMAGE_DIR_PATH', '/imageupload/imageuploads/');

if (DEBUG_MODE) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
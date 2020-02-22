<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| DAPHNE Database Constants
|--------------------------------------------------------------------------
|
| Define the constants from the database, and translate them in human readable
| variables.
|
*/
defined('PROJECT_REQUEST')          OR define('PROJECT_REQUEST', 3);
defined('PROJECT_REQUEST_ACCEPTED') OR define('PROJECT_REQUEST_ACCEPTED', 4);
defined('PROJECT_REQUEST_DECLINED') OR define('PROJECT_REQUEST_DECLINED', 5);
defined('PROJECT_INVIT')            OR define('PROJECT_INVIT', 6);
defined('PROJECT_INVIT_ACCEPTED')   OR define('PROJECT_INVIT_ACCEPTED', 7);
defined('PROJECT_INVIT_DECLINED')   OR define('PROJECT_INVIT_DECLINED', 8);
defined('NEW_USER')                 OR define('NEW_USER', 10);


defined('PGSQL_TRUE')               OR define('PGSQL_TRUE', 't');
defined('PGSQL_FALSE')              OR define('PGSQL_FALSE', 'f');


/*defined('DATASET_REQUEST_ACCEPTED') OR define('DATASET_REQUEST_ACCEPTED', 4);
defined('PROJECT_REQUEST_DECLINED') OR define('DATASET_REQUEST_DECLINED', 5);*/
defined('DATASET_REQUEST')          OR define('DATASET_REQUEST', 333);
defined('DATASET_INVIT')            OR define('DATASET_INVIT', 666);



defined('VISIBILITY_PRIVATE')       OR define('VISIBILITY_PRIVATE', 0);
defined('VISIBILITY_CUSTOM')        OR define('VISIBILITY_CUSTOM', 1);
defined('VISIBILITY_PUBLIC')        OR define('VISIBILITY_PUBLIC', 2);

defined('ACCESS_WRITE')        OR define('ACCESS_WRITE', 'w');
defined('ACCESS_READ')        OR define('ACCESS_READ', 'r');


/*
|--------------------------------------------------------------------------
| DAPHNE E-mail
|--------------------------------------------------------------------------
|
| Define the email adress of the web application.
|
*/
 //defined('AEGIS_MAIL') OR define('AEGIS_MAIL', "samia.mokdad@cirad.fr"); // my email test
 defined('AEGIS_MAIL') OR define('AEGIS_MAIL', "aegis@cirad.fr"); // official  address : aegis@cirad.fr

 /*
 |--------------------------------------------------------------------------
 | DAPHNE extra path
 |--------------------------------------------------------------------------
 |
 | NOTE Dont forget folder permissions.
 |
 */
 defined('UPLOAD_PATH') OR define('UPLOAD_PATH', './uploads/'); // Path where the files uploaded will be copied.

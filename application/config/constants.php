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
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
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
defined('EXIT_SUCCESS')         OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')           OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')          OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')    OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')   OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD')  OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')      OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')        OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')       OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')       OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
defined('APPS_IP_STATIC')       OR define('APPS_IP_STATIC', '10.11.9.246:8080/');
defined('APPS_REFRESH')         OR define('APPS_REFRESH', 3600);
defined('APPS_CORP')            OR define('APPS_CORP', 'PT. MULTI ARTA SEKAWAN');
defined('APPS_NAME')            OR define('APPS_NAME', 'MASPRO');
defined('APPS_KEYWORD')         OR define('APPS_KEYWORD', 'Aplikasi Website PT. MULTI ARTA SEKAWAN, PT. MULTI ARTA SEKAWAN');
defined('APPS_AUTHOR')          OR define('APPS_AUTHOR', 'PT. MULTI ARTA SEKAWAN | IT DEPARTMENT');
defined('APPS_DESC')            OR define('APPS_DESC', 'Aplikasi Website PT. MULTI ARTA SEKAWAN adalah sebuah Aplikasi berbasis website yang digunakan untuk memonitoring status, progress dari aplikasi-aplikasi yang telah dan akan dikerjakan.');
defined('API_PO')               OR define('API_PO', 'http://localhost:8001/api-po-mas/');
defined('COMPANY_ADDRESS')      OR define('COMPANY_ADDRESS', 'Jl. Millenium 22 Blok O2. 3A, Kadu Agung, Kec. Tigaraksa, Kabupaten Tangerang, Banten 15720');

defined('LINK_COMPANY')         OR define('LINK_COMPANY', 'https://e-ticketing.main-mfg.com/'); // highest automatically-assigned error code
defined('API_GAS')              OR define('API_GAS', 'http://localhost:8001/global-application-system/');
defined('API_ETICKETING')       OR define('API_ETICKETING', 'https://e-ticketing.main-mfg.com/');
defined('LINK_GAS')             OR define('LINK_GAS', 'http://10.11.9.22:8080/global-application-system/');
defined('WH_FIFO')              OR define('WH_FIFO', 'MAS/FO/WH/14');
defined('PD_RS')                OR define('PD_RS', 'MAS/FO/WH/14');

//defined('API_PO')             OR define('API_PO', 'https://api-po-mas.main-mfg.com/');
//defined('API_PO')             OR define('API_PO', 'https://purchase-order.omas-mfg.com/');
//defined('API_PO')             OR define('API_PO', 'http://localhost:8001/omas-purchase-order/');

<?php


/**
 * @return bool
 */
function isCLI()
{
    return (php_sapi_name() === 'cli');
}
function isWeb() {
    return ! isCLI();
}

/**
 * It gets data from Web or CLI
 * @param $name
 * @param string $default
 * @return string|array
 * @example request - \app\php\php index.php "mc=test.all&func=my"
 * @code
 *      print_r( in() );        - print out all data.
 *      echo in('id');
 * @endcode
 */
function in( $name=null, $default = '' ) {
    if ( isCLI() ) {
        $q = $GLOBALS['argv'][1];
        parse_str( $q, $_REQUEST );
    }
    if ( $name === null ) return $_REQUEST;
    else if ( isset( $_REQUEST[$name]) && $_REQUEST[$name] ) return $_REQUEST[$name];
    else return $default;
}
function esc( $name ) {
    $val = in($name);
    return db()->escape( $val );
}
function getModule() {
    $mc = trim(in('mc'));
    if ( empty($mc) ) return 'index';
    $arr = explode('.', $mc, 2);
    return $arr[0];
}
function getController() {
    $mc = trim(in('mc'));
    if ( empty($mc) ) return 'index';
    $arr = explode('.', $mc, 2);
    if ( isset( $arr[1] ) ) return $arr[1];
    else return 'index';
}
function json_success( $data = '' ) {
    $res = [ 'code' => 0 ];
    if ( $data ) $res['data'] = $data;
    echo json_encode($res);
    exit;
}
function json_error( $code, $message=null ) {
    if ( is_array($code) ) $res = $code;
    else $res = [ 'code' => $code, 'message' => $message ];
    echo json_encode( $res );
    exit;
}

/**
 * Glob recursively.
 *
 * @param $pattern
 * @param int $flags
 * @return array
 */
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}


/**
 *
 * Make an http POST request and return the response content and headers
 *
 *
 * @param $url - url of the requested script
 * @param $data - string or hash array of request variables
 * @return string - response body without headers
 */
function http_post ($url, $data)
{
    if ( ! is_array( $data ) ) $data_url = $data;
    else $data_url = http_build_query ($data);
    $data_len = strlen ($data_url);
    $context = stream_context_create (
        array (
            'http'=> array (
                'method'    => 'POST',
                'header'    =>"Content-Type: application/x-www-form-urlencoded\r\nConnection: close\r\nContent-Length: $data_len\r\n",
                'content'   => $data_url,
                'timeout'   => 10
            )
        )
    );

    $content = file_get_contents ( $url, false, $context );
    return $content;
}

/**
 * Returns 0 if the $str's length is between $min and $max.
 * @param $str
 * @param $min
 * @param int $max
 * @return int
 */
function validate_length( $str, $min, $max=10240000 ) {
    $len = strlen($str);
    if ( $len < $min ) return -1;
    else if ( $len > $max ) return -2;
    else return 0;
}

function validate_regex( $ex, $str ) {
    return preg_match( $ex, $str );
}

/**
 * Returns false if ID has right format. matches.
 *
 * @param $id
 * @return bool|string
 *      - false on success
 *      - error message on failure.
 */
function validate_id( $id ) {

    if ( validate_length( $id, 4, 64) ) return 'id-length-too-short-or-too-long';
    if ( ! preg_match("/^[a-zA-Z0-9\-_@\.]+$/", $id) ) return 'id-malformed';
    return false;

}

/**
 * Returns false if Email is valid.
 * @param $email
 * @return bool
 */
function validate_email( $email ) {
    if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) return false;
    else return 'email-malformed';
}

/**
 * Return false if Password is ok.
 * @param $password
 * @return bool
 */
function validate_password( $password ) {
    if ( validate_length( $password, 5 ) ) return 'password-too-short';
    return false;
}


/**
 * @warning it only work on powershell !!
 *
 * @param $text
 * @param $status
 * @return string
 * @throws Exception
 */
function colorize($text, $status) {
    if ( isCLI() ) {
        $out = "";
        switch($status) {
            case "SUCCESS":
                $out = "[42m"; //Green background
                break;
            case "FAILURE":
                $out = "[41m"; //Red background
                break;
            case "WARNING":
                $out = "[43m"; //Yellow background
                break;
            case "NOTE":
                $out = "[44m"; //Blue background
                break;
            default:
                throw new Exception("Invalid status: " . $status);
        }
        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
    else {
        switch($status) {
            case "SUCCESS":
                $color = "green"; //Green background
                break;
            case "FAILURE":
                $color = "red"; //Red background
                break;
            case "WARNING":
                $color = "yellow"; //Yellow background
                break;
            case "NOTE":
                $color = "blue"; //Blue background
                break;
            default:
                $color = "white";
        }
        return "<span style='background-color:$color;'>$text</span>";
    }

}




function test( $exp, $success, $failure = null ) {
    static $_test_count = 0;
    if ( $_test_count == 0 ) {
        echo colorize("\nxBase Unit Test Begins ...\n\n", "SUCCESS");
        if ( isWeb() ) echo '<br>';
    }
    $_test_count ++;
    echo "[$_test_count] ";
    if ( $exp ) echo "PASS: $success";
    else echo colorize("FAIL: $failure", 'FAILURE');
    echo "\n";
    if ( isWeb() ) {
        echo "<br />";
    }
}

function encrypt_password( $password ) {
    return md5( $password );
}

function error( $code, $message ) {
    return ['code'=>$code, 'message'=>$message];
}

function get_token_id( $user ) {
    $md5 = md5("$user[idx]-$user[id]-$user[email]-$user[password]");
    return "{$user['idx']}-$md5";
}
<?php

/**
 * @param $name
 * @param null $default
 * @return null
 */
function in( $name, $default = '' ) {
    if ( isset( $_REQUEST[ $name ] ) && $_REQUEST[ $name ] ) return $_REQUEST[$name];
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
function json_error( $code, $message ) {
    $res = [ 'code' => $code, 'message' => $message ];
    echo json_encode( $res );
    exit;
}
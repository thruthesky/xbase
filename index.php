<?php
header('Access-Control-Allow-Origin: *');
include_once "etc/ezSQL-master/shared/ez_sql_core.php";
include_once "etc/ezSQL-master/sqlite/ez_sql_sqlite3.php";
$db = new ezSQL_sqlite3('./','xbase.db');





// Create a table..
// $db->query("CREATE TABLE user ( idx INTEGER PRIMARY KEY, id varchar(64), password varchar(128), name varchar(32) )");

// $db->query("INSERT INTO user (id, password, name) VALUES ('charlse', 'abc123', 'Charlse')");

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'register' ) {

    $db->query("INSERT INTO user (id, password, name) VALUES( '$_REQUEST[id]', '$_REQUEST[password]', '$_REQUEST[name]')");

    $idx = $db->insert_id;

    $row = $db->get_row( "SELECT * FROM user WHERE idx=$idx", ARRAY_A);

    echo json_encode( $row );

}
else {

    $results = $db->get_results(" SELECT * FROM user ");
    echo json_encode( $results );

}

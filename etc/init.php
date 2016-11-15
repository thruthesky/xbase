<?php



$_sqlite_db = new Database('./','xbase.db');
$_sqlite_db->hide_errors();
function db() {
    global $_sqlite_db;
    return $_sqlite_db;
}

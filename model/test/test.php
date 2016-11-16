<?php
define( 'SERVER_URL', 'http://work.org/xbase/index.php');
class Test {
    public function all () {
        $files = rglob( '*_test.php' );
        // print_r($files);
        foreach( $files as $file ) {
            include_once $file;
            $class_name = ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $obj = new $class_name;
            $obj->run();
        }
        exit;
    }

    public function method() {
        list ( $model, $class, $method ) = explode( '.', in('method') );
        include_once "model/$model/$class.php";
        $class_name = ucfirst($class);
        $obj = new $class_name;
        $obj->$method();
        exit;
    }
}
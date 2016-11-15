<?php
class App {

    public function run() {
        $module = ucfirst(getModule());
        $controller = getController();
        $obj = new $module();
        $obj->$controller();
        json_error(-1119, 'Controller did not send JSON data');
    }
}
function app() {
    return new App();
}

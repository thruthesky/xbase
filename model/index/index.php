<?php

class Index {
    public function index() {
        json_success( [ 'data' => 'Welcome to xbase', 'stamp' => time() ]);
    }
}

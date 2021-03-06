<?php

class post_config_test {
    public function run() {

        $this->test_create();
        $this->update();
        $this->test_delete();
    }



    public function test_create() {
        $id = "test-forum-2";

        $this->delete( $id );

        // error test without id
        $_REQUEST['mc'] = 'post_config.create';
        $re = http_test( $_REQUEST );
        if ( is_error( $re ) ) test_pass("post_config()->create() failed: $re[message]");
        else test_fail("post_config create pass? it must be error because it has no post id");


        // error test without name
        $_REQUEST['id'] = $id;
        $re = http_test( $_REQUEST );
        if ( is_error( $re ) ) test_pass("post_config()->create() failed: $re[message]");
        else test_fail("post_config create pass? it must be error because it has no post name");

        // create one
        $_REQUEST['name'] = $id . '-name';
        $re = http_test( $_REQUEST );
        if ( is_success( $re ) ) test_pass("post_config()->create() success: idx: $re[data]");
        else {
		    test_fail("post_config()->create() failed: $re[message]");
	    }

    }


    public function update() {

        $id = 'update-test';
        $_REQUEST['mc'] = 'post_config.create';
        $_REQUEST['id'] = $id;
        $_REQUEST['name'] = 'test name';
        $_REQUEST['title'] = 'test title';


        $this->delete( $_REQUEST['id'] );


        // create
        $re = http_test( $_REQUEST );
        if ( is_success( $re ) ) test_pass("post_config_test::update() >> post_config()->create() success: idx: $re[data]");
        else test_fail("post_config_test::update() >> post_config()->create() failed: $re[message]");

        // update - wrong post id error test
        $_REQUEST['mc'] = 'post_config.edit';
        $_REQUEST['id'] = 'wrong-post-id-2';
        $_REQUEST['name'] = 'name changed';
        $re = http_test( $_REQUEST );
        if ( is_error( $re ) ) test_pass("wrong post id test");
        else test_fail("wrong-post-id test success? it must be failure.");

        $_REQUEST['id'] = $id;
        $re = http_test( $_REQUEST );
        if ( is_success( $re ) ) test_pass("post_config_test::update() >> post_config()->edit() success");
        else test_fail("post_config_test::update() >> post_config()->edit() failed: $re[message]");


    }

    private function test_delete()
    {
        $id = 'delete-test-3';
        $this->delete($id);
        $this->create($id);
        $e = $this->delete($id);
        if ( $e ) test_fail('test_delete() error: ' . $e);
    }



    /**
     * @param $id
     * @return bool
     *  - false on success
     *  - string on error.
     */
    private function create($id) {
        $re = http_test( ['mc'=>'post_config.create', 'id'=>$id, 'name'=>'name'] );
        if ( is_success($re) ) return false;
        else return $re['message'];
    }

    /**
     * @param $id
     * @return bool
     *  - false on success
     *  - string on error.
     */
    private function delete($id) {
        $re = http_test( [ 'mc' => 'post_config.delete', 'id' => $id ] );
        if ( is_success($re) ) return false;
        else return $re['message'];
    }
}

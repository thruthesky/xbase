<?php

class post_test {
    public function run() {

        $this->create();
        $this->update();
        $this->delete();
        $this->count();
        $this->search();
    }


    //

    private function get($idx) {
        // get edited post
        $data = [
            'mc'=>'post.get',
            'idx' => $idx,
            'fields'=>'*'
        ];
        return http_test( $data );
    }



    private function create()
    {
        // error test without title.
        $re = post()->create();
        if ( is_error( $re ) ) test_pass("post()->create() failed because no title: $re[message]");
        else test_fail("it should be error.");


        // error test with title but without post_id
        $_REQUEST['title'] = "This is title.";
        $re = post()->create();
        if ( is_error( $re ) ) test_pass("post()->create() failed : $re[message]");
        else test_fail("success: idx: $re");


        // create test with title and post id.
        $_REQUEST['post_id'] = 'helper';
        $re = post()->create();
        if ( is_error( $re ) ) test_fail("post()->create() failed : $re[message]");
        else test_pass("create success: idx: $re");


    }

    /// from here.
    private function update()
    {


        // write a post.
        $data =[
            'mc' => 'post.write',
            'post_id' => 'test',
            'title' => 'for update'
        ];
        $re = http_test( $data );
        if ( is_success($re) ) test_pass("post_test::update() created: idx: $re[data]");
        else test_fail("test create failed: $re[message]");
        $idx = $re['data'];

        // get the post
        $data = [
            'mc'=>'post.get',
            'idx' => $idx,
            'fields'=>'*'
        ];
        $re = http_test( $data );
        if ( is_success($re) ) test_pass('post_test::update() >> post.get success');
        else test_fail('post_test::update() >> post.get failed: ' . $re['message']);
        $before = $re['data'];

        // edit a post
        $data = [
            'mc' => 'post.edit',
            'idx' => $idx,
            'title' => 'new title'
        ];
        //print_r($data);
        $re = http_test( $data );
        if ( is_success($re) ) test_pass("post_test::update() edited: idx: ");
        else test_fail("test edit failed: $re[message]");

        // get edited post
        $data = [
            'mc'=>'post.get',
            'idx' => $idx,
            'fields'=>'*'
        ];
        $re = http_test( $data );
        if ( is_success($re) ) test_pass('post_test::update() >> post.get success');
        else test_fail('post_test::update() >> post.get failed: ' . $re['message']);
        $after = $re['data'];

        // compare before and after.
        if ( $before['title'] == $after['title'] ) test_fail("post has not updated.");
        else test_pass("post edited: before: '$before[title]', after: '$after[title]'");

    }

    ///
    public function delete()
    {

        // write a post.
        $data =[
            'mc' => 'post.write',
            'post_id' => 'test',
            'title' => 'post-delete-test: ' . time()
        ];
        $re = http_test( $data );
        if ( is_success($re) ) test_pass("post_test::delete() created: idx: $re[data]");
        else test_fail("post_test:delete() create failed: $re[message]");
        $idx = $re['data'];


        $re= $this->get( $idx );
        if ( is_success($re) ) test_pass('post_test::update() >> post.get success');
        else test_fail('post_test::update() >> post.get failed: ' . $re['message']);



        // delete the post

        $data =[
            'mc' => 'post.delete',
            'idx' => $idx
        ];
        $re = http_test( $data );
        if ( is_success($re) ) test_pass("post_test::delete() deleted");
        else test_fail("post_test:delete() delete failed: $re[message]");

        $re= $this->get( $idx );
        if ( is_error($re) ) test_pass('post_test::delete() >> post deleted');
        else test_fail('post_test::delete() >> post NOT deleted: ' . $re['message']);



    }

    private function search()
    {

        $title = "Hello World Test: ";

        // create 100 posts.
        $data = ['mc' => 'post.search'];
        $data['options'] = [
            'cond' => "title LIKE '%$title%'",
            'limit' => 1
        ];
        $re = http_test( $data );
        if ( is_success( $re ) && $re['data']['count'] ) {
            test_pass( "Like like 100 posts already created. search total count: {$re['data']['total_count']}");
        }
        else {
            $post = [];
            $post['post_id'] = 'test';
            $post['mc'] = 'post.write';
            for( $i = 0; $i < 100; $i ++ ) {
                $post['title'] = $title . $i;
                $re = http_test( $post );
                if ( is_success( $re ) ) echo ".";
                else {
                    test_pass ("post_test::search() >> post write failed: $re[message]");
                }
            }
        }

        // search users. total count 100. count 100.
        $data = ['mc' => 'post.search'];
        $data['options'] = [
            'cond' => "title LIKE '%$title%'",
            'limit' => 999
        ];
        $re = http_test( $data );
        if ( is_success( $re ) ) test_pass( "post search total count: {$re['data']['total_count']} searched: count: {$re['data']['count']}. It is only 100 since the number posts are only 100.");
        else test_fail("search failed: $re[message]");

        // count 33
        $data['options']['limit'] = 33;
        //print_r($data);
        $re = http_test( $data );
        //echo "re:\n";
        //print_r( $re['data']);
        if ( is_success( $re ) ) test_pass( "post search total count: {$re['data']['total_count']} searched: count: {$re['data']['count']}");
        else test_fail("search failed: $re[message]");


        // get 4th page. total count should be 100, searched count should be 1.
        $data['options']['page'] = 4;
        $re = http_test( $data );
        if ( is_success( $re ) ) test_pass( "post search total count: {$re['data']['total_count']} searched: count: {$re['data']['count']}");
        else test_fail("search failed: $re[message]");

    }

    private function count()
    {

        $re = http_test( [ 'mc'=>'post.count'] );
        if ( is_success($re) ) test_pass("post.count: $re[data]");
        else test_fail("post.count failed: $re[message]");

    }
}
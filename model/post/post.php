<?php

class post extends Entity {


    public function __construct()
    {
        parent::__construct();
        $this->setTable( 'post_data' );
        $this->setSearchableFields('*');
    }

    /**
     * Restful interface
     *
     */
    public function write() {
        json($this->create());
    }

    /**
     * Restful interface
     */
    public function edit() {
        json( $this->update() );
    }

    /**
     * @return array|int
     *      - post.idx on success
     *      - error data on failure.
     *
     * @Attention NOT Restful interface. @use post::write() for restful interface.
     *
     * @condition
     *      - on create(), it checks if 'post_id' exists.
     */
    public function create() {
        $data = $this->getRequestPostData();
        if ( $error = $this->validate_post_data( $data ) ) return $error;
        $config = post_config()->get( $data['post_id'] );
        if ( empty($config) ) return error(-40104, 'post-config-does-not-exist');
        $data['user_id'] = my('id');
        $data['created'] = time();
        $data['updated'] = time();
        $idx = db()->insert('post_data', $data);

        if ( $idx ) return $idx;
        else return error(-40100, 'failed-to-post-create');
    }

    /**
     * in('idx') - is the post.idx to edit.
     *
     * @return mixed
     *
     * @Attention NOT Restful interface. @use post::edit() for restful interface.
     *
     */
    private function update()
    {
        $data = $this->getRequestPostData();
        if ( $error = $this->validate_post_data( $data, true ) ) return $error;
        // $data['user_id'] = my('id'); // for admin edit.
        $data['updated'] = time();
        if ( ! isset($data['idx']) ) return error( -40564, 'input-idx');
        $post = $this->get( $data['idx'] );

        if ( $error = $this->checkPermission( $post, $data['password'] ) ) return $error;
        db()->update( $this->getTable(), $data, "idx=$data[idx]");

        return false;
    }

    /**
     * Checks permission on the $post with $password or logged in user's account.
     * @param $post
     * @param $password - plain text password! The password must plain text. not encrypted.
     * @return array|bool
     *      - false on success.
     *      - error array on failure.
     *
     * @code-flow
     * 1. password match
     * 2. login user id match.
     */
    private function checkPermission( $post, $password ) {
	if ( empty($post) ) return error( -40568, 'post-not-exist' );
	$password = encrypt_password( $password );
        if ( isset( $password ) && $password ) {
            if ( $password == $post['password'] ) return false; // success. permission granted.
            else return error( -40564, 'wrong-password' );
        }
        else if ( $post['user_id'] == 'anonymous' ) return error( -40565, 'login-or-input-password' );
        else if ( $post['user_id'] != my('id') ) return error( -40567, 'not-your-post' );
        return false; // success. this is your post. permission granted.
    }

    public function validate_post_data( $data, $edit = false ) {
        $create = ! $edit;
        if ( $create ) {
            if ( empty( $data['title'] ) ) return error( -40200, 'input title');
            if ( empty( $data['post_id'] ) ) return error( -40201, 'input post_id');
        }
        if ( $edit ) {
            if ( isset( $data['idx'] ) && empty( $data['idx'] ) ) return error( -40204, 'input idx');
        }
        return false;
    }

    private function getRequestPostData()
    {
        $data = [];

        /*
        if ( in('idx') ) $data['idx'] = in('idx');
        if ( in('post_id') ) $data['post_id'] = in('post_id');
        if ( in('password') ) $data['password'] = in('password');
        if ( in('title') ) $data['title'] = in('title');
        if ( in('content') ) $data['content'] = in('content');

        if ( in('email') ) $data['email'] = in('email');
        if ( in('first_name') ) $data['first_name'] = in('first_name');
        if ( in('middle_name') ) $data['middle_name'] = in('middle_name');
        if ( in('last_name') ) $data['last_name'] = in('last_name');
        if ( in('gender') ) $data['gender'] = in('gender');
        if ( in('birth_year') ) $data['birth_year'] = in('birth_year');
        */

        $names = [ 'idx', 'post_id', 'password', 'title', 'content',
            'email', 'first_name', 'middle_name', 'last_name', 'gender',
            'birth_year', 'birth_month', 'birth_day', 'country', 'province', 'city',
            'address', 'mobile', 'landline'
        ];

        foreach( $names as $name ) {
            if ( in($name) ) $data[ $name ] = in($name);
        }

	if ( isset( $data['password'] ) && $data['password'] ) $data['password'] = md5( $data['password'] ); // @todo need to improve security by putting secret pass-phrase.


        for( $i = 1; $i <= 10; $i++ ) {
            $v = "category_$i";
            if ( in( $v ) ) $data[ $v ] = in( $v );
        }


        for( $i = 1; $i <= 10; $i++ ) {
            $v = "extra_$i";
            if ( in( $v ) ) $data[ $v ] = in( $v );
        }
        for( $i = 1; $i <= 5; $i++ ) {
            $v = "attachment_$i";
            if ( in( $v ) ) $data[ $v ] = in( $v );
        }

        return $data;
    }

    /**
     *
     *
     * @note This can be used as HTTP interface.
     *
     * @param null $idx
     * @return void
     *
     * @todo test.
     */
    public function delete( $idx = null ) {
        if ( in('mc') ) {
            $idx = in('idx');
            if ( empty($idx) ) json_error(-40222, "input-idx");
        }

        $post = $this->get( $idx );
        if ( $error = $this->checkPermission( $post, in('password') ) ) json_error($error);

        $re = parent::delete( $idx );
        if ( $re === false ) json_success();
        else json_error( -40223, "post-delete-failed");
    }

}

function post() {
    return new post();
}

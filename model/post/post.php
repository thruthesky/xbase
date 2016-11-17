<?php

class post extends Entity {


    public function __construct()
    {
        parent::__construct();
        $this->setTable( 'post_data' );
        $this->setSearchableFields('idx,post_id,user_id,title,content,created,updated');
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
     */
    public function create() {
        $data = $this->getRequestPostData();
        if ( $error = $this->validate_post_data( $data ) ) return $error;
        $data['user_id'] = my('id');
        $data['created'] = time();
        $data['updated'] = time();
        $idx = db()->insert('post_data', $data);

        if ( $idx ) return $idx;
        else return error(-40100, 'failed to create post reord.');
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
        if ( $post['user_id'] != my('id') ) return error(-40560, 'not-your-post');

        db()->update( $this->getTable(), $data, "idx=$data[idx]");

        return false;
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

        if ( in('idx') ) $data['idx'] = in('idx');
        if ( in('post_id') ) $data['post_id'] = in('post_id');
        if ( in('title') ) $data['title'] = in('title');
        if ( in('content') ) $data['content'] = in('content');

        return $data;
    }

    /**
     * @param null $idx
     * @return void
     */
    public function delete( $idx = null ) {
        if ( in('mc') ) {
            $idx = in('idx');
            if ( empty($idx) ) json_error(-40222, "input-idx");
        }
        // if you are admin, pass
        $post = $this->get( $idx );
        if ( $post['user_id'] == my('id') ) { // yes it is yours.

        }
        else {
            // @todo check if admin
            json_error( -40224, 'not-your-post');
        }
        $re = parent::delete( $idx );
        if ( $re === false ) json_success();
        else json_error( -40223, "post-delete-failed");
    }

}

function post() {
    return new post();
}
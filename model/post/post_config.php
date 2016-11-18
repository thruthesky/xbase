<?php
/**
 * Class post_config
 *
 * @see @example How to get post_config through REST API
 *
 *      post/post_test.php::get_post_config();
 *
 *
 *
 */
class post_config extends Entity {

    public function __construct()
    {
        parent::__construct();
        $this->setTable( 'post_config' );
    }

    /**
     *
     * @see @example post/post_test.php::create_post_config();
     *
     */
    public function create() {
        $data = $this->getRequestPostConfigData();
        if ( $error = $this->validate_post_config_data( $data ) ) json( $error );

        $config = post_config()->get( $data['id'] );
        if ( $config ) json_error( -40324, "post-id-in-use");
        $idx = db()->insert('post_config', $data);
        if ( $idx ) json_success( $idx );
        else json_error( -40320, "DB ERROR: $idx");
    }

    public function edit() {
        $data = $this->getRequestPostConfigData();
        if ( $error = $this->validate_post_config_data( $data, true ) ) json( $error );
        $config = post_config()->get( $data['id'] );
        if ( ! $config ) json_error( -40324, "wrong-post-id");
        $id = db()->escape($data['id']);
        unset( $data['id'] );
        db()->update('post_config', $data, "id='$id'");
        json_success( );
    }

    /**
     *
     * @Warning @todo security.
     * @param null $idx
     * @return bool|string
     */
    public function delete( $idx = null ) {
        if ( $idx ) {
            return parent::delete( $idx );
        }
        else if ( in('id') ) {
            if ( $error = parent::delete( in('id') ) ) json_error(-40345, $error );
            else json_success(in('id'));
        }
        else json_error(-40340, "input-post-id");
        return false;
    }

    private function getRequestPostConfigData()
    {
        $config = [];
        if ( in('id') ) $config['id'] = in('id');
        if ( in('name') ) $config['name'] = in('name');
        if ( in('title') ) $config['title'] = in('title');
        if ( in('description') ) $config['description'] = in('description');
        return $config;
    }

    private function validate_post_config_data( $data, $edit = false ) {
        $create = ! $edit;
        // post id is needed for edit.
        if ( empty( $data['id'] ) ) json_error( -40301, 'input-id');
        if ( empty( $data['name'] ) ) json_error( -40302, 'input name');
        // if ( empty( $data['title'] ) ) json_error( -40303, 'input title');
        return false;
    }
}

function post_config() {
    return new post_config();
}
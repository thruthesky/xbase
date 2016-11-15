<?php

class User {
    /**
     *
     * @code
     *      http://work.org/xbase/?mc=user.fetch
     * @endcode
     *
     */
    public function fetch() {
        $users = db()->get_results( "SELECT * FROM user");
        json_success( $users );
    }

    public function getRequestedUserData() {

        $user = [];
        $user['id'] = in('id');
        $user['password'] = in('password');
        $user['name'] = in('name');
        return $user;

    }

    /**
     * Register the user and returns the User record.
     */
    public function register() {

        $idx = db()->insert( 'user', $this->getRequestedUserData() );
        json_success( $this->get($idx) );

    }


    /**
     * Update user information
     */
    public function update() {
        $idx = db()->update( 'user', $this->getRequestedUserData(), "id='" . esc('id') . "'" );
        // db()->debug();

        json_success();
    }

    /**
     * Returns the user record.
     *
     * @param $idx
     * @return array|null
     */
    public function get( $idx ) {
        return db()->get_row( "SELECT * FROM user WHERE idx=$idx");
    }
}
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
        $users = db()->get_results( "SELECT * FROM user", ARRAY_A);
        json_success( $users );
    }

    /**
     *
     * Get user create/update data from HTTP Query or STDIN
     * @attention it exists if there is any error.
     * @return array
     * @see user/user_crud_test::validate()
     */
    public function getRequestedUserData() {

        $user = [];
        $user['id'] = in('id');
        $user['password'] = in('password');
        $user['email'] = in('email');
        $user['name'] = in('name');


        return $user;

    }

    /**
     *
     *
     *
     * @param $user
     * @return bool|string - false on success
     * - false on success
     * - string of Error Message on failure.
     */
    public function validate_user_data( $user ) {


        if ( ! isset( $user['id'] ) ) return 'input id';
        if ( ! isset( $user['password'] ) ) return 'input password';
        if ( ! isset( $user['email'] ) ) return 'input email';

        if ( $error = validate_id( $user['id'] ) ) return $error;
        if ( validate_email( $user['email'] ) ) return $error;
        if ( $error = validate_password( $user['password'] ) ) return $error;
        if ( $this->get( $user['id'] ) ) return 'id-exists';
        if ( $this->getByEmail( $user['email'] ) ) return 'email-exists';


        return false;
    }
    /**
     * Register the user and returns the User record.
     *
     */
    public function register() {

        $user_idx = $this->create( $this->getRequestedUserData() );
        if ( is_numeric( $user_idx ) ) json_success( $this->get($user_idx) );
        else json_error( -500, $user_idx );
    }


    /**
     * Create a user record on database.
     *
     * @note This method does not interact with USER INPUT directly.
     *
     *
     * @param $user
     * @return mixed
     *      - user.idx on success
     *      - Error message as string on failure.
     *
     * @note this method may 'exit()' if there is database query error.
     *
     */
    public function create( $user ) {

        if ( $error = $this->validate_user_data( $user ) ) return $error;
        $user['password'] = encrypt_password( $user['password'] );
        $idx = db()->insert( 'user',  $user );
        if ( is_numeric($idx) ) return $idx;
        return 'real_register() failed';
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
     * @param $idx - user.idx or user.id
     * @return array|null
     */
    public function get( $idx ) {
        if ( is_numeric( $idx ) ) return db()->get_row( "SELECT * FROM user WHERE idx=$idx", ARRAY_A);
        else {
            $id = db()->escape( $idx );
            return db()->get_row( "SELECT * FROM user WHERE id='$id'", ARRAY_A);
        }
    }

    public function getByEmail ( $email ) {
        $email = db()->escape( $email );
        return db()->get_row( "SELECT * FROM user WHERE email='$email'", ARRAY_A );
    }

    /**
     *
     * echo JSON data based on getLoginToken()
     *
     * @param $id
     * @param $password
     */
    public function login($id, $password)
    {
        $re = $this->getLoginToken( $id, $password );
        if ( is_array( $re ) ) json_error( $re );
        else json_success( $re );
    }

    /**
     * Returns user login token.
     * @note with login token, user can authenticate himself.
     * @warning if you need security, you should use SSL ( https:// )
     * @param $id
     * @param $password
     * @return mixed
     *      - string of token on success
     *      - hash array of error code and error message on failure.
     */
    public function getLoginToken( $id, $password ) {
        if ( $error = validate_id( $id ) ) return error( -20075, $error );
        $user = $this->get( $id );
        if ( empty($user) ) return error(-20070, 'user-not-exist');
        if ( $user['password'] != encrypt_password( $password ) ) return error( -20071, 'wrong-password');

        return get_token_id( $user );

    }
}

function user() {
    return new User();
}
<?php

class user_crud_test {
    public function run() {
        // echo "Hello, user crud!\n";


        $this->validation();
        $this->register();

        $this->login();
    }

    /**
     * @see user/user::getRequestedUserData()
     */
    public function validation() {

        test( $error = validate_id( 'abc' ), "validate_id('abc') failed: $error" );
        test( ! validate_id( '1234' ), "validate_id('1234') success" );
        test( $error = validate_email( 'abc@def' ), "validate_email('abc@def') failed: $error" );
        test( ! validate_email( 'abc@def.com' ), "validate_email('abc@def.com') success" );
        test( $error = validate_password( '1234' ), "validate_password('1234') failed: $error" );
        test( ! validate_password( '12345' ), "validate_password('12345') $error" );

    }

    /**
     *
     * ex) \app\php\php index.php "mc=test.method&method=user.user_crud_test.register&id=myid2&password=12345&email=abc@def.co"
     */
    public function register() {
        $user = [];
        $re = user()->create( $user );
        test( ! is_numeric($re), 'register() failed', "register() ok? why? it should be failed." );


        $user = [ 'id' => 'id' . md5(time()), 'password' => '12345' ];
        $re = user()->create( $user );
        test( ! is_numeric($re), "register() failed: $re", "register() ok? why? it should be failed." );

        $user['email'] = "$user[id]@gmail.com";
        $re = user()->create( $user );
        test( is_numeric($re), "register() success", "register() failed: $re" );


        $re = user()->create( $user );
        test( $re == 'id-exists', "register() failed", "register() success? why? : $re" );


        $user['id'] = $user['id'] . '2';
        $re = user()->create( $user );
        test( $re == 'email-exists', "register() failed", "register() success? why? : $re" );


        $user['email'] = "$user[id]@gmail.com";
        $re = user()->create( $user );
        test( is_numeric($re), "register() success with change of id and email", "register() failed: $re" );

    }


    public function login() {
        $user = [
            'id' => 'login-test-' . md5(time()),
            'password' => 'abc123'
        ];
        $user['email'] = "$user[id]@gmail.com";
        $user_idx = user()->create( $user );
        test( is_numeric($user_idx), "register() success for login", "register() failed: $user_idx" );

        $re = user()->getLoginToken( $user['id'] . 'noexists' , $user['password'] );
        test( is_array($re), "login failed: $re[code], $re[message]");

        $re = user()->getLoginToken( $user['id'], $user['password'] . ' fail' );
        test( is_array($re), "login failed: $re[code], $re[message]");

        $re = user()->getLoginToken( $user['id'], $user['password'] );
        test( is_string($re), "got login token: $re", "login failed");


    }
}
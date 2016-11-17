<?php

class user_crud_test {
    public function run() {
        // echo "Hello, user crud!\n";


        $this->validation();
        $this->register();
        $this->update();
        $this->changePassword();

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
        test( ! validate_password( '12345' ), "validate_password('12345') : success" );

    }

    /**
     *
     * ex) \app\php\php index.php "mc=test.method&method=user.user_crud_test.register&id=myid2&password=12345&email=abc@def.co"
     */
    public function register() {
        $user = [];
        $re = user()->create( $user );
        test( ! is_numeric($re), 'register() failed', "user->create() ok? why? it should be failed." );


        $user = [ 'id' => 'id' . md5(time()), 'password' => '12345' ];
        $re = user()->create( $user );
        test( ! is_numeric($re), "user->create() failed: $re", "user->create() ok? why? it should be failed." );

        $user['email'] = "$user[id]@gmail.com";
        $re = user()->create( $user );
        test( is_numeric($re), "user->create() success", "user->create() failed: $re" );


        $re = user()->create( $user );
        test( $re == 'id-exists', "user->create() failed", "user->create() success? why? : $re" );


        $user['id'] = $user['id'] . '2';
        $re = user()->create( $user );
        test( $re == 'email-exists', "user->create() failed", "user->create() success? why? : $re" );


        $user['email'] = "$user[id]@gmail.com";
        $re = user()->create( $user );
        test( is_numeric($re), "user->create() success with change of id and email", "user->create() failed: $re" );


        $data = array_merge( $user, ['mc'=>'user.register'] );
        $res = http_test( $data );
        if ( is_error( $res ) ) test_pass("register through HTTP failed: $res[message]");
        else test_fail("restration on HTTP usccess? why?");



        $user['id'] = $user['id'] . '3';
        $user['email'] = "$user[id]@gmail.com";
        $data = array_merge( $user, ['mc'=>'user.register'] );
        $res = http_post( SERVER_URL, $data, true);
        test( $res['code'] == 0, "register through HTTP success: session_id: $res[data]", "restration on HTTP failed? why?");

    }


    public function update() {


        $id = "id-update-test-2";
        $password = '12345a';
        $email = "$id@gmail.com";

        user()->delete( $id );

        // register
        $user = [ 'id' => $id, 'password' => $password, 'email' => $email];
        $user_idx = user()->create( $user );
        test( is_numeric($user_idx), 'user()->create() ok for update test', "failed for user()->create() : $user_idx" );

        // login
        $data = array_merge( $user, ['mc'=>'user.login'] );
        $res = http_post( SERVER_URL, $data, true);
        $m = $res['code'] ? $res['message'] : $res['data'];
        test( $res['code'] == 0, "login ok for update: session_id: $m", "login on HTTP failed: $m");
        if ( $res['code'] == 0 ) $session_id = $res['data'];
        else $session_id = null;

        // update email
        unset( $user['id'] );
        unset( $user['password'] );
        $user['email'] = "$id@naver.com";
        $data = array_merge( $user, ['mc'=>'user.edit', 'session_id'=>$session_id] );
        $res = http_test( $data );

        $m = $res['code'] ? $res['message'] : $res['data'];
        test( $res['code'] == 0 , "edit for update ok: new session_id: $m", "user.edit failed: ($res[code]) $m");
        $new_session_id = $m;

        // login again and compare new session id.
        $user['id'] = $id;
        $user['password'] = $password;
        $data = array_merge( $user, ['mc'=>'user.login'] );
        $res = http_post( SERVER_URL, $data, true);
        $m = $res['code'] ? $res['message'] : $res['data'];
        // print_r($user);
        test( $res['code'] == 0, "login ok for check new session_id: $m", "login again on HTTP failed: $m");
        test( $new_session_id == $m, "new session id ok", "new session id is not correct: $m");

        // get updated my('email')
        $res = http_post(SERVER_URL, ['mc' => 'user.my', 'session_id' => $new_session_id, 'field'=>'email'], true);
        $m = $res['code'] ? $res['message'] : $res['data'];
        test( $res['code'] == 0 && $m == $user['email'], "user.my( email ) success. id: $m", "failed to get email: $m ( $new_session_id )");


        // update password
        unset( $user['id'] );
        $user['password'] = 'new-password';
        $data = array_merge( $user, ['mc'=>'user.edit', 'session_id'=>$new_session_id] );
        $res = http_post( SERVER_URL, $data, true);
        $m = $res['code'] ? $res['message'] : $res['data'];
        test( $res['code'] == 0 , "edit for password ok: new session_id: $m", "user.edit failed: ($res[code]) $m");
        $new_session_id = $m;

        // login again
        $user['id'] = $id;
        $data = array_merge( $user, ['mc'=>'user.login'] );
        $res = http_post( SERVER_URL, $data, true);
        $m = $res['code'] ? $res['message'] : $res['data'];
        test( $res['code'] == 0, "login ok after update password: $m", "login again on HTTP failed: $m");
        test( $new_session_id == $m, "Updated session session id ok after password changed.", "new session id is not correct: $m");







    }

    private function changePassword()
    {
    }


}
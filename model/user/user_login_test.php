<?php

class user_login_test {
    public function run() {
        $this->login();
    }



    public function login() {
        $user = [
            'id' => 'login-test-' . md5(time()),
            'password' => 'abc123'
        ];
        $user['email'] = "$user[id]@gmail.com";
        $user_idx = user()->create( $user );
        test( is_numeric($user_idx), "register() success for login", "register() failed: $user_idx" );

        $re = user()->getSessionID( $user['id'] . 'noexists' , $user['password'] );
        test( is_array($re), "login failed: $re[code], $re[message]");

        $re = user()->getSessionID( $user['id'], $user['password'] . ' fail' );
        test( is_array($re), "login failed: $re[code], $re[message]");

        $re = user()->getSessionID( $user['id'], $user['password'] );
        test( is_string($re), "Login success: got login token: $re", "login failed");

        $session_id = $re;

        $res = http_post(SERVER_URL, ['mc' => 'user.login', 'id' => $user['id'], 'password' => $user['password'] ], true);
        test( $res['code'] == 0 && $session_id == $res['data'], "user.login() success. session-id: $res[data]", "failed on user.login()");




        $res = http_post(SERVER_URL, ['mc' => 'user.my', 'session_id' => $session_id], true);
        test( $res['code'] == 0, "user.my() success. id: {$res['data']['id']}", "failed on user.my()");

        $res = http_post(SERVER_URL, ['mc' => 'user.my', 'session_id' => $session_id, 'field'=>'email'], true);
        test( $res['code'] == 0 && $res['data'] == $user['email'], "user.my( email ) success. id: {$res['data']}", "failed to get email");

    }

}
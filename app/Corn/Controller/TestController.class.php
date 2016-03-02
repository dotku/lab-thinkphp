<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
    public function index() {

    }
    public function model() {
        $model_user = D('User');
        // $model_user->sayHelloFromModel();
        $data_user['id'] = 3;
        $data_user['password'] = md5('p@ssword123');
        $data_user['username'] = 'username4';
        /*
        try {
            $model_user->add($data_user);
        } catch (Exception $e){
            var_dump($e);
        }
        */
        $model_user->save($data_user);
    }
    public function model_mediaVideo() {

    }
}
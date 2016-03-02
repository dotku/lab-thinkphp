<?php 
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
    public function sayHelloFromModel($data='',$options=array()){
        var_dump('hello world!');
    }
    public function save($data='',$options=array()){
        var_dump($data);
        parent::save($data,$options);
    }
}
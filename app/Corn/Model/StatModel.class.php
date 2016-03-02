<?php 
namespace Corn\Model;
use Think\Model;
class StatModel extends Model {
    public function save($data='',$options=array()){
        var_dump($data);
        parent::save($data,$options);
    }
}
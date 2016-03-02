<?php
namespace Corn\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function testModel(){
        $model_stat = D('stat');
        $model_stat->save();
    }
}
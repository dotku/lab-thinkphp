<?php
namespace Common\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index() {
        var_dump('Common Controller');
        $this->display();
    }
}
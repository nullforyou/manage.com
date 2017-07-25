<?php
namespace app\manage\controller;

class Accessory extends System{
    
    public function file($dir = ''){
        $list = \app\manage\extend\Accessory::instance()->getdir(decode($dir));
        return view('accessory/list', ['list'=>$list]);
    }
}

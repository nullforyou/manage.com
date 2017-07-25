<?php
namespace app\manage\controller;

use think\Cache;
use think\Db;

class Settings extends System{
    
    
    public function clearCache(){
        Cache::clear();
        $this->success();
    }
    
    public function systemConfig(){
        if ($this->request->isAjax()) {
            $config = $this->request->post('config/a');
            foreach ($config as $key => $val) {
                if (Db::name('system_settings')->where('param_name', $key)->count()) {
                    Db::name('system_settings')->where('param_name', $key)->update(['param_value' => $val]);
                } else {
                    Db::name('system_settings')->insert(['param_name' => $key, 'param_value' => $val]);
                }
            }
            Cache::clear();
            $this->success('操作成功');
        } else {
            return view('settings/systemconfig', ['settings'=>hasSettings(false)]);
        }
    }
}
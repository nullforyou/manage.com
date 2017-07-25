<?php
/**
 *扩展之权限
 */
namespace app\manage\extend;
use \think\Config;
use \think\Session;

class Permission{
    
    private $menuConfig;
    
    private static $_instance;
    
    public static function instance(){
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    private function __construct(){
        $this->menuConfig = Config::load(CONF_PATH.'manage' . DIRECTORY_SEPARATOR . 'menus.php', '', 'manage');
    }
    
    /**
     *获取所有的权限
     */
    public function getAll(){
        return $this->menuConfig['menus'];
    }
    
    /**
     *获取当前用户菜单
     */
    public function getPermissionMenus($auth, $isSuper, $all = true){
        if ($isSuper == 1) {
            return $this->getAll();
        }
        if (!is_array($auth)) {
            $auth = explode(',', $auth);
        }
        $auth = array_flip($auth);
        
        $menus = [];
        foreach ($this->getAll() as $key => $val) {
            $temp = [];
            foreach ($val['menu'] as $v) {
                if ($all === true) {
                    if (isset($auth[$v['as']])) {
                        $temp['menu'][] = $v;
                    }
                } else {
                    if ($v['display'] and isset($auth[$v['as']])) {
                        $temp['menu'][] = $v;
                    }
                }
            }
            if ($temp) {
                $temp['label'] = $val['label'];
                $temp['icon'] = $val['icon'];
                $menus[$key] = $temp;
            }
        }
        return $menus;
    }
    
    /**
     *判断某一方法是否有操作权限
     */
    public function isAuth($module, $controller, $action, $method){
        if (in_array($module . '/' . $controller, $this->menuConfig['no_auth_class'])) {
            return true;
        }
        $target = $module . '/' . $controller . '/' . $action;
        
        if (in_array($target, $this->menuConfig['no_auth_method'])) {
            return true;
        }
        $manageSession = Session::get('manage', 'management');
        if (!empty($manageSession['admin']['user_super'])) {
            return true;
        }
        if (empty($manageSession['admin']['auth'])) {
            $auth = [];
        } else {
            $auth = array_flip(explode(',', $manageSession['admin']['auth']));
        }
        foreach ($this->getAll() as $val) {
            foreach ($val['menu'] as $v) {
                if ((isset($auth[$v['as']])) && (false !== strpos($v['action'], $target)) && (false !== strpos($v['method'], $method))) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }
}
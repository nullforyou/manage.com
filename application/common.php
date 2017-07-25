<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
error_reporting(E_ALL & ~E_NOTICE);
// 应用公共文件
if (! function_exists('getValByKey')) {
    /**
    *根据数组某字段为键值返回数组
    */
    function getValByKey($field = false, $array, $valueField = false, $hasKey = true){
        if (false === $field && false === $valueField) {
            return $array;
        }
        $new_array = array();
        foreach ($array as $val) {
            if (is_object($val)) {
                if ($val instanceof \think\Model) {
                    $val = $val->toArray();
                } else {
                    $val = get_object_vars($val);
                }
            }
            if (false === $field) {
                $new_array[] = $val[$valueField];
            } else {
                if (false === $valueField) {
                    $new_array[$val[$field]] = $val;
                    if (false === $hasKey) {
                        unset($new_array[$val[$field]][$field]);
                    }
                } else {
                    $new_array[$val[$field]] = $val[$valueField];
                }
            }
        }
        return $new_array;
    }
}

if (! function_exists('hasSettings')) {
	function hasSettings($param_name){
		static $_settings;
		if (empty($_settings)) {
			//读取系统配置缓存
			$db = \think\Db::name('system_settings');
			$_settings = \think\Cache::remember('sys_settings', function() use ($db) {
				return getValByKey('param_name', $db->select());
			});
		}
		if (false === $param_name) {
			return $_settings;
		} else {
			if ($_settings[$param_name]['param_value'] == 'disabled') {
				return false;
			} else {
				return true;
			}
		}
	}
}

/**
 *移动文件
 */
if (! function_exists('moveTempFile')) {
	function moveTempFile($file, $id){
		if (false === strpos($file, DOMAIN . DS .'additiontodo' . DS . 'temp' . DS . 'atlas')) {
			return $file;
		}
		$newFile = str_replace(
			DOMAIN . DS .'additiontodo' . DS . 'temp' . DS . 'atlas',
			DOMAIN . DS .'additiontodo' . DS . 'atlas' . DS . $id,
			$file);
		$dirFile = str_replace('http://'.DOMAIN . DS . 'additiontodo', ROOT_PATH . 'writable', $file);
		$newDirFile = str_replace('http://'.DOMAIN . DS . 'additiontodo', ROOT_PATH . 'writable', $newFile);
		@mkdir(dirname($newDirFile), 0777, true);
		copy($dirFile, $newDirFile);
		unlink($dirFile);
		return $newFile;
	}
}

/**
 *判断是否有权限
 */
if (! function_exists('hasAuth')) {
	function hasAuth(string $module, string $controller, string $action, string $method){
		return \app\manage\extend\Permission::instance()->isAuth($module, $controller, $action, $method);
	}
}

/**
 *根据ip获取省市 123.125.114.144
 */
function getRegionByIp($ip){
	if(empty($ip)){  
		return false; 
	}  
	$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
	if(empty($res)){ return false; }  
	$jsonMatches = array();  
	preg_match('#\{.+?\}#', $res, $jsonMatches);  
	if(!isset($jsonMatches[0])){
		return false;
	}  
	$json = json_decode($jsonMatches[0], true);  
	if(isset($json['ret']) && $json['ret'] == 1){  
		$json['ip'] = $ip;  
		unset($json['ret']);  
	}else{  
		return false;  
	}  
	return $json;  
}
/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}
/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = 'cxphp') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}
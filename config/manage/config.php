<?php
return [
    
    'site' => [
        'title' => '测试',
        'keywords' => '',
        'description' => '',
    ],
    
    'region_json_file' => ROOT_PATH . 'public' . DS . 'static' . DS . 'theme' . DS . 'json' . DS . 'region.json',
    'region_json_file_web' => 'http://' . DOMAIN . DS . 'theme' . DS . 'json' . DS . 'region.json',
    
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PLUGIN__' => 'http://' . DOMAIN . '/plugins',
        '__PUBLIC__'=>'http://' . DOMAIN . '/theme',
        '__CSS__'=>'http://' . DOMAIN . '/theme/default/css',
        '__JS__'=>'http://' . DOMAIN. '/theme/default/js',
        '__ROOT__' => 'http://' . DOMAIN,
    ],
    
    'template' => [
        
        'type'         => 'Think', // 模板引擎类型 支持 php think 支持扩展
        
        'view_path'    => APP_PATH . 'manage/view/', // 模板路径
        
        'view_suffix'  => 'html', // 模板后缀
        
        'view_depr'    => DS, // 模板文件名分隔符
        
        'tpl_begin'    => '<{', // 模板引擎普通标签开始标记
        
        'tpl_end'      => '}>', // 模板引擎普通标签结束标记
        
        'taglib_begin' => '<{', // 标签库标签开始标记
        
        'taglib_end'   => '}>', // 标签库标签结束标记
         
        'strip_space'  => false, // 去除模板文件里面的html空格与换行
         
        'tpl_cache'    => false, // 开启模板编译缓存
    ],
    
    /*用于数据储存而非cache 和 session*/
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379
    ],
    
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY', 
        // 验证码字体大小(px)
        'fontSize' => 25, 
        // 是否画混淆曲线
        'useCurve' => true, 
         // 验证码图片高度
        'imageH'   => 30,
        // 验证码图片宽度
        'imageW'   => 100, 
        // 验证码位数
        'length'   => 5, 
        // 验证成功后是否重置        
        'reset'    => true
    ],
];
<?php
return [
    
    'site' => [
        'intro' => '08Up',
        'baseurl' => 'http://'.DOMAIN,
        'title' => '08up.com',
        'record_varchar' => 'XXXXXXXXXXXXXXXXXXXX',
        'keywords' => '',
        'description' => '',
    ],
    
    
    //使用页面模板
    'view_template' => 'No.7_qzhai',
    
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        
        
        //'__PLUGIN__' => 'http://' . DOMAIN . '/plugins',
        //'__PUBLIC__'=>'http://' . DOMAIN . '/theme/default',
        //'__CSS__'=>'http://' . DOMAIN . '/theme/default/css',
        //'__JS__'=>'http://' . DOMAIN. '/theme/default/js',
        //'__IMAGE__'=>'http://' . DOMAIN . '/theme/default/image',
        //'__ROOT__' => 'http://' . DOMAIN,
        
        
        '__PLUGIN__' => 'http://' . DOMAIN . '/plugins',
        '__PUBLIC__'=>'http://' . DOMAIN . '/theme',
        '__CSS__'=>'http://' . DOMAIN . '/theme/No.7_qzhai/css',
        '__JS__'=>'http://' . DOMAIN. '/theme/No.7_qzhai/js',
        '__IMAGE__'=>'http://' . DOMAIN . '/theme/No.7_qzhai/image',
        '__ROOT__' => 'http://' . DOMAIN,
    ],
    
    
    'template' => [
        
        'type'         => 'Think', // 模板引擎类型 支持 php think 支持扩展
        
        'view_path'    => APP_PATH . 'index/view/', // 模板路径
        
        'view_suffix'  => 'html', // 模板后缀
        
        'view_depr'    => DS, // 模板文件名分隔符
        
        'tpl_begin'    => '<{', // 模板引擎普通标签开始标记
        
        'tpl_end'      => '}>', // 模板引擎普通标签结束标记
        
        'taglib_begin' => '<{', // 标签库标签开始标记
        
        'taglib_end'   => '}>', // 标签库标签结束标记
         
        'strip_space'  => false, // 去除模板文件里面的html空格与换行
         
        'tpl_cache'    => false, // 开启模板编译缓存
    ],
    
    //路由
    'url_route_on'  =>  true,
    'url_route_must'=>  false,
];
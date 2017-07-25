<?php

use think\Route;

Route::get('search', 'index/Index/search', ['method'=>'get', 'ext'=>'html']); //全站搜索

Route::get('flag', 'index/Index/flagList', ['method'=>'get', 'ext'=>'html']); //标签列表

Route::get('cat', 'index/Index/categoryList', ['method'=>'get', 'ext'=>'html']); //分类列表


// --------------文章 -----------

Route::rule('article/:id', 'index/Article/article', 'get', ['ext'=>'html'], ['id' => '\d+']); //文章详细

Route::rule('article/:type/[:id]', 'index/Article/articleList', 'get', ['ext'=>'html'], ['type' => '\w+', 'id' => '\d+']); //根据条件获取文章列表

Route::post('article/comment', 'index/Article/comment', ['ext' => 'html']); //提交评论


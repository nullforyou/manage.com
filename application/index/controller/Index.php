<?php
namespace app\index\controller;

use think\Db;

class Index extends \app\index\controller\Base{
    
    
    public function __construct(){
	parent::__construct();
    }
    
    public function index(){
	$this->redirect('/article/article.html');
    }
    
    /**
     *ToDo:全站搜索
     */
    public function search(){
	exit('未完善');
    }
    
    /**
     *ToDo:标签列表
     */
    public function flagList(){
	return $this->fetch(config('view_template') . '/index/flag', [
	    'flag' => \model\ModelFacade::model('Flag')->withCount([
		'article' => function($query){
		    $query->where('article_if_pub', 'pass');
		}
	    ])->where('disabled', 0)->select()
	]);
    }
    
    /**
     *ToDo:分类列表
     */
    public function categoryList(){
	return $this->fetch(config('view_template') . '/index/cat', [
	    'cat' => \model\ModelFacade::model('SyscontentCategory')->withCount([
		'article' => function($query){
		    $query->where('article_if_pub', 'pass');
		}
	    ])->where('disabled', 0)->where('category_level', 3)->select()
	]);
    }
}

<?php
namespace app\index\controller;

use think\Db;
use \model\ModelFacade;

class Base extends \think\Controller{
    
	private $rows = 20;
	
    public function __construct(){
        parent::__construct();
		
		//标签数
		$this->view->assign('flagNum', ModelFacade::model('Flag')->where('disabled', 0)->count());
		//分类数
		$this->view->assign('catNum', ModelFacade::model('SyscontentCategory')->where('disabled', 0)->where('category_level', 3)->count());
		//文章数
		$this->view->assign('articleNum', ModelFacade::model('Article')->where('article_if_pub', 'pass')->count());
		
		//最新6条文章
		$this->view->assign('articleList', ModelFacade::model('Article')->where('article_if_pub', 'pass')->order('article_pub_time DESC')->limit(6)->select());
		
        $this->view->assign('navList', $this->getNav());
    }
    
    /**
     *ToDo:获取导航
     *return array
     */
    private function getNav(){
        return Db::name('article_category')->field('category_id,category_name')->where('category_level', 3)->where('disabled', 0)->select();
    }
    
    /**
	 *@param str $modelName 模形$model对象
	 *@param array|str $where 语句条件
	 *@param str $order 排序
	 *@param int $page 查询页数
	 *@param int $listRows 每页条数
	 *@param str $fields 查询字段
	 */
	public function getDataList($model, $where, $order = "id desc", $page = 0, $listRows = 0, $fields = "", $templateName = 'list', $fnName = 'dataList'){
		if (empty($listRows)) {
            $listRows = $this->rows;
        }
		if (empty($page)) {
			$page = empty($_REQUEST['p']) ? 0 : $_REQUEST['p'];
		}
		$return = array();
		$tempObj = $model->where($where)->order($order);
		if ($fields) {
			$tempObj = $model->field($fields);
		}
        $list = $tempObj->where($where)->page($page, $listRows)->select();
        
		$count = $model->where($where)->count();
		if (method_exists($this, "_after_".$fnName)) {
            $fnName = "_after_".$fnName;
			$this->$fnName($list);
		}
        return ['list'=>$list, 'count'=>$count];
	}
    
}

<?php
namespace app\manage\controller;
use think\Session;
use \think\Url;
use \think\Cache;
use \think\Db;

class System extends \think\Controller{
    
    protected $rows = 20;
    
    public function __construct(){
        parent::__construct();
        
        if (false == Session::has('manage', 'management')) {
            //跳转登录
            $this->redirect(Url::build('/manage/login', null, true, DOMAIN));
        }
        
        $manageSession = Session::get('manage', 'management');
        
        $this->_userInfo = $manageSession['admin'];
        
        //记录用户id
        $this->_userId = $this->_userInfo['user_id'];
        
        if (false === hasAuth($this->request->module(), $this->request->controller(), $this->request->action(), $this->request->method())) {
            if ($this->request->isAjax()) {
                $this->error('无操作权限');
            } else {
                die('无操作权限');
            }
        }
    }
    
    /**
     *获取 jqGrid 列表
     */
    public function getjQGridList($param){
        if (!method_exists($this, '_getList')) {
            return json(['code' => 0, 'msg' => '无该查找']);
        }
        return json(call_user_func_array([$this, '_getList'], func_get_args($param)));
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
	
	
	public function _update(){
		if ($this->request->has('guid')) {
			return json(\app\manage\extend\Accessory::instance()->uploadImageMarkdown($this->request->param('guid')));
		} else {
			$fileName = $this->request->post('fileName/s');
			$fileDistrict = $this->request->post('fileDistrict/s');
			return json(\app\manage\extend\Accessory::instance()->uploadImage($fileName, $fileDistrict));
		}
	}
}
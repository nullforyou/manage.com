<?php
namespace app\manage\controller;

use think\Db;

class Atlas extends System{
    
    private $model = null;
    private $categoryModel = null;
    
    public function __construct(){
        parent::__construct();
        $this->model = new \app\manage\model\Article();
        $this->categoryModel = new \app\manage\model\SyscontentCategory();
    }
    
    public function index(){
        $filter = ['article_template'=> array('eq', 'imgs')];
        if ($this->request->has('title')) {
            $filter['article_title'] = array('like',  '%'.$this->request->get('title/s').'%');
        }
        if ($this->request->has('load')) {
            $indexPage = $this->request->get('page/d', 1);
            $data = $this->model->where($filter)->limit(($indexPage - 1) * 20, 20)->select();
            foreach ($data as $val) {
                $val->articleImgsOne;
            }
            return json(['list'=>$data, 'count'=>count($data)]);
        } else {
            return view('syscontent/atlas/index', [
                'request' => $this->request,
                'data' => $this->model->where($filter)->limit(20)->select(),
            ]);
        }
    }
    
    public function modify($id = 0){
        if ($this->request->isAjax()) {
            $this->modifySave();
        } else {
            return $this->modifyEdit($id);
        }
    }
    
    private function modifyEdit($id){
        $data = [
            'catlist' => $this->categoryModel->getTreeCategory($this->categoryModel->getCategoryByWhere())
        ];
        if (!empty($id)) {
            $data['article'] = $this->model->oneResult($id);
        }
        return $this->fetch('syscontent/atlas/modify', $data);
    }
    
    private function modifySave(){
        try {
            $data = [];
            $data['created_id'] = $this->_userId;
            $data['article_title'] = $this->request->post('title/s');
            $data['article_category'] = $this->request->post('category/d');
            $data['article_template'] = 'imgs';
            $data['article_summary'] = $this->request->post('summary/s');
            $data['article_pub_time'] = $this->request->post('pub_time/s');
            if (empty($data['article_pub_time'])) {
                $data['article_pub_time'] = date('Y-m-d');
            }
            $data['article_if_pub'] = $this->request->post('if_pub/s', 'pass');
            $data['extends']['article_img'] = $this->request->post('img/a');
            $data['extends']['article_intro'] = $this->request->post('imgintro/a');
            $data['extends']['article_figure'] = $this->request->post('firstimg/a');
            $id = $this->request->post('id/d');
            if (empty($id)) {
                $this->model->addContent($data);
            } else {
                $this->model->updateContent($data, $id);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功', url('/manage/content/cat/list'));
    }
    
    
}
<?php
namespace app\manage\controller;


class Category extends System{
    
    private $model = null;
    
    public function __construct(){
        parent::__construct();
        $this->model = new \app\manage\model\SyscontentCategory();
    }
    
    public function index(){
        return view('syscontent/category/index', [
            'list' => $this->model->getTreeCategory($this->model->getCategoryByWhere())
        ]);
    }
    
    public function modify($id = 0){
        if ($this->request->isAjax()) {
            $this->modifySave();
        } else {
            return $this->modifyEdit($id);
        }
    }
    
    private function modifyEdit($id){
        if (empty($id)) {
            return $this->fetch('syscontent/category/modify', [
                'list' => $this->model->getTreeCategory($this->model->getCategoryByWhere(['category_level'=>array('lt', 3)]))
            ]);
        } else {
            return $this->fetch('syscontent/category/modify', [
                'list' => $this->model->getTreeCategory($this->model->getCategoryByWhere(['category_id'=>array('NEQ', $id),'category_level'=>array('lt', 3)])),
                'category' => $this->model->get($id)->toArray()
            ]);
        }
        
    }
    
    private function modifySave(){
        try {
            $data = [];
            $data['category_pid'] = $this->request->post('pid/d');
            $data['category_name'] = trim($this->request->post('name/s'));
            $data['disabled'] = $this->request->post('disabled/d');
            $data['category_tag'] = trim($this->request->post('tag/s'));
            $id = $this->request->post('id/d');
            if (empty($id)) {
                $this->model->addCategory($data);
            } else {
                $this->model->updateCategory($data, $id);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功', url('/manage/content/cat/list'));
    }
}
<?php
namespace app\manage\controller;


class Flag extends System{
    
    private $model = null;
    
    public function __construct(){
        parent::__construct();
        $this->model = new \app\manage\model\Flag();
    }
    
    public function index(){
        return view('syscontent/flag/index', [
            'list' => $this->model->getList()
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
            return $this->fetch('syscontent/flag/modify', [
                'flag' => []
            ]);
        } else {
            return $this->fetch('syscontent/flag/modify', [
                'flag' => $this->model->get($id)
            ]);
        }
        
    }
    
    private function modifySave(){
        try {
            $data = [];
            $data['flag_name'] = trim($this->request->post('name/s'));
            $data['disabled'] = trim($this->request->post('disabled/s'));
            $id = $this->request->post('flag_id/d');
            if (empty($id)) {
                $this->model->addFlag($data);
            } else {
                $this->model->updateFlag($data, $id);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功', url('/manage/content/flag/list'));
    }
}
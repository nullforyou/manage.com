<?php
namespace app\manage\controller;

use think\Db;

class Article extends System{
    
    private $model = null;
    private $categoryModel = null;
    private $flagModel = null;
    
    public function __construct(){
        parent::__construct();
        $this->model = new \app\manage\model\Article();
        $this->categoryModel = new \app\manage\model\SyscontentCategory();
        $this->flagModel = new \app\manage\model\Flag();
    }
    
    public function index(){
        $filter = ['article_template' => array('eq', 'word')];
        if ($this->request->has('title')) {
            $filter['article_title'] = array('like',  '%'.$this->request->get('title/s').'%');
        }
        return view('syscontent/article/index', [
            'request' => $this->request,
            'data' => $this->getDataList($this->model, $filter, 'article_id desc'),
            'category' => getValByKey('category_id', Db::name('article_category')->where('category_level', 3)->where('disabled', 0)->field('category_id,category_name')->select(), 'category_name'),
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
        $data = [
            'catlist' => $this->categoryModel->getTreeCategory($this->categoryModel->getCategoryByWhere()),
            'flaglist' => $this->flagModel->getList(true)
        ];
        $data['edittype'] = $this->request->get('edittype', 'html');
        
        if (!empty($id)) {
            $data['article'] = $this->model->oneResult($id);
            $data['edittype'] = $data['article']->articleWord->aritcle_edit_type;
        }
        if ($data['edittype'] == 'html') {
            return $this->fetch('syscontent/article/modify', $data);
        } else {
            if (!hasSettings('markdown_edit')) {
                die('系统未开启Markdown文本编辑器');
            }
            return $this->fetch('syscontent/article/modify_markdown', $data);
        }
    }
    
    private function modifySave(){
        try {
            $data = [];
            $data['created_id'] = $this->_userId;
            $data['article_title'] = $this->request->post('title/s');
            $data['article_category'] = $this->request->post('category/d');
            $data['article_template'] = 'word';
            $data['article_summary'] = $this->request->post('summary/s');
            $data['article_pub_time'] = $this->request->post('pub_time/s');
            
            $data['article_region'] = implode('-', $this->request->post('region/a', []));
            
            $data['article_flag'] = $this->request->post('article_flag/s');
            if (empty($data['article_pub_time'])) {
                $data['article_pub_time'] = date('Y-m-d');
            }
            $data['article_if_pub'] = $this->request->post('if_pub/s', 'pass');
            
            $data['extends']['article_content'] = $this->request->post('content/s');
            
            $data['extends']['aritcle_edit_type'] = $this->request->post('edit_type/s', 'html');
            
            if ($data['extends']['aritcle_edit_type'] == 'markdown') {
                $data['extends']['article_markdown'] = $this->request->post('article_markdown/s');
            }
            
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
    
    public function changeStatus($oper, $id){
        try {
            Db::startTrans();
            if (!in_array($oper, ['down', 'up', 'disabled']) || empty($id)) {
                throw new \Exception('操作对象错误');
            }
            $article = $this->model->get($id);
            if (!$article) {
                throw new \Exception('操作对象错误');
            }
            if ($oper == 'disabled') {
                if ($oper->article_template == 'word') {
                    Db::name('article_word')->where('article_id', $id)->delete();
                } else {
                    Db::name('article_imgs')->where('article_id', $id)->delete();
                }
                $article->delete();
            } elseif ($oper == 'down') {
                $article->article_if_pub = 'wait_auth';
                $article->save();
            } elseif ($oper == 'up') {
                $article->article_if_pub = 'pass';
                $article->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
}
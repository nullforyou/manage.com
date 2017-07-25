<?php
namespace app\manage\controller;

use think\Db;

class Comment extends System{
    
    private $model = null;
    
    public function __construct(){
        parent::__construct();
        $this->model = new \app\manage\model\ArticleComment();
        $this->subModel = new \app\manage\model\ArticleSubComment();
    }
    
    public function index(){
        return view('syscontent/comment/index', [
            'request' => $this->request,
            'data' => $this->getDataList($this->model, ['comment_auth'=>['in', ['pass', 'wait_auth']]], 'comment_id desc'),
        ]);
    }
    
    public function changeStatus($oper, $id){
        try {
            if (!in_array($oper, ['disabled', 'pass']) || empty($id)) {
                throw new \Exception('操作对象错误');
            }
            $comment = $this->model->get($id);
            if (!$comment) {
                throw new \Exception('操作对象错误');
            }
            if ($oper == 'disabled') {
                $comment->comment_auth = 'del';
            } elseif ($oper == 'pass') {
                $comment->comment_auth = 'pass';
            }
            $comment->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
    
    public function subCommentChangeStatus($oper, $id){
        try {
            if (!in_array($oper, ['disabled', 'pass']) || empty($id)) {
                throw new \Exception('操作对象错误');
            }
            $subComment = $this->subModel->get($id);
            if (!$subComment) {
                throw new \Exception('操作对象错误');
            }
            if ($oper == 'disabled') {
                $subComment->comment_auth = 'del';
            } elseif ($oper == 'pass') {
                $subComment->comment_auth = 'pass';
            }
            $subComment->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
    
    
}
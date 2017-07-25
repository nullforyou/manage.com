<?php
namespace app\index\controller;

use think\Db;

class Article extends \app\index\controller\Base{
    
    public function articleList($type, $id = 0){
        $articleList = \model\ModelFacade::model('Article');
        
        if ($type == 'flag') {
            $articleList = $articleList->where('article_flag', $id);
        } elseif ($type == 'cat') {
            $articleList = $articleList->where('article_category', $id);
        } elseif ($type == 'article') {
            $articleList = $articleList;
        }
        
        $articleList = $articleList->where('article_if_pub', 'pass')->order('article_pub_time DESC')->select();
        return $this->fetch(config('view_template').'/article/list', ['list' => $articleList]);
    }
    public function article($id){
        if (!Db::name('article_hits_history')->where('article_id', $id)->where('hit_ip', $this->request->ip())->where('hit_date', date('Y-m-d'))->count()) {
            Db::name('article_hits_history')->insert(['article_id' => $id, 'hit_ip' => $this->request->ip(), 'hit_date' => date('Y-m-d')]);
            Db::name('article_info')->where('article_id', $id)->setInc('article_hits');
        }
        $data['request'] = $this->request;
        $data['article'] = \model\ModelFacade::model('Article')->oneResult($id);
        if ($this->request->has('replytocom')) {
            $data['replytocom'] = \model\ModelFacade::model('ArticleComment')->find($this->request->get('replytocom/s'));
        }
        return $this->fetch(config('view_template').'/article/detail', $data);
    }
    
    /**
     *ToDo:添加评论
     */
    public function comment(){
        try {
            $comment_id = input('post.comment_parent/d');
            if (empty($comment_id)) {
                \model\ModelFacade::model('ArticleComment')->addComment([
                    'comment_content' => input('post.comment/s'),
                    'article_id' => input('post.comment_post_ID/d'),
                    'origin_nickname' => input('post.author/s'),
                    'origin_email' => input('post.email/s'),
                    'origin_url' => input('post.url/s'),
                ], $this->request);
            } else {
                \model\ModelFacade::model('ArticleSubComment')->addComment([
                    'sub_comment_content' => input('post.comment/s'),
                    'article_id' => input('post.comment_post_ID/d'),
                    'origin_nickname' => input('post.author/s'),
                    'origin_email' => input('post.email/s'),
                    'origin_url' => input('post.url/s'),
                    'comment_id' => $comment_id,
                ], $this->request);
            }
            return json(['success' => 1]);
        } catch (\Exception $e) {
            return json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }
}
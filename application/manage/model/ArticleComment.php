<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class ArticleComment extends Model{
    
    protected $table = 'op_article_comment';
    
    protected $autoWriteTimestamp = true;
    
    protected $createTime = 'created_at';
    
    protected $updateTime = false;
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *ToDo:关联子评论
     */
    public function subComment(){
        return $this->hasMany('ArticleSubComment', 'comment_id')->where('comment_auth', 'in', ['pass', 'wait_auth']);
    }
    
    public function addComment($params, $request, $user_id = 0){
        try {
            Db::startTrans();
            $validate = new \think\Validate([
                'comment' => 'require',
                'author' => 'require|max:10',
                'email' => 'require|email|max:20',
                'article_id' => 'require|number|gt:0',
                'url' => function($value, $data){
                    if (empty($value)) return true;
                    if(!filter_var($value, FILTER_VALIDATE_URL)) return '网址不是有效的URL地址';
                    return true;
                }
            ]);
            if (!$validate->check(
                [
                    'comment' => $params['comment_content'],
                    'author' => $params['origin_nickname'],
                    'email' => $params['origin_email'],
                    'article_id' => $params['article_id'],
                    'url' => $params['origin_url']
                ])
            ) {
                throw new \Exception($validate->getError());
            }
            
            $origin_ip = $request->ip();
            $origin_area = getRegionByIp($origin_ip);
        
            $this->save([
                'comment_content' => $params['comment_content'],
                'article_id' => $params['article_id'],
                'origin_ip' => $origin_ip,
                'origin_area' => $origin_area,
                'created_id' => $user_id,
                'origin_nickname' => $params['origin_nickname'],
                'origin_email' => $params['origin_email'],
                'origin_url' => $params['origin_url']
            ]);
            if (!Db::name('article_info')->where('article_id', $params['article_id'])->count()) {
                throw new \Exception('评论对象缺失');
            }
            Db::name('article_info')->where('article_id', $params['article_id'])->setInc('article_comment_num');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
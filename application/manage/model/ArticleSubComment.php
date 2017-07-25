<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class ArticleSubComment extends Model{
    
    protected $table = 'op_article_comment_sub';
    
    protected $autoWriteTimestamp = true;
    
    protected $createTime = 'created_at';
    
    protected $updateTime = false;
    
    protected function initialize(){
        parent::initialize();
    }
    
    public function addComment($params, $request, $user_id = 0){
        try {
            $validate = new \think\Validate([
                'comment|评论内容' => 'require',
                'author|昵称' => 'require|max:10',
                'email|邮箱' => 'require|email|max:20',
                'article_id|文章对象' => 'require|number|gt:0',
                'url|邮箱' => function($value, $data){
                    if (empty($value)) return true;
                    if(!filter_var($value, FILTER_VALIDATE_URL)) return '网址不是有效的URL地址';
                    return true;
                },
                'comment_id|评论对象缺失' => function($value, $data){
                    if (empty($value)) return true;
                    if (intval($value) <= 0) return '评论对象缺失';
                    return true;
                }
            ]);
            if (!$validate->check(
                [
                    'comment' => $params['sub_comment_content'],
                    'author' => $params['origin_nickname'],
                    'email' => $params['origin_email'],
                    'article_id' => $params['article_id'],
                    'url' => $params['origin_url'],
                    'comment_id' => $params['comment_id']
                ])
            ) {
                throw new \Exception($validate->getError());
            }
            
            $origin_ip = $request->ip();
            $origin_area = getRegionByIp($origin_ip);
            $this->save([
                'sub_comment_content' => $params['sub_comment_content'],
                'comment_id' => $params['comment_id'],
                'article_id' => $params['article_id'],
                'origin_nickname' => $params['origin_nickname'],
                'origin_email' => $params['origin_email'],
                'origin_url' => $params['origin_url'],
                'origin_ip' => $origin_ip,
                'origin_area' => $origin_area,
                'created_id' => $user_id,
                
            ]);
            if (!Db::name('article_comment')->where('comment_id', $params['comment_id'])->count()) {
                throw new \Exception('评论对象缺失');
            }
            Db::name('article_comment')->where('comment_id', $params['comment_id'])->setInc('comment_num');
            Db::name('article_info')->where('article_id', $params['article_id'])->setInc('article_comment_num');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
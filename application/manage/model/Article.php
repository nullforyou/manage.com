<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class Article extends Model{
    
    protected $pk = 'article_id';
    
    protected $table = 'op_article_info';
    
    protected $autoWriteTimestamp = true;
    
    protected $createTime = 'created_at';
    
    protected $updateTime = 'updated_at';
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *关联文章
     */
    public function articleWord(){
        return $this->hasOne('ArticleWord', 'article_id');
    }
    
    /**
     *关联文章-内容-图片
     */
    public function articleContentImgs(){
        return $this->hasMany('ArticleContentImgs', 'article_id');
    }
    
    /**
     *关联图集
     */
    public function articleImgs(){
        return $this->hasMany('ArticleImgs', 'article_id');
    }
    
    /**
     *图集头图属性绑定
     */
    public function articleImgsOne(){
        return $this->hasOne('ArticleImgs', 'article_id')->where('article_figure', 1)->bind('article_img');
    }
    
    /**
     *文章评论
     */
    public function comment(){
        return $this->hasMany('ArticleComment', 'article_id')->where('comment_auth', 'in', ['pass', 'wait_auth']);
    }
    /**
     *设置时间修改器
     */
    public function setArticlePubTimeAttr($value){
        if (is_integer($value)) {
            return $value;
        } else {
            return strtotime($value);
        }
    }
    
    /**
     *获取单个对象
     */
    public function oneResult($id){
        $article = $this->get($id);
        if ($article->article_template == 'word') {
            $article->articleWord;
            $article->articleContentImgs;
            $imglist = $article->articleContentImgs;
            $article->articleWord->html = $article->articleWord->article_content;
            if ($imglist) {
                $imglist = getValByKey('origin', $imglist);
                $article->articleWord->html = \app\manage\extend\Accessory::instance()->processContent($article->articleWord->article_content, 'O', $imglist);
            }
        } else {
            $article->articleImgs;
        }
        $article->article_flag = explode(',', $article->article_flag);
        return $article;
    }
    
    
    public function addContent($data){
        try {
            Db::startTrans();
            $dataExtends = $data['extends'];
            unset($data['extends']);
            $data['article_pub_time'] = strtotime($data['article_pub_time']);
            if (false === $this->validate('SyscontentArticle')->allowField(true)->save($data)) {
                throw new \Exception($this->getError());
            }
            if ($data['article_template'] == 'word') {
                if (!in_array($dataExtends['aritcle_edit_type'], ['html', 'markdown'])) {
                    throw new \Exception('文章文本类型不正确');
                }
                
                if (empty($dataExtends['article_content'])) {
                    throw new \Exception('文章内容不能为空');
                } else {
                    foreach (\app\manage\extend\Accessory::instance()->processContent($dataExtends['article_content'], 'I', $this->article_id) as $val) {
                        $insertData[] = [
                            'html' => $val['html'],
                            'origin' => $val['origin'],
                        ];
                    }
                    if ($insertData) {
                        $this->articleContentImgs()->saveAll($insertData);
                    }
                }
                if (false === $this->articleWord()->save($dataExtends)) {
                    throw new \Exception('新增文章错误');
                }
            } elseif ($data['article_template'] == 'imgs') {
                $insertData = [];
                $i = 0;
                $article_figure = 0;
                if (!empty($dataExtends['article_figure'])) {
                    $article_figure = end($dataExtends['article_figure']);
                }
                foreach ($dataExtends['article_img'] as $key => $val) {
                    $insertData[] = [
                        'article_figure' => empty($article_figure) ? ($article_figure == $i ? 1 : 0) : ($article_figure == $key ? 1 : 0),
                        'article_order' => $i,
                        'article_img' => \app\manage\extend\Accessory::instance()->processUploadImg($val, $this->article_id),
                        'article_intro' => $dataExtends['article_intro'][$key]
                    ];
                    $i ++;
                }
                if ($insertData) {
                    $this->articleImgs()->saveAll($insertData);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    
    public function updateContent($data, $id){
        try {
            Db::startTrans();
            $data['article_id'] = $id;
            $dataExtends = $data['extends'];
            unset($data['extends']);
            $data['article_pub_time'] = strtotime($data['article_pub_time']);
            if (false === $this->validate('SyscontentArticle')->allowField(true)->save($data, $id)) {
                throw new \Exception($this->getError());
            }
            if ($this->article_template == 'word') {
                if (!in_array($dataExtends['aritcle_edit_type'], ['html', 'markdown'])) {
                    throw new \Exception('文章文本类型不正确');
                }
                
                if (empty($dataExtends['article_content'])) {
                    throw new \Exception('文章内容不能为空');
                }
                
                foreach (\app\manage\extend\Accessory::instance()->processContent($dataExtends['article_content'], 'I') as $val) {
                    $insertData[] = [
                        'html' => $val['html'],
                        'origin' => $val['origin'],
                    ];
                }
                //删除原来的图片数据
                $this->articleContentImgs()->delete();
                if ($insertData) {
                    //添加现在的图片数据
                    $this->articleContentImgs()->saveAll($insertData);
                }
                $this->articleWord->article_content = $dataExtends['article_content'];
                $this->articleWord->article_markdown = $dataExtends['article_markdown'];
                $this->articleWord->save();
            } else {
                $this->articleImgs()->delete();
                $insertData = [];
                $i = 0;
                $article_figure = 0;
                if (!empty($dataExtends['article_figure'])) {
                    $article_figure = end($dataExtends['article_figure']);
                }
                foreach ($dataExtends['article_img'] as $key => $val) {
                    $temp = [
                        'article_figure' => empty($article_figure) ? ($article_figure == $i ? 1 : 0) : ($article_figure == $key ? 1 : 0),
                        'article_order' => $i,
                        'article_intro' => $dataExtends['article_intro'][$key]
                    ];
                    if (false !== strpos($val, 'additiontodo/upload')) {
                        $temp['article_img'] = $val;
                    } else {
                        $temp['article_img'] = \app\manage\extend\Accessory::instance()->processUploadImg($val, $this->article_id);
                    }
                    $insertData[] = $temp;
                    $i ++;
                }
                if ($insertData) {
                    $this->articleImgs()->saveAll($insertData);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
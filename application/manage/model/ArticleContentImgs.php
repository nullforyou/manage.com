<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class ArticleContentImgs extends Model{
    
    protected $table = 'op_article_content_imgs';
    
    protected function initialize(){
        parent::initialize();
    }
    
}
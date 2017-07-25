<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class ArticleImgs extends Model{
    
    protected $table = 'op_article_imgs';
    
    protected function initialize(){
        parent::initialize();
    }
    
}
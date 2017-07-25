<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class ArticleWord extends Model{
    
    protected $table = 'op_article_word';
    
    protected function initialize(){
        parent::initialize();
    }
    
}
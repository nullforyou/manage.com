<?php
namespace model;

class ModelFacade {
    
    /**
     *ToDo:获取实例对象
     */
    public static function model($modelName){
        static $_model;
        
        if (!$_model[$modelName]) {
            if (class_exists('\\app\\manage\\model\\'.$modelName, true)) {
                $_model[$modelName] = self::getInstance($modelName);
            } else {
                throw new \Exception('模型不存在');
            }
        }
        return $_model[$modelName];
    }
    
    
    /**
     *ToDo:获取某个实例化对象
     *return object
     */
    private static function getInstance($className){
        $instance;
        switch ($className) {
            case 'Flag':
                $instance = new \app\manage\model\Flag;
                break;
            case 'SyscontentCategory':
                $instance = new \app\manage\model\SyscontentCategory;
                break;
            case 'Article':
                $instance = new \app\manage\model\Article;
                break;
            case 'ArticleComment':
                $instance = new \app\manage\model\ArticleComment;
                break;
            case 'ArticleSubComment':
                $instance = new \app\manage\model\ArticleSubComment;
                break;
            default:
                throw new \Exception('模型不存在');
                break;
        }
        return $instance;
    }
}
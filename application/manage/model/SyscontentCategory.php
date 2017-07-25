<?php
namespace app\manage\model;

use think\Model;

class SyscontentCategory extends Model{
    
    protected $pk = 'category_id';
    
    protected $table = 'op_article_category';
    
    protected $autoWriteTimestamp = false;
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *ToDo:¹ØÁªÎÄÕÂ
     */
    public function article(){
        return $this->hasMany('Article', 'article_category');
    }
    
    public function addCategory($data){
        try {
            if ($parent = $this::get($data['category_pid'])) {
                $data['category_level'] = $parent->category_level + 1;
                $data['category_path'] =  $parent->category_path . "," . $data['category_pid'];
            } else {
                $data['category_level'] = 1;
            }
            if (false === $this->validate('SyscontentCategory')->allowField(true)->save($data)) {
                throw new \Exception($this->getError());
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    public function updateCategory($data, $id){
        try {
            if ($parent = $this::get($data['category_pid'])) {
                $data['category_level'] = $parent->category_level + 1;
                $data['category_path'] =  $parent->category_path . "," . $data['category_pid'];
            } else {
                $data['category_level'] = 1;
                $data['category_path'] =  ',';
            }
            if (false === $result = $this->validate('SyscontentCategory')->allowField(true)->save(array_merge($data, ['category_id'=>$id]), ['category_id'=>$id])) {
                throw new \Exception($this->getError());
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
    }
    
    public function getTreeCategory($data){
        if (empty($data)) {
            return [];
        }
        $tree = new \tree\Tree($data, ['category_id', 'category_pid']);
        return $tree->leaf();
    }
    
    public function getCategoryByWhere($filter = []){
        return \think\Db::name('article_category')->where($filter)->select();
    }
}
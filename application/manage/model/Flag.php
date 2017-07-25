<?php
namespace app\manage\model;

use think\Model;

class Flag extends Model{
    
    protected $pk = 'flag_id';
    
    protected $table = 'op_article_flag';
    
    protected $autoWriteTimestamp = false;
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *ToDo:¹ØÁªÎÄÕÂ
     */
    public function article(){
        return $this->hasMany('Article', 'article_flag');
    }
    
    public function addFlag($data){
        try {
            if (false === $this->validate('Flag')->allowField(true)->save($data)) {
                throw new \Exception($this->getError());
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    public function updateFlag($data, $id){
        try {
            if (false === $result = $this->validate('Flag')->allowField(true)->save(array_merge($data, ['flag_id'=>$id]), ['flag_id'=>$id])) {
                throw new \Exception($this->getError());
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
    }
    
    public function getList($disabled = false){
        if (false === $disabled) {
            return $this->all();
        } else {
            return $this->where('disabled', 0)->select();
        }
    }
}
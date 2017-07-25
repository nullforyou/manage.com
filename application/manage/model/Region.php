<?php
namespace app\manage\model;
use think\Model;
use think\Db;

class Region extends Model{
    
    protected $pk = 'id';
    
    protected $table = 'op_region';
    
    protected $autoWriteTimestamp = true;
    
    protected function initialize(){
        parent::initialize();
    }
    
    public function setJsonFile(){
        $file = config('region_json_file');
        file_put_contents($file, json_encode($this->getTree()));
    }
    
    private function getTree(){
        $list = $this->getAll();
        $tree = new \tree\Tree($list, ['id', 'parent_id']);
        return $tree->leaf();
    }
    
    private function getAll(){
        return collection(self::all())->toArray();
        
    }
}
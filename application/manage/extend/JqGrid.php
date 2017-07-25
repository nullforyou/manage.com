<?php
namespace app\manage\extend;

use think\Request;
use think\Db;

class JqGrid {
    
    private $rows = 20;
    
    private $page = 1;
    
    private $sidx = 'id';
    
    private $sord = 'desc';
    
    private $indexPage;
    
    private $filters = [];
    
    private $model;
    
    private $vagueField = [];
    
    private $filtField = [];
    
    
    private static $_instance;
    
    public static function instance($option = []){
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }
    
    private function __construct($option = []){
        $this->setOption($option);
        $this->getFilters(Request::instance());
        $this->rows = $rows = Request::instance()->has('rows') ? Request::instance()->get('rows/d') : $this->rows;
        $this->page = $page = Request::instance()->has('page') ? Request::instance()->get('page/d') : $this->page;
        $this->sidx = $sidx = Request::instance()->has('sidx') ? Request::instance()->get('sidx') : $this->sidx;
        $this->sord = $sord = Request::instance()->has('sord') ? Request::instance()->get('sord') : $this->sord;
        
        if ($rows <= 0) $rows = $this->rows;
        if ($page - 1 < 0) {
            $this->indexPage = 0;
        } else {
            $this->indexPage = ($page - 1) * $rows;
        }
    }
    
    public function __set($name, $value){
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
    
    private function setOption($option = []){
        foreach ($option as $key => $val) {
            $this->__set($key, $val);
        }
    }
    
    private function getFilters($request){
        if (!($request->has('_search') and $request->has('filters'))) {
            return true;
        }
        $filters = json_decode($request->get('filters'), true);
        if (empty($filters)) {
            return true;
        }
        foreach ($filters['rules'] as $key => $val) {
            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $val['data'])) {
                $filters['rules'][$key]['data'] = strtotime($val['data']);
            }
            switch ($val['op']) {
                case "eq"://等于
                    $filters['rules'][$key]['op'] = 'EQ';
                    break;
                case "ne"://不等
                    $filters['rules'][$key]['op'] = 'NEQ';
                    break;
                case "bw"://开始于
                    $filters['rules'][$key]['op'] = 'EGT'; //大于等于
                    break;
                case "ew"://开始于
                    $filters['rules'][$key]['op'] = 'ELT'; //小于等于
                    break;
                case "cn"://包含
                case "in"://包含
                    $filters['rules'][$key]['op'] = 'IN'; //IN 查询
                    break;
                case "nc"://不包含
                case "ni"://不包含
                    $filters['rules'][$key]['op'] = 'NOT IN'; //IN 查询
                    break;
                case "nu"://空值
                    $filters['rules'][$key]['op'] = 'NULL';
                    break;
                case "nn"://非空值
                    $filters['rules'][$key]['op'] = 'NOT NULL';
                    break;
                case "bn"://不开始于
                    $filters['rules'][$key]['op'] = 'LT'; //小于
                    break;
                case "en"://不结束于
                    $filters['rules'][$key]['op'] = 'GT'; //大于
                    break;
            }
        }
        $this->filters = array_merge($filters, $this->filters);
    }
    
    public function query($appendFilter = []){
        $indexPage = $this->indexPage;
        $rows = $this->rows;
        $sidx = $this->sidx;
        $sord = $this->sord;
        $filters = $this->filters;
        $model = $this->model;
        $vagueField = $this->vagueField;
        $list = $model::all(function($query) use ($indexPage, $rows, $sidx, $sord, $filters, $vagueField, $appendFilter) {
            $result = $query;
            foreach ($appendFilter as $key => $val) {
                $temp = explode('|', $key);
                if (count($temp) > 1) {
                    $result->where($temp[0], $temp[1], $val);
                } else {
                    $result->where($temp[0], $val);
                }
                
            }
            if (!empty($filters['rules'])) {
                $result = $result->where(function ($query) use ($filters, $vagueField){
                    if (strtolower($filters['groupOp']) == 'or') {
                        foreach ($filters['rules'] as $val) {
                            if (in_array($val['field'], $vagueField)) {
                                $query = $query->whereOr($val['field'], 'like', "{$val['data']}%");
                            } else {
                                $query = $query->whereOr($val['field'], $val['op'], $val['data']);
                            }
                        }
                    } else {
                        foreach ($filters['rules'] as $val) {
                            if (in_array($val['field'], $vagueField)) {
                                $query = $query->where($val['field'], 'like', "{$val['data']}%");
                            } else {
                                $query = $query->where($val['field'], $val['op'], $val['data']);
                            }
                        }
                    }
                });
            }
            $result->limit($indexPage, $rows)->order($sidx, $sord);
        });
        $countSql = preg_replace_callback('/.*(\*).*(ORDER.*)/', function($matches){return str_replace([$matches[1],$matches[2]], ['count(*) as total', ''], $matches[0]);}, $model->getlastsql());
        $pageCount = 0;
        if ($list) {
            $count = Db::query($countSql);
            if ($count[0]['total']) {
                $pageCount = ceil($count[0]['total'] / $rows);
            }
        }
        $listData = [];
        
        
        foreach ($list as $val) {
            $val = $val->toArray();
            array_walk($val, function(&$v, $k, $filtField){
                if (in_array($k, $filtField)) {
                    $v = '';
                }
                if (!empty($k) && is_numeric($v) && '_itme' === substr($k, -5)) {
                    $v = date('Y-m-d H:i:s', $v);
                }
            }, $this->filtField);
            $listData[] = $val;
        }
        return ['rows'=>$listData, 'page'=>$this->page, 'total'=>$pageCount];
    }
    
    
    
    
    
}
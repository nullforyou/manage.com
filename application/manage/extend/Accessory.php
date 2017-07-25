<?php
/**
 *扩展之附件管理
 */
namespace app\manage\extend;

class Accessory{
    
    private $dir;
	
    private static $_instance;
    
    public static function instance(){
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
	
	private function __construct(){
        $this->dir = ROOT_PATH . 'writable' . DS . 'upload';
    }
	
	public function getdir($dir = ''){
		if (empty($dir)) {
			return $this->getfiles($this->dir);
		} else {
			return $this->getfiles($this->get_disk_dir($dir));
		}
	}
	
	private function get_disk_dir($dir){
		return str_replace('http://' . DOMAIN . DS . 'additiontodo', ROOT_PATH . 'writable', $dir);
	}
	
	private function get_web_dir($dir){
		return str_replace(ROOT_PATH . 'writable', 'http://' . DOMAIN . DS . 'additiontodo', $dir);
	}
	
	private function getfiles($dir){
		$list = [];
		if (is_dir($dir)) {
			foreach (scandir($dir) as $val) {
				if ($val == '.' || $val == '..') {
					continue;
				}
				if (is_dir($dir . DS . $val)) {
					$list['folder'][$val] = encode($this->get_web_dir($dir . DS . $val));
				} else {
					$web = $this->get_web_dir($dir . DS . $val);
					switch (pathinfo($web, PATHINFO_FILENAME)) {
						case 'image':
							$web = str_replace(DS.'image', '', $web);
							break;
						case '800x600':
							$web = str_replace(DS.'800x600', '', $web) . '@800x600';
							break;
						case '400x300':
							$web = str_replace(DS.'400x300', '', $web) . '@400x300';
							break;
						case '200x150':
							$web = str_replace(DS.'200x150', '', $web) . '@200x150';
							break;
						default:
							$web = str_replace(DS.'origin', '', $web) . '@origin';
							break;
					}
					$list['file'][] = [
						'dir' => $web,
						'stat' => stat($dir . DS . $val),
						'pathinfo' => pathinfo($dir . DS . $val)
					];
				}
			}
		}
		return $list;
	}
	
    
    public function uploadImage($fileName, $fileDistrict){
		// 获取表单上传文件
		$file = request()->file($fileName);
		if (empty($file)) {
			$data['code'] = 0;
			$data['msg'] = '附件过大,上传附件不能超过'.ini_get('upload_max_filesize');
			return $data;
		}
		// 移动到框架应用根目录/writable/temp/ 目录下
		$info = $file->validate(['size'=>1024 * 1024 * 2,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'writable' . DS . 'temp' . DS . $fileDistrict);
		
		if($info){
			$data['urlimg'] = 'http://' . DOMAIN . DS . 'additiontodo' . DS . 'temp' . DS . $fileDistrict . DS . $info->getSaveName();
			$data['code'] = 1;
			
		}else{
			$data['code'] = 0;
			$data['msg'] = $file->getError();
		}
        return $data;
    }
	
	/**
	 *markdown上传图片
	 */
	public function uploadImageMarkdown($guid){
		$file = request()->file('editormd-image-file');
		if (empty($file)) {
			$data['success'] = 0;
			$data['message'] = '附件过大,上传附件不能超过'.ini_get('upload_max_filesize');
			return $data;
		}
		// 移动到框架应用根目录/writable/temp/ 目录下
		$info = $file->validate(['size'=>1024 * 1024 * 2,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'writable' . DS . 'temp' . DS . $guid);
		if($info){
			$data['url'] = 'http://' . DOMAIN . DS . 'additiontodo' . DS . 'temp' . DS . $guid . DS . $info->getSaveName();
			$data['success'] = 1;
		}else{
			$data['success'] = 0;
			$data['message'] = $file->getError();
		}
        return $data;
	}
	
	/**
	 *处理上传的图片
	 */
	public function processUploadImg($file, $origin = 'no_origin'){
		$saveFile = str_replace('http://'.DOMAIN.'/additiontodo', ROOT_PATH . 'writable', $file);
		//检查文件是否存在
		if (!file_exists($saveFile)) {
			throw new \Exception('文件不存在');
			return false;
		}
		$filepath = pathinfo($saveFile);
		//正式目录地址
		$newSavePath = str_replace('/temp/', '/upload/', $filepath['dirname']) . DS .  $origin . DS . $filepath['filename'];
		
		@mkdir($newSavePath, 0777, true);
		
		//移动图片文件
		$newSaveFile = $newSavePath . DS . 'origin.' . $filepath['extension'];
		copy($saveFile, $newSaveFile);
		
		//打开图片文件
		$image = \think\Image::open($newSaveFile);
		
		//图片处理
		foreach (['800x600', '400x300', '200x150'] as $val) {
			$size = explode('x', $val);
			//缩略图
			$thumbFile = $newSavePath . DS . $val . '.' . $filepath['extension'];
			$this->setThumb($size[0], $size[1], $image, $thumbFile);
			//生成水印
			$this->setWater($thumbFile, ROOT_PATH . 'public' . DS . 'watermark.png');
		}
		//给原图右下角添加水印并保存
		$image = \think\Image::open($newSaveFile);
		$image->water(ROOT_PATH . 'public' . DS . 'watermark.png')->save($newSavePath . DS . 'image.' . $filepath['extension']);
		
		$returnFile = str_replace(ROOT_PATH . 'writable', 'http://'.DOMAIN.'/additiontodo', $newSaveFile);
		return pathinfo($returnFile, PATHINFO_DIRNAME) . '.' . $filepath['extension'];
	}
	
	
	/**
	 *生成缩略图
	 */
	private function setThumb($width, $height, $file, $newFile){
		if (!is_object($file)) {
			$file = \think\Image::open($file);
		}
		$file->thumb($width, $height)->save($newFile);
	}
	/**
	 *加水印
	 */
	private function setWater($file, $water){
		$file = \think\Image::open($file)->water($water)->save($file);
	}
	
	/**
	 *对包含图片的内容进行出入库加工
	 *@param $content 内容
	 *@param $type 出入库 I/O
	 */
	public function processContent(string &$content, string $type){
		if ($type === 'I') {
			$object = clone $this;
			$object->imgList = [];
			$origin = func_num_args() > 2 ? func_get_arg(2) : false;
			$content = preg_replace_callback('/<img(.*?)src=\"(.*?)"(.*?)>/', function($matches) use ($object, $origin){
				if ($matches[2]) {
					if (false !== strpos($matches[2], 'additiontodo/temp')) {
						$matches['web'] = $object->processUploadImg($matches[2], $origin);
						$matches['html'] = str_replace($matches[2], $matches['web'], $matches[0]);
						$matches['origin'] = "[__img:".count($object->imgList)."__]";
						$object->imgList[count($object->imgList)] = $matches;
					} else {
						$matches['web'] = $matches[2];
						$matches['html'] = str_replace($matches[2], $matches['web'], $matches[0]);
						$matches['origin'] = "[__img:".count($object->imgList)."__]";
						$object->imgList[count($object->imgList)] = $matches;
					}
					return $matches['origin'];
				}
			}, $content);
			return $object->imgList;
		}
		if ($type === "O") {
			if (func_num_args() > 2) {
				$imglist = func_get_arg(2);
				return preg_replace_callback('/\[__img:[\d+]__\]/', function($matches) use ($imglist){
                    return $imglist[$matches[0]]['html'];
                }, $content);
			}
		}
	}
}
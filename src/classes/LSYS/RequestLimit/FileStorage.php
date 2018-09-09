<?php
/**
 * 文件存储限制请求实现
 */
namespace LSYS\RequestLimit;
class FileStorage implements Storage{
	protected $_dir;
	/**
	 */
	public function __construct($dir=null){
		if($dir==null)$dir=sys_get_temp_dir();
		$this->_dir=rtrim($dir,"\\/").DIRECTORY_SEPARATOR;
	}
	protected function _file($key){
		$file=md5($key);
		$dir=substr($file, 0,6);
		if(!is_dir($this->_dir.$dir))mkdir($this->_dir.$dir);
		return $this->_dir.$dir.DIRECTORY_SEPARATOR.$file.".rqcache";
	}
	public function add($key,$time){
		$file=$this->_file($key);
		is_file($file)?$f=fopen($file, "r+"):$f=fopen($file, "a+");
		$ntime=time();
		$len=filesize($file);
		if($len>0){
			$data=fread($f,$len);
		}else $data=null;
		if (!empty($data)){
			list($_time,$num)=explode('|', $data);
			if ($_time>$ntime) $data=$_time."|".($num+1);
			else unset($data);
		}
		if (!isset($data))$data=($ntime+$time)."|1";
		ftruncate($f,0);
		rewind($f);
		fwrite($f,$data);
		fclose($f);
		return true;
	}
	public function get($key){
		$file=$this->_file($key);
		if (!is_file($file)) return 0;
		$data=file_get_contents($file);
		if (empty($data))return 0;
		list($t,$n)=explode("|", $data);
		if ($t<=time()) return 0;
		return $n;
	}
	public function ttl($key){
		$file=$this->_file($key);
		if (!is_file($file)) return 0;
		$data=file_get_contents($file);
		if (empty($data))return 0;
		list($t,$n)=explode("|", $data);
		$t=$t-time();
		return $t>0?$t:0;
	}
}
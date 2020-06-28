<?php
/**
 * memcache存储限制请求实现
 */
namespace LSYS\RequestLimit;
class MemcacheStorage implements Storage{
	protected $_mem;
	public function __construct(\LSYS\Memcache $memcache=null){
	    $this->_mem=$memcache?$memcache:\LSYS\Memcache\DI::get()->Memcache();
	}
	public function add(string $key,int $time):bool{
	    $this->_mem->configServers();
		$time_key=$key."_time";
		if($this->_mem->get($time_key)==NULL){
			if ($time>=2592000)$timeout=time()+$time;
			else if ($time<=0)$timeout=0;
			else $timeout = $time;
			$this->_mem->set($key,0,$timeout);
		}
		if ($this->_mem->increment($key)==1){
			if ($time>=2592000)$timeout=time()+$time;
			else if ($time<=0)$timeout=0;
			else $timeout = $time;
			$this->_mem->set($time_key,time()+$time,$timeout);
		}
		return true;
	}
	public function get(string $key):int{
	    $this->_mem->configServers();
		return (int)$this->_mem->get($key);
	}
	public function ttl(string $key):int{
	    $this->_mem->configServers();
	    $save_time=$this->_mem->get($key."_time");
		if ($save_time==null||$save_time-time()<=0) return 0;
		return $save_time-time();
	}
}
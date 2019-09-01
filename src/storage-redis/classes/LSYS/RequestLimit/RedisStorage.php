<?php
/**
 * redis存储限制请求实现
 */
namespace LSYS\RequestLimit;
class RedisStorage implements Storage{
	/**
	 * @var \LSYS\Redis
	 */
	protected $_redis;
	public function __construct(\LSYS\Redis $redis=null){
	    $this->_redis=$redis?$redis:\LSYS\Redis\DI::get()->redis();
	}
	public function add($key,$time){
	    $this->_redis->configConnect();
		if ($this->_redis->incr($key)==1){
			$this->_redis->expire($key,$time);
		}
		return true;
	}
	public function get($key){
	    $this->_redis->configConnect();
		return $this->_redis->get($key);
	}
	public function ttl($key){
	    $this->_redis->configConnect();
		return $this->_redis->ttl($key);
	}
}
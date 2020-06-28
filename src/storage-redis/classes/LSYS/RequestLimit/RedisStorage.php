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
	public function add(string $key,int $time):bool{
	    $this->_redis->configConnect();
		if ($this->_redis->incr($key)==1){
			$this->_redis->expire($key,$time);
		}
		return true;
	}
	public function get(string $key):int{
	    $this->_redis->configConnect();
		return (int)$this->_redis->get($key);
	}
	public function ttl(string $key):int{
	    $this->_redis->configConnect();
		return (int)$this->_redis->ttl($key);
	}
}
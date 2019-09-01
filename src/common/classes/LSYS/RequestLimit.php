<?php
namespace LSYS;
use LSYS\RequestLimit\Storage;
class RequestLimit{
	/**
	 * 当前状态为需要验证码
	 * @var integer
	 */
	const IS_CAPTCHA=2;
	/**
	 * 已通过请求限制校验
	 * @var integer
	 */
	const IS_PASS=1;
	/**
	 * 当前请求已屏蔽
	 * @var integer
	 */
	const IS_BLOCK=0;
	//默认规则
	//0 表示不限制
	public static $rule=array(
		60=>array(2,4),
		3600=>array(15,60),
		86400=>array(1440,2880),
	);
	//token
	protected $_token;
	//client ip
	protected $_ip;
	//rule
	protected $_rule=array();
	/**
	 * @var Captcha
	 */
	protected $_captcha;
	/**
	 * @var Storage
	 */
	protected $_storage;
	/**
	 * 请求限制
	 * @param string $token
	 * @param Storage $storage
	 * @param Captcha $captcha
	 */
	public function __construct($token,Storage $storage,Captcha $captcha=null){
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$this->_ip=array_shift($client_ips);
		}
		elseif (isset($_SERVER['HTTP_X_REAL_IP'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_X_REAL_IP']);
			$this->_ip=array_shift($client_ips);
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
			$this->_ip=array_shift($client_ips);
		}else if (isset($_SERVER['REMOTE_ADDR'])){
			$this->_ip=$_SERVER['REMOTE_ADDR'];
		}else{
			$this->_ip='127.0.0.1';
		}
		$this->_rule=self::$rule;
		$this->_token=$token;
		$this->_storage=$storage;
		$this->_captcha=$captcha;
	}
	/**
	 * 清除限制
	 * @return \LSYS\RequestLimit
	 */
	public function clearLimit(){
		$this->_rule=array();
		return $this;
	}
	/**
	 * 自定义限制
	 * 0 表示不限制
	 * 没设置验证码对象而需要验证码时将返回false
	 * @param int $time
	 * @param int $block_num
	 * @param number $captcha_num
	 * @return \LSYS\RequestLimit
	 */
	public function setLimit($time,$captcha_num,$block_num=0){
		$this->_rule[$time]=array($captcha_num,$block_num);
		return $this;
	}
	/**
	 * 客户端IP
	 * @param string $ip
	 * @return \LSYS\RequestLimit
	 */
	public function setIp($ip){
		$this->_ip=$ip;
		return $this;
	}
	/**
	 * 当前被屏蔽时,开放屏蔽时间
	 * @return number
	 */
	public function nextTime(){
		$rules=$this->_rule;
		krsort($rules);
		foreach ($rules as $k=>$rule){
			$num=$this->_storage->get($this->_key(self::IS_BLOCK, $k));//屏蔽
			list($c,$b)=$rule;
			if ($b>0&&$b>=$num){
				return $this->_storage->ttl($this->_key(self::IS_BLOCK, $k));
			}
		}
		return 0;
	}
	protected function _key($is,$k){
		return $is.$k.$this->_ip.$this->_token;
	}
	/**
	 * 获取当前TOKEN的IS状态
	 * @return string
	 */
	public function is(){
		$rules=$this->_rule;
		krsort($rules);
		foreach ($rules as $k=>$rule){
			$num=$this->_storage->get($this->_key(self::IS_BLOCK, $k));//屏蔽
			list($c,$b)=$rule;
			if ($b>0&&$num>=$b){
				return self::IS_BLOCK;
			}
		}
		foreach ($rules as $k=>$rule){
			$num=$this->_storage->get($this->_key(self::IS_CAPTCHA, $k));//验证码
			list($c,$b)=$rule;
			if ($c>0&&$num>=$c){
				return self::IS_CAPTCHA;
			}
		}
		return self::IS_PASS;
	}
	protected function _set(){
		foreach ($this->_rule as $k=>$rule){
			if ($k<=0)continue;
			$this->_storage->add($this->_key(self::IS_BLOCK, $k),$k);//
			$this->_storage->add($this->_key(self::IS_CAPTCHA, $k),$k);//
		}
	}
	/**
	 * 进行请求校验
	 * @param string $code
	 * @return boolean
	 */
	public function run($code=null){
		$is=$this->is();
		if ($is==self::IS_PASS){
			$this->_set();
			return true;
		}
		if ($this->_captcha&&$is==self::IS_CAPTCHA&&$this->_captcha->valid($code)){
			$this->_set();
			return true;
		}
		return false;
	}
	/**
	 * 获取验证码对象
	 * @return \LSYS\Captcha|NULL
	 */
	public function getCaptcha(){
		return $this->_captcha;
	}
}
<?php
namespace LSYS\RequestLimit;
interface Storage{
	/**
	 * 存储限制数据增加
	 * @param string $key
	 * @param number $time
	 */
    public function add(string $key,int $time):bool;
	/**
	 * 获取已请求数量
	 * @param string $key
	 */
	public function get(string $key):int;
	/**
	 * 获取请求限制数据存活时间
	 * @param number $key
	 */
	public function ttl(string $key):int;
}
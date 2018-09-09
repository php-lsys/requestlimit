# 基于IP的可定制的请求拦截或限制访问
1. 可定制多个规则,实现同一IP请求多少次出现验证码或屏蔽请求
2. 统一的验证码接口,可以接入你的想要的任何验证码类库或自行实现验证码
3. 可以用在如注册,手机验证码获取等页面,防止机器人请求


> 默认数据存放文件,还可以使用以下依赖包:
	
	"lsys/requestlimit-storage-memcache":"~1.0.0",
	"lsys/requestlimit-storage-redis":"~1.0.0",
	"lsys/requestlimit-storage-memcached":"~1.0.0"

```
<?php
use LSYS\RequestLimit;
require_once  __DIR__."/Bootstarp.php";
//默认使用图片验证码
//以下是 Geetest验证码
//使用转下格式,或在前端转

if (isset($_GET['geetest_challenge'])&&isset($_GET['geetest_validate'])&&isset($_GET['geetest_seccode'])&&!isset($_GET['code']))

	$_GET['code']=implode(",",array(
			$_GET['geetest_challenge'],
			$_GET['geetest_validate'],
			$_GET['geetest_seccode']
	));


//标识,自定义字符串
$key="login:13510461170";
//默认数据存储在文件中,在系统临时目录
$storage=new FileStorage();
//数据使用memcache存储
//LSYS\RequestLimit\RedisStorage
//数据使用redis存储
//LSYS\RequestLimit\MemcacheStorage
//使用Geetest验证吗
$captcha=new LSYS\Captcha\Geetest($key);
$vc=new RequestLimit($key,$storage,$captcha);

//默认规则参见:RequestLimit::$rule 
//清除已设置规则
//$vc->clear_limit();
//设置规则
//$vc->set_limit(60/*时间,单位秒*/,100/*指超过100次请求屏蔽*/,50/*超过50次请求需要输入验证码*/);
//$vc->set_limit(600,1000,500);


$status=$vc->is();//得到当前请求的状态
//RequestLimit::IS_CAPTCHA 表示需要验证码
//RequestLimit::IS_BLOCK 表示请求超过限制
//其他表示通过
if($status==RequestLimit::IS_CAPTCHA&&!isset($_GET['code'])){
	//提示需要输入验证码

	if ($vc->get_captcha() instanceof LSYS\Captcha\Geetest) {

		$res=$vc->get_captcha()->get_result();
		include "geetest/show.php";
	}else include "captcha/show.php";
	die();
}else if ($status==RequestLimit::IS_BLOCK){
        $time=$vc->next_time();//得到重新开放访问时间
	die("屏蔽,请在{$time}秒后尝试访问");
}
if (!$vc->run(@$_GET['code'],$status)){//请求校验,通过放回true
	die('验证码错误');
}
die("通过");
?>

```
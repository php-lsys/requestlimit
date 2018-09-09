<?php
/**
 * lsys lcaptcha
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	"geetest"=>array(
		'pc'=>array(
			'captcha_id'=>'b46d1900d0a894591916ea94ea91bd2c',
			'private_key'=>'36fc3fe98530eea08dfc6ce76e3d24c4',
		),
		'mobile'=>array(
			'captcha_id'=>'7c25da6fe21944cfe507d2f9876775a9',
			'private_key'=>'f5883f4ee3bd4fa8caec67941de1b903',
		)
	),
	"lcs"=>array(
			'urls'=>array(
					'service_url'=>'http://192.168.1.101:801/captcha/server/check',
					'client_url'=>'http://192.168.1.101:801/captcha/client/show',
			),
			'apps'=>array(
					'dome'=>array(
							'appid'=>'test',
							'appkey'=>'36fc3fe98530eea08dfc6ce76e3d24c4',
					)
			),
	),
);
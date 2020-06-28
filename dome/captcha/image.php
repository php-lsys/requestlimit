<?php
//显示图片
use LSYS\Captcha\Image;
require_once  __DIR__."/../Bootstarp.php";
if (isset($_GET['token']))$token=$_GET['token'];
else $token=NULL;
$iamge=new Image($token);
$iamge->render(100, 40,isset($_GET['r']));
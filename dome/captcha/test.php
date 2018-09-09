<?php
use LSYS\Captcha\Image;
require_once  __DIR__."/../Bootstarp.php";
if (isset($_GET['token']))$token=$_GET['token'];
else $token=NULL;
$iamge=new Image($token);
echo $iamge->test(@$_GET['code']);
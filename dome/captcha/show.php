<?php
//显示页面
$t = $vc->get_captcha();
$res=$t->get_result();
?>
<script src="http://code.jquery.com/jquery-1.12.3.min.js"></script>
<form action="check.php" method="GET">
<img src="./captcha/image.php?token=<?php echo $res?>" onclick="this.src=this.src.indexOf('r=')==-1?(this.src+'&r='):(this.src.replace(/r=.*$/,'r='+Math.random()))"/>
<input type="text"name="code"><span class="show_status"></span>
<input type="submit"/>
</form>
<script>
	$('input[name=code]').blur(function(){
		var val=this.value;
		$.ajax({
			url:'./captcha/test.php',
			data:{code:val,token:'<?php echo $res?>'},
			success:function(data){
				if(data==1)$('.show_status').html('ok');
				else $('.show_status').html('bad');
			}
		})
	})
</script>
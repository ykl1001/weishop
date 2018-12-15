<!DOCTYPE html>
<!--[if IE 6]><html lang="zh-CN" class="ie6 ie9- ie8-"><![endif]-->
<!--[if IE 7]><html lang="zh-CN" class="ie7 ie9- ie8-"><![endif]-->
<!--[if IE 8]><html lang="zh-CN" class="ie8 ie9-"><![endif]-->
<!--[if IE 9]><html lang="zh-CN" class="ie9"><![endif]-->
<!--[if (gt IE 8)|!(IE)]><!-->
<html lang="zh-CN">
<!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<title>登录</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome-ie7.css') }}"><![endif]-->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/base.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/ldzc.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/sm-ht.css') }}">
	<script src="{{ asset('js/html5.js') }}"></script>
	<script src="{{ asset('js/jquery.1.9.1.js') }}"></script>	
	<script src="{{ asset('js/sm-ht.js') }}"></script>	
	@section('headCss')
	@show{{-- head区域css样式表 --}}
	@section('headJs')
	@show{{-- head区域javscript脚本 --}}
	@section('beforeStyle')
	@show{{-- 在内联样式之前填充一些东西 --}}
	@section('head_style')
	@show{{-- head区域内联css样式表 --}}
	@section('afterStyle')
	@show{{-- 在内联样式之后填充一些东西 --}}
	<style type="text/css">
		body{background: #fff;}
	</style>
</head>
<body>
	<div class="all">	
		<!-- dlbd -->
		<div class="g-dlbd">
			<div class="w1040 ma clearfix">
				<div class="m-lfct fl">
					<a href=""><img src="{{ asset('images/dlzim.png') }}" alt=""></a>
				</div>
				<div class="m-rgct fr">
					<p class="tc"><img src="{{ asset('images/dltt3.png') }}" alt=""></p>
					<div class="m-dlboxct">
						<form action="javascript:void(0)" method="" onsubmit="return logincheck">
							<fieldset>
								<legend>登录表单</legend>
								<div class="u-dliptitem clearfix mb20">
									<label class="f-ipttt fl"><i class="fa i-user"></i></label>
									<input id="name" type="text" placeholder=" 请输入手机号">
								</div>
								<div class="u-dliptitem clearfix">
									<label class="f-ipttt fl"><i class="fa i-pwd"></i></label>
									<input id="pwd" type="password" placeholder=" 请输入密码">
								</div>
								<p class="clearfix m-iptt">
									<!-- <span class="fl"><input type="checkbox"><label for="" class="ml5">自动登录</label></span> -->
									<a href="{{ u('Public/forgetpwd') }}" class="fl">忘记密码?</a>
									<a href="{{ u('Public/register') }}" style="color:#0b5382;" class="fr">免费注册</a>
								</p>
								<input type="submit" class="btn f-dlbtn login cur" onclick="logincheck()" value="立即登录">
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
function logincheck() {
	var name = document.getElementById("name").value;
	var pwd = document.getElementById("pwd").value;
	if(name == ""){
		alert("请输入手机号");
		$("#name").focus();
		return false;
	}/* else{
		var name = document.getElementById("name").value;
		var reg = /^1[\d+]{10}$/;
		if(!reg.test(name)){
			alert('请输入正确的手机号码');
			$("#name").focus();
			return false;
		}
	} */
	if(pwd == ""){
		alert("请输入密码");
		$("#pwd").focus();
		return false;
	}

	var obj = new Object();
	obj.mobile = name;
	obj.pwd = pwd;
	$.post("{{ u('Public/dologin') }}",obj,function(result){
		if(result['status'] == true ){
			if (result['data']['type'] == 3) {
				window.location = result['url'];
			} else {
				window.location="{{ u('Index/index') }}";
			}
		} else {
			alert(result.msg);
		}
	},'json');
}
</script>
@include('seller._layouts.alert')
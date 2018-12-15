@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
      <a class="button button-link button-nav pull-left" href="javascript:$.href('{{ u('UserCenter/index') }}')" data-transition='slide-out' data-no-cache="true" external>
        <span class="icon iconfont">&#xe600;</span>返回
      </a>
      <a class="button button-link button-nav pull-right" data-popup=".popup-about" href="{{ u('User/reg') }}" external>注册</a>
      <h1 class="title f16">登录</h1>
    </header>
@stop

@section('css')
<style type="text/css">
    input#vcode_input{height: 40px;width: 100px;border: 1px solid #ced6dc;}
    .x-tkfont{padding: 2em 10px;font-size: .875em;color: #999;max-height: 260px;overflow: auto;}
</style>
@stop

@section('content')
    @if($quicklogin == 2)
        <div class="content" id='1'>
            <div class="buttons-tab">
                <a href="#" class="tab-link button active" external>普通登录</a>
                <a href="{{ u('User/login',['quicklogin'=>1]) }}" class="button" external>短信快捷登录</a>
            </div>
            <div class="y-box">
                <div class="y-input y-ptdl">
                    <i class="icon iconfont">&#xe65d;</i>
                    <input type="text" name="cellphone" id="cellphone" class="tel" value="" placeholder="请输入手机号码" maxlength="11">
                </div>
                <div class="y-input y-password y-pswd">
                    <i class="icon iconfont">&#xe63b;</i>
                    <input type="password" name="password" id="password" class="password" value="" placeholder="请输入密码">
                    <b class="icon iconfont eye">&#xe657;</b>
                </div>
                <div class="mt15">
                    <a href="javascript:login()" class="button button-big button-fill button-danger" id="login">登录</a>
                </div>
                <div class="y-yyyzm clearfix mt15">
                    {{--<a href="javascript:$.loginWeixin();" class="fl y-wxkjdl"><i class="icon iconfont">&#xe64b;</i>微信快捷登录</a>--}}
                    <a href="{{ u('User/repwd') }}" class="fr">忘记密码？</a>
                </div>
                <div class="y-wxbigicon">
                    <span class="c-gray">第三方登录</span>
                    <a href="javascript:$.loginWeixin();"><div><i class="icon iconfont">&#xe64b;</i></div><p>微信登录</p></a>
                </div>
            </div>
            <script type="text/javascript">
                var quicklogin = "{{$quicklogin}}";
            </script>
        </div>
    @else
        <div class="content" id='1'>
            <div class="buttons-tab">
                <a href="{{ u('User/login',['quicklogin'=>2]) }}" class="button">普通登录</a>
                <a href="#" class="tab-link button active">短信快捷登录</a>
            </div>
            <div class="y-box">
                <div class="y-input y-account">
                    <i class="icon iconfont">&#xe614;</i>
                    <input type="text" name="cellphone" id="cellphone" class="tel" value="" placeholder="请输入手机号码" maxlength="11">
                </div>
                <div class="y-input y-yzm">
                    <!-- <div class="y-yzminput"> -->
                    <input type="text" name="identify" id="identify" value="" maxlength="6" placeholder="请输入验证码">
                    <!-- </div> -->
                    @if($vcodeType == 1)
                        <a href="#" class="button button-big button-fill button-danger" id="getmask" data-backfunction="$.checkverify()">获取验证码</a>
                    @else
                        <a href="javascript:getCode()" class="button button-big button-fill button-danger" id="getCode">获取验证码</a>
                    @endif
                    
                    <!--  y-ddbtn   (class)  59秒后重新发送   (文字) -->
                </div>
                <div class="mt15">
                    <a href="javascript:login()" class="button button-big button-fill button-danger pageloading" id="login">登录</a>
                </div>
            </div>
            <script type="text/javascript">
                var quicklogin = "{{$quicklogin}}";
            </script>
        </div>
    @endif

    <!-- 互动验证码 -->
    @include('wap.community._layouts.slide_auth_code')
@stop

@section($js)
<script type="text/javascript">
    $.loginWeixin = function() {
        var weixinlogin = {'scope':'snsapi_userinfo','state':'fanwe'};
        $.showPreloader('请稍等，正在进入微信...');
        setTimeout(function(){
            window.App ? window.App.wxlogin(JSON.stringify(weixinlogin)) : window.location.href = "{{ u('User/weixin') }}";
        }, 500);
    }
    function js_wxlogin(ErrCode, code, state, lang, country){
        $.showPreloader('请稍等，正在登录...');
        setTimeout(function(){
            if(ErrCode == 0){
                window.location.href = "{{u('User/accesstoken')}}"+"?code="+code+"&state=fwapp";
            }else{
                $.hidePreloader();
                $.toast("授权以后才可以微信登录");
            }
        }, 500);
    }

    var getcode_url = "{{ u('User/verify') }}";
    var path = "{!! u('User/imgverify')!!}?random=" + Math.random();
    var t;

    function getCode(){
        if($("#getCode").attr("banned") == "false"){
            return false;
        }
        var modal = $.modal({
          title: '请输入图片验证码',
          text: '',
          afterText:  '<div class="yz_color_style verifynotice">'+
                        '<div class="m-tkbg">'+
                            '<div class="x-tkbg">'+
                                '<div class="x-tkbgi">'+
                                    '<div class="m-tkny">'+
                                        '<div class="m-tkinfor">'+
                                            '<p class="x-tkfont m-tktextare x-imgverify" style="padding: 2em 10px;font-size: .875em;color: #999;max-height: 260px;overflow: auto;">'+
                                                '<input id="vcode_input" type="text" name="imgverify" class="fl" style="height: 40px;width: 100px;border: 1px solid #ced6dc;">'+
                                                '<img src="{{ u('User/imgverify')}}" class="fl ml10" id="imgverify">'+
                                                '<span id="changeimg" class="fl ml10 mt15" style="cursor:pointer;"> 换一张</span>'+
                                            '</p>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>',
          buttons: [
            {
                text: '返回'
            },
            {
                text: '确定',
                bold: true,
                onClick: function () {
                    $.checkverify();
                }
            },
          ]
        });
    }

    // $(document).on("touchend","#changeimg",function(){
    //     $("#imgverify").attr('src',path);
    // });

    $(document).on("touchend","#changeimg",function(){
        path = "{!! u('User/imgverify')!!}?random=" + Math.random();
        $("#imgverify").attr('src',path);
    });

    $(document).on("touchend",".canver",function(){
        $("#imgverify").attr('src',path);
    });


    //发送验证码
    $.checkverify = function(){
        var imgverify = $("input[name=imgverify]").val();
        mobile = $("#cellphone").val();
        if(mobile != ""){
            var reg = /^1[\d+]{10}$/;
            if(!reg.test(mobile)){
                $.toast('请输入正确的手机号码'); 
                return false;
            }
        }else{
            $.toast("手机号码不能为空");
            return false;
        }

        $.post(getcode_url,{mobile:mobile,imgverify:imgverify},function(result){
            if(result.code == 0){
                $("#getCode").addClass("last-time");
                $.lastTime();
                $("#imgverify").attr('src',path);
                $("input[name=imgverify]").empty();
                // $('.verifynotice').addClass('none').hide();
                $.toast(result.msg);
            }else{
                $("#imgverify").attr('src',path);
                $.toast("验证码错误");
            }
        },'json');
    };

    var type = "getCode";
    if("{{ $vcodeType }}" == 1) {
        type = "getmask";
    }

    //倒计时
    var wait = 60;//获取验证码等待时间(秒)
    $.lastTime = function(){
        if (wait == 0) {
            $("#"+type).attr("banned","true").css("background-color","rgb(82, 178, 246)").css("font-size","0.875em").removeClass("last-time");
            $("#"+type).html("重新发送");
            wait = 60;
        } else {
            if($("#"+type).hasClass("last-time") == false){
                $("#"+type).css("font-size","14px").css("background-color","gray");
                $("#"+type).addClass("last-time")
            }

            $("#"+type).attr("banned","false");//倒计时过程中禁止点击按钮
            $("#"+type).html(wait + "s 重新发送");//改变按钮中value的值
            wait--;
            t = setTimeout(function() {
                $.lastTime();//循环调用
            },1000)
        }
    }

    function login() {
        var mobile = $("#cellphone").val();
        var verify = $("#identify").val();
        var pwd = $("#password").val();
        var reg = /^1\d{10}$/;
        if(!reg.test(mobile)){
            $.toast("请输入正确的手机号码");
            return false;
        }

        if(quicklogin != 2){
            var data = {
                  mobile:mobile,
                  verifyCode:verify
                };
            if(data.verifyCode ==  ''){
                $.toast("请输入验证码");
                return false;
            }
        }else{
            var data = {
              mobile:mobile,
              pwd:pwd
            };
            if(data.pwd == '' ){
                $.toast("请输入密码");
                return false;
            }
        }
        $.showPreloader('登录中...');
        $.post("{{ u('User/dologin') }}",data,function(res){
            $.hidePreloader();
            if(res.code == 0){
                $.regPushDevice(res.data.id,"{!! $return_url !!}");
            }else{
                $.toast(res.msg);
            }
        },"json");
    }

    function js_apns(devive,token){
        var data = new Object();
        data.devive = devive;
        data.apns = token;
        data.id = FANWE.PUSH_REG_ID;

        $.post("{!! u('UserCenter/regpush') !!}", data, function(result){
            window.location.href = "{!! $return_url !!}";
        });
    }

    //清理页面信息
    $.clearInfo = function(){
        $("#"+type).text('获取验证码').attr({'style':'', 'banned':'true'}).removeClass("last-time");
        clearTimeout(t);
    }

    $.clearInfo();
</script>
@stop

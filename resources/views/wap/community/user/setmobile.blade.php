@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        @if($args['id'] > 0)
            <!-- 邀请注册不显示返回按钮 -->
        @else
            <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
                <span class="icon iconfont">&#xe600;</span>返回
            </a>
        @endif
        <h1 class="title f16">设置手机</h1>
    </header>
@stop

@section('css')
    <style type="text/css">
        input#vcode_input{height: 40px;width: 100px;border: 1px solid #ced6dc;}
        .x-tkfont{padding: 2em 10px;font-size: .875em;color: #999;max-height: 260px;overflow: auto;}
    </style>
@stop

@section('content')
    <div class="content" id="page-reg">
        <div class="y-box">
            <div class="y-input y-account">
                <i class="icon iconfont">&#xe614;</i>
                <input type="text" name="cellphone" id="cellphone" class="tel f12" value="" placeholder="请输入11位手机号码" maxlength="11">
            </div>
            <div class="y-input y-password y-pswd">
                <i class="icon iconfont">&#xe63b;</i>
                <input type="password" name="password" id="password" class="password f12" value="" placeholder="6-20位，建议数字\字母\符号组合">
                <b class="icon iconfont eye">&#xe657;</b>
            </div>
            <div class="y-input y-password y-pswd">
                <i class="icon iconfont">&#xe63b;</i>
                <input type="password" name="password_new" id="password_new" class="f12" value="" placeholder="重复密码">
                <b class="icon iconfont eye">&#xe657;</b>
            </div>
            <div class="mt15">
                <a href="javascript:reg();" class="button button-big button-fill button-danger"  id="reg" >确定</a>
            </div>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        function reg() {
            var mobile = $("#cellphone").val();
            var pwd = $("#password").val();
            var pwds = $("input[name=password_new]").val();
            var reg = /^1[3|4|5|8][0-9]\d{8}$/;
            if(!reg.test(mobile)){
                $.toast("请输入正确的手机号码");
                return false;
            }
            var data = {
                mobile:mobile,
                pwd:pwd,
                pwds:pwds
            };

            if(data.pwd == '' || data.pwds == ''){
                $.toast("密码不能为空");
                return false;
            }
            if(data.pwd != data.pwds){
                $.toast("两次密码密码不一致");
                return false;
            }

            $.post("{{ u('User/doregbyweixin') }}",data,function(res){
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
    </script>
@stop
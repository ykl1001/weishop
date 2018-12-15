@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">修改密码</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="content-block-title c-gray2">验证码已发送至{{ substr_replace($user['mobile'], '****', 3, 4) }}</div>
        <div class="list-block">
            <ul class="y-wdzh y-sz y-zfzhmm">
                <!-- Text inputs -->
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">验证码</div>
                            <div class="item-input">
                                <input type="text" placeholder="请输入验证码" name="verifyCode">
                            </div>
                            <div class="pa y-zhmmbtn" id="getCode" disabled="false"><a href="#" class="c-white">已发送(60S)</a></div><!-- <div class="pa y-zhmmbtn c-bg">重新发送 -->
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">新密码</div>
                            <div class="item-input">
                                <input type="password" placeholder="请输入新密码" class="" name="newPwd">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner y-nobor">
                            <div class="item-title label f14">重复密码</div>
                            <div class="item-input">
                                <input type="password" placeholder="请再次输入新密码" class="c-gray2" name="reNewPwd">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title c-gray2">密码长度在6位，建议数字、字母、符号组合</div>
        <p class="y-bgnone mb10"><a class="y-paybtn f16 dorepaypwd">确定</a></p>
    </div>
    </div>
        
@stop

@section($js)
<script type="text/javascript">
    $(document).ready(function(){
        $.getCode(1);
    })

    //获取验证码
    $.getCode = function(type){
        var args = {
            mobile : "{{ $user['mobile'] }}",
            type : "repaypass"
        };
        $.post("{{ u('UserCenter/verify') }}", args,function(res){
            if(res.code == 0){
                $.lastTime();
                if(type != 1){
                    $.toast(res.msg);
                }
            }else{
                if(type != 1) {
                    $.toast(res.msg);
                }
            }
        },"json");
    }
    $(document).on("touchend","#getCode",function(event){
        if($(this).attr("disabled") == "false"){
            return false;
        }
        event.preventDefault();
        event.stopPropagation();
        event.isImmediatePropagationStopped();
        $.getCode(2);
    })

    //倒计时
    var wait = 60;//获取验证码等待时间(秒)
    $.lastTime = function(){
        if (wait == 0) {
            $("#getCode").attr("disabled","true").addClass("c-bg").removeClass("last-time");
            $("#getCode").find("a").html("重新发送");
            wait = 60;
        } else {
            if($("#getCode").hasClass("last-time") == false){
                $("#getCode").addClass("last-time").removeClass("c-bg");
            }
            $("#getCode").attr("disabled","false");//倒计时过程中禁止点击按钮
            $('#getCode').find("a").html("已发送(" + wait + "S)");//改变按钮中value的值
            wait--;
            setTimeout(function() {
                $.lastTime();//循环调用
            },1000)
        }
    }

    $(document).on("click", ".dorepaypwd", function(){
        var args = {
            verifyCode : $("input[name=verifyCode]").val(),
            newPwd : $("input[name=newPwd]").val(),
            reNewPwd : $("input[name=reNewPwd]").val(),
            type : 1
        };
        if(args.newPwd != args.reNewPwd){
            $.toast("两次输入密码不一致");
            return;
        }
        $.showPreloader('正在修改支付密码...');
        $.post("{{ u('UserCenter/dorepaypwd') }}",args, function(res){
            $.hidePreloader();
            if(res.status){
                $.alert(res.msg,function(){
                    @if((int)Input::get('pay') == 0)
                    var return_url = "{!! u('UserCenter/info') !!}";
                    $.router.load(return_url, true);
                            @else
                            var return_url = "{!! u('Order/cashierdesk', ['orderId'=>Input::get('orderId')]) !!}";
                    window.location.href = return_url;
                    @endif
            });
            }else{
                $.toast(res.msg);
            }
        },"json");
    })
</script>
@stop

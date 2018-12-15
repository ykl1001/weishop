@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Mine/bank',['id'=>$data['id']]) }}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('content')
    <div class="y-centblock">
        <p class="f12">已发送验证码短信到</p>
        <p class="f30">{{$data['mobile']}}</p>
    </div>
    <div class="list-block y-SMSverify">
        <ul>
            <li class="item-content y-yzm">
                <div class="item-inner">
                    <div class="item-input">
                        <input type="text" maxlength="6" class="f14" placeholder="请输入短信验证码" name="verifyCode" id="verifyCode" value="">
                        <input type="hidden" class="f14" name="mobile" id="mobile" value="{{$data['mobile']}}">
                        <a href="#" id="getCode" onclick="$.sendBank()"  class="y-boundbtn bg_ff2d4b">发送验证码</a>
                        <!-- 按钮背景 红：bg_ff2d4b  灰：bg_a5a5a5 -->
                    </div>
                </div>
            </li>
        </ul>
    </div>
   <div class="account_hd_bottom content-padded">
       <a  href="#" class="button button-fill button-success" id="verifyCode_show">下一步</a>
   </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            $("#{{$id_action.$ajaxurl_page}} #verifyCode_show").click(function(){

                var mobile = $("#{{$id_action.$ajaxurl_page}} #mobile").val();
                var verifyCode = $("#{{$id_action.$ajaxurl_page}} #verifyCode").val();
                $.post("{{u('Mine/verifyCodeCk')}}", {mobile:mobile,verifyCode:verifyCode}, function(result){
                    var url = "{!!u('Mine/bank',['id'=>$data['id']])!!}&verifyCode="+result.data;
                    if(result.code == 0){
                        JumpURL(url,'#seller_bank_view',2);
                    }else{
                        $.toast(result.msg);
                    }
                });
                return false;
            });
            $.sendBank = function(){
                var mobile = $("#{{$id_action.$ajaxurl_page}} #mobile").val();

                $.post("{{u('Staff/verify')}}", {mobile:mobile}, function(result){
                    if(result.code == 0){
                        $.lastTime();
                    }else{
                        $.toast(result.msg);
                    }
                });
            }
            //倒计时
            var wait = 60;//获取验证码等待时间(秒)
            $.lastTime = function(){
                if (wait == 0) {
                    $("#getCode").attr("onclick","$.sendBank()").addClass("bg_ff2d4b").removeClass("bg_a5a5a5").removeClass("last-time").css("color",'#FFF');
                    $("#getCode").html("重新发送");
                    wait = 60;
                } else {
                    if($("#getCode").hasClass("last-time") == false){
                        $("#getCode").addClass("last-time").removeClass("bg_ff2d4b").addClass("bg_a5a5a5").css("color",'#FFF');
                    }
                    $("#getCode").attr("onclick","javascript:return false;");//倒计时过程中禁止点击按钮
                    $('#getCode').html(wait + "S后重新获取");//改变按钮中value的值
                    wait--;
                    setTimeout(function() {
                        $.lastTime();//循环调用
                    },1000)
                }
            }
            $.sendBank();
        });
    </script>
@stop
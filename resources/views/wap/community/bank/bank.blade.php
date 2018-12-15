@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left isExternal" href="{{ u('Bank/carry') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$top_title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('content')
    <!-- new -->
    <div class="content" data-ptr-distance="55" data-distance="50" id='udb_bank'>
   <form  method="post"  id="ajax-form">
        <div class="list-block y-boundbankcard">
           <ul>
               <li>
                   <div class="item-content">
                       <div class="item-inner">
                           <div class="item-title label">持卡人</div>
                           <div class="item-input">
                               <input type="text" class="f14" placeholder="请输入持卡人姓名" @if($old)readonly="readonly" @endif name="name" id="name" value="{{$data['name']}}">
                           </div>
                       </div>
                   </div>
               </li>
               <li>
                   <div class="item-content">
                       <div class="item-inner">
                           <div class="item-title label">开户行</div>
                           <div class="item-input">
                               <input type="text" class="f14" placeholder="请输入开户银行" name="bank" @if($old)readonly="readonly" @endif id="bank" value="{{$data['bank']}}">
                           </div>
                       </div>
                   </div>
               </li>
               <li>
                   <div class="item-content">
                       <div class="item-inner">
                           <div class="item-title label">卡号</div>
                           <div class="item-input">
                               <input type="text" class="f14" placeholder="请输入银行卡号" maxlength="19" name="bankNo" @if($old)readonly="readonly" @endif id="bankNo" value="{{$data['bankNo']}}">
                           </div>
                       </div>
                   </div>
               </li>
               <li>
                   <div class="item-content">
                       <div class="item-inner">
                           <div class="item-title label">手机号码</div>
                           <div class="item-input">
                               <input type="text" class="f14" maxlength="11" placeholder="请输入银行卡手机号码" name="mobile" @if($old)readonly="readonly" @endif id="mobile" value="{{$data['mobile']}}">
                           </div>
                       </div>
                   </div>
               </li>
               @if(!$data)
               <li>
                  <div class="item-content y-yzm">
                      <div class="item-inner">
                          <div class="item-title label">验证码</div>
                          <div class="item-input">
                              <input type="text"  name="verifyCode" id="verifyCode"  class="f14" placeholder="请输入短信验证码">
                              <a href="#" id="getCode" onclick="$.sendBank()" class="y-boundbtn c-graybg">发送验证码</a>
                              <!-- 按钮背景 红：bg_ff2d4b  灰：bg_a5a5a5 -->
                          </div>
                      </div>
                  </div>
               </li>
               @endif
           </ul>
       </div>
       @if(!$data)
           <div class="p10">
               <button type="submit" class="button button-fill button-danger  bg_ff2d4b y-paybtn">保存</button>
           </div>
       @else
           @if(!$verifyCode)
               <div class="p10">
                   <a href="#" onclick="$.href('{{u('Bank/verifyCode',['id'=>$data['id']])}}')" class=" y-paybtn f16">编辑</a>
               </div>
           @else
               <input type="hidden" class="f14" name="verifyCode" id="verifyCode" value="{{$verifyCode}}">
               <input type="hidden" class="f14" name="id" id="id" value="{{$data['id']}}">
               <div class="p10">
                  <button type="submit"  class="y-paybtn f16paybtn">保存</button>
               </div>
           @endif
       @endif
   </form>
</div>
@stop
@section("js")
    <script type="text/javascript">
        $(function(){
            $("#udb_bank #ajax-form").submit(function(){
                $.post("{{u("Bank/bankSve")}}", $(this).serialize(), function(result){
                    if(result.code == 0){
                        var url ="{{$url}}";
                        if(url == "index"){
                            $.href('{{u('Bank/bank',['id'=>$data['id']])}}');
                        }else{
                            $.href('{{u('Bank/carry')}}');
                        }
                    }else{
                        $.toast(result.msg);
                    }
                });
                return false;
            });
            $.sendBank = function(){
                var mobile = $("#udb_bank #mobile").val();

                $.post("{{u('Bank/verify')}}", {mobile:mobile}, function(result){
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
                    $("#getCode").attr("onclick","$.sendBank()").addClass("c-bg").removeClass("c-graybg").removeClass("last-time").css("color",'#FFF');
                    $("#getCode").html("重新发送");
                    wait = 60;
                } else {
                    if($("#getCode").hasClass("last-time") == false){
                        $("#getCode").addClass("last-time").removeClass("c-bg").addClass("c-graybg").css("color",'#FFF');
                    }
                    $("#getCode").attr("onclick","javascript:return false;");//倒计时过程中禁止点击按钮
                    $('#getCode').html(wait + "S后重发");//改变按钮中value的值
                    wait--;
                    setTimeout(function() {
                        $.lastTime();//循环调用
                    },1000)
                }
            }
        });
    </script>
@stop
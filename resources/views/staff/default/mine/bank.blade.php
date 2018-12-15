@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Mine/'.$url) }}','#seller_{{$url}}_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('content')
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
                               <input type="text" class="f14" placeholder="请输入银行卡手机号码" name="mobile" @if($old)readonly="readonly" @endif id="mobile" value="{{$data['mobile']}}">
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
                              <a href="#" id="getCode" onclick="$.sendBank()" class="y-boundbtn bg_ff2d4b" style="color: #000">发送验证码</a>
                              <!-- 按钮背景 红：bg_ff2d4b  灰：bg_a5a5a5 -->
                          </div>
                      </div>
                  </div>
               </li>
               @endif
           </ul>
       </div>
       @if(!$data)
           <div class="y-plr10">
               <button type="submit"style="width: 100%"   class="button button-fill button-danger  bg_ff2d4b">保存</button>
           </div>
       @else
           @if(!$verifyCode)
               <div class="y-plr10">
                   <a href="#" onclick="JumpURL('{{u('Mine/verifyCode',['id'=>$data['id']])}}','#mine_verifyCode_view',2)" class="button button-fill button-danger  bg_ff2d4b">编辑</a>
               </div>
           @else
               <input type="hidden" class="f14" name="verifyCode" id="verifyCode" value="{{$verifyCode}}">
               <input type="hidden" class="f14" name="id" id="id" value="{{$data['id']}}">
               <div class="y-plr10">
                  <button type="submit"style="width: 100%"   class="button button-fill button-danger  bg_ff2d4b">保存</button>
               </div>
           @endif
       @endif
   </form>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            $("#{{$id_action.$ajaxurl_page}} #ajax-form").submit(function(){
                $.post("{{u("Mine/bankSve")}}", $(this).serialize(), function(result){
                    if(result.code == 0){
                        var url ="{{$url}}";
                        if(url == "index"){
                            JumpURL('{{u('Mine/bank',['id'=>$data['id']])}}','#mine_bank_view',1);
                        }else{
                            JumpURL('{{u('Mine/carry')}}','#seller_carry_view',2);
                        }
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
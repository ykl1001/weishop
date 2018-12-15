@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{ u('UserCenter/index') }}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的账号</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <div class="list-block">
            <ul class="y-wdzh y-sz">
                <!-- Text inputs -->
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">头  像 </div>
                            <div class="item-after">
                                <form>
                                    <span class="c-black y-wdtxi">
                                        <label id="imglabel" class="img-up-lb" style="display:inline-block;">
                                            @if(!empty($user['avatar']))
                                                <img src="{{ formatImage($user['avatar'],100,100) }}" alt="" class="avatar_img" style="padding-top:-5px;">
                                            @else
                                                <img src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="" class="avatar_img" style="padding-top:-5px;">
                                            @endif
                                         </label>
                                     </span> 
                                </form>
                                <i class="icon iconfont ml10 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="$.href('{{ u('UserCenter/nick') }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">会员名称</div>
                            <div class="item-input">
                                <input type="text" value="{{$user['name']}}" id="name" placeholder="经自己取个会员名" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="@if(empty($user['mobile'])) $.href('{{ u('Order/bindmobile2') }}') @else @if($user['isDelUser'] == true)$.href('{{ u('UserCenter/verifymobile') }}') @else  $.showUpdateUserM() @endif @endif">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">手机号码</div>
                            <div class="item-input">
                                <input type="text" value="{{$user['mobile']}}" placeholder="@if(empty($user['mobile'])) 未绑定 @else 手机号码 @endif" class="" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="$.href('{{ u('UserCenter/repwd') }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">修改密码</div>
                            <div class="item-input">
                                <input type="password" value="**********" class="c-gray2" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="mt10" onclick="$.href('{{ u('UserCenter/paypwd',['type'=>3]) }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">支付密码</div>
                            <div class="item-input">
                                <input @if($isPayPwd == 0)type="text"@else type="password" @endif value="未设置" class="c-gray2">
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
			
        </div>
    </div>
@stop

@section($js)

    <script type="text/javascript"> 
        $(document).on('click','.avatar_img', function () {
            $(this).fanweImage({
                width:320, 
                height:320, 
                callback:function(url, target) {
                    $('.avatar_img').get(0).src = url;
                    if(target != "" && url != ""){
                        $.post("{{ u('UserCenter/updateinfo') }}", { "name": $("#name").val(), "avatar": url }, function (res)
                        {
                            if (res.code == '99996')
                            {
                                $.router.load("{{ u('User/login') }}", true);
                            }
                            else if (res.code != 0)
                            {
                                $.alert(res.msg);
                            }
                        }, "json")
                    }
                }
            });
        }); 
        Zepto(function($){ 
            $.showUpdateUserM = function(){
                $.modal({
                    title:  '操作提示',
                    text: '您的手机号码已经绑定了商家平台，更换成功旧手机号码将作废,请用新手机号码登录商家平台!',
                    buttons: [
                        {
                            text: '确定',
                            onClick: function() {
                                $.href('{{ u('UserCenter/verifymobile') }}');
                            }
                        },
                        {
                            text: '取消'
                        },
                    ]
                })
            }
        });
    </script>
@stop
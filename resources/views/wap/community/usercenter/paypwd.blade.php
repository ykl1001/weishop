@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ !empty($nav_back_url) ? $nav_back_url : 'javascript:$.back()'}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">@if($isPayPwd == 0) 设置密码 @else 修改密码 @endif</h1>
    </header>
@stop

@section('content')
    @if($isPayPwd == 0)
        <div class="content" id=''>
            <div class="list-block">
                <ul class="y-wdzh y-sz y-zfzhmm">
                    <!-- Text inputs -->
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">密码</div>
                                <div class="item-input">
                                    <input type="number" placeholder="请输入密码" id="udb_input" data-id="newPwd" name="newPwd" maxlength="6" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">重复密码</div>
                                <div class="item-input">
                                    <input type="number" placeholder="请再次输入新密码" id="udb_input" data-id="reNewPwd" class="c-gray2" name="reNewPwd"  maxlength="6" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="content-block-title c-gray2">密码长度在6位，只能是数字！</div>
            <p class="y-bgnone mb10"><a class="y-paybtn f16 dorepaypwd">确定</a></p>
        </div>
    @else
        <div class="content" id=''>
            <div class="list-block">
                <ul class="y-wdzh y-sz y-zfzhmm">
                    <!-- Text inputs password-->
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">原密码</div>
                                <div class="item-input">
                                    <input type="number" id="udb_input" data-id="oldPwd" name="newPwd" placeholder="请输入原密码" maxlength="6" oninput="if(value.length>6)value=value.slice(0,6)"  onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">

                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">新密码</div>
                                <div class="item-input">
                                    <input type="number" placeholder="请输入新密码" id="udb_input" data-id="oldPwd" name="newPwd" maxlength="6" oninput="if(value.length>6)value=value.slice(0,6)"  onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">重复密码</div>
                                <div class="item-input">
                                    <input type="number" placeholder="请再次输入新密码" id="udb_input" data-id="reNewPwd" class="c-gray2" maxlength="6" oninput="if(value.length>6)value=value.slice(0,6)"  name="reNewPwd" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="content-block-title c-gray2">密码长度在6位，只能是数字！</div>
            <p class="y-bgnone mb10"><a class="y-paybtn f16 dorepaypwd">确定</a></p>
            <p class="tr mt10 mr10"><a href="{{ u('UserCenter/repaypwd') }}" class=" c-red f14">忘记支付密码</a></p>
        </div>
    @endif
@stop

@section($js)
    <script type="text/javascript">
        $(document).off("focus", "#udb_input");
        $(document).on("focus", "#udb_input",function(){
            $(this).attr("type","number").focus();
        });
        $(document).off("blur", "#udb_input");
        $(document).on("blur", "#udb_input",function(){
            $(this).attr("type","password");
        });
        $(document).on("click", ".dorepaypwd", function(){
            var args = {
                oldPwd : $("input[name=oldPwd]").val(),
                newPwd : $("input[name=newPwd]").val(),
                reNewPwd : $("input[name=reNewPwd]").val()
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
                        @elseif((int)Input::get('pay') == 2)
                        var return_url = "{{ $nav_back_url }}";
                        window.location.href = return_url;
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

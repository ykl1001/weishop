@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        @if($args['id'] > 0)
            <!-- 邀请注册不显示返回按钮 -->
        @else
            <a class="button button-link button-nav pull-left back isExternal" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
                <span class="icon iconfont">&#xe600;</span>返回
            </a>
        @endif
        <h1 class="title f16">成为分销商</h1>
    </header>
@stop

@section('css')
    <style type="text/css">
        .y-xtexta{height: 10em;width: 100%;border-radius: .25rem;border:1px solid #dadada;resize: none;padding:.5rem;}
    </style>
@stop

@section('content')
    <div class="content" id="page-reg">
        <div class="y-box">
            <div class="y-input">
                <textarea type="text" name="remark" id="remark" class="tel f12 y-xtexta" maxlength="50" placeholder="输入您的申请需求!(可以不填写)">{{$data['remark']}}</textarea>
            </div>

            <div class="mt15">
                <a href="javascript:reg();" class="button button-big button-fill button-danger"  id="reg" >确定</a>
            </div>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        var noreg = 0;
        function reg() {
            var remark = $("#remark").val();
            var data = {
                remark:remark
            };
            if(noreg == 1){
                return false;
            }
            noreg = 1;
            $.post("{{ u('UserCenter/regsharechapman') }}",data,function(res){
                if(res.code == 0){
                    $.toast('申请成功');
                    setTimeout(2000, window.location.href = "{{u('UserCenter/index')}}")
                }else{
                    noreg = 0;
                    $.toast(res.msg);
                }
            },"json");
        }
    </script>
@stop
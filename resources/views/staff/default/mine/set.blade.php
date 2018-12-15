@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="item">
        <a href="#" onclick="JumpURL('{{u('Mine/feedback')}}','#mine_feedback_view',2)">
            <i class="iconfont left bj74c9c2">&#xe663;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                意见反馈
            </div>
        </a>
        <a href="#" onclick="JumpURL('{{u('More/detailAll',['code'=>3])}}','#more_detailAll_view',2)">
            <i class="iconfont left bjffa70f">&#xe665;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                关于我们
            </div>
        </a>
    </div>
    <div class="pd050">
        <a class="ui-button_login_out ui-button_login_out-logout" href="#">退出登录</a>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        function js_apns(devive,token){
            var data = new Object();
            data.devive = devive;
            data.apns = token;
            data.id = FANWE.PUSH_REG_ID;
            data.role = FANWE.ROLE;

            $.post("{!! u('Staff/regpush') !!}", data, function(result){
                window.location.href = "{{ u('Staff/login') }}";
            });
        }
    </script>
@stop

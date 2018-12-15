@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')@stop
@section('show_refresh')@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="pcenter-head">
        <div class="headimg"><div><img src="{{$staff['avatar']}}" onerror='this.src="{{ asset('wap/community/client/images/wdtx-wzc.png') }}"'></div></div>
        <a class="gotocenter" href="#" onclick="JumpURL('{{u('Mine/account2')}}','#mine_account2_view',2)">{{$staff['name']}}<i class="iconfont">&#xe64b;</i></a>
    </div>
    <div class="blank050"></div>

    <div class="item">
        <a href="#" onclick="JumpURL('{{u('Mine/message')}}','#mine_message_view',2)" class=" @if($hasNewMsg) newstip @endif" href="">
            <i class="iconfont left bj7cbbfe">&#xe666;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                消息通知
                <span class="memo"></span>
            </div>
        </a>
        @if($invitation['sellerStatus'] == 1 &&  ($role == 1 || $role == 3|| $role == 5|| $role == 7))
        <a href="#" onclick="JumpURL('{{u('Invitation/index')}}','#invitation_index_view',1)">
            <i class="iconfont left bj74c9c2">&#xe642;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                邀请返现
                <span class="memo"></span>
            </div>
        </a>
        @endif
        <a  href="#" onclick="JumpURL('{{u('More/detailAll',['code'=>7])}}','#more_detailAll_view',2)">
            <i class="iconfont left bj87ce4c">&#xe667;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                使用帮助
                <span class="memo"></span>
            </div>
        </a>
    </div>
    <div class="blank050"></div>
    <div class="item">
        <a onclick="JumpURL('{{u('Mine/set')}}','#mine_set_view',2)" >
            <i class="iconfont left bjffa70f">&#xe668;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                设置
            </div>
        </a>
        @if($staff['isSystem'] == 1)
        <a onclick="IsWork('{{$staff['isWork']}}')" >
            <i class="iconfont left bjff9667">&#xe63d;</i>
            <i class="iconfont right">@if($staff['isWork'] == 1)上班@else下班@endif&#xe64b;</i>
            <div class="con">
                当前状态
            </div>
        </a>
        <a onclick="JumpURL('{{u('Mine/account')}}','#mine_account_view',2)" >
            <i class="iconfont left b-fb8486">&#xe657;</i>
            <i class="iconfont right">提现&#xe64b;</i>
            <div class="con">
                账户余额:{{$staff['extend']['withdrawMoney'] or 0}}
            </div>
        </a>
        @endif
    </div>
@stop
@section('preloader')@stop
@section($js)
    <script type="text/javascript">
        function IsWork(status){
            if(status == 1){ //上班的情况就是提示下班
                $.modal({
                    title:  '提示',
                    text: "上班一天了，好好休息一下！",
                    buttons: [
                        {
                            text: '取消',
                            bold: true
                        },{
                            text: '确定',
                            onClick: function() {
                                $.post("{{ u('Mine/updatework') }}",{'is_work':0},function(res){
                                    if(res.code == 0){
                                        $.toast('打卡成功');
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1000);
                                    }else{
                                        $.toast(res.msg);
                                    }
                                })
                            }
                        },
                    ]
                })
            }else{
                $.modal({
                    title:  '提示',
                    text: "系统将会分配订单给您配送，请做好准备！",
                    buttons: [
                        {
                            text: '取消',
                            bold: true
                        },{
                            text: '确定',
                            onClick: function() {
                                $.post("{{ u('Mine/updatework') }}",{'is_work':1},function(res){
                                    if(res.code == 0){
                                        $.toast('打卡成功');
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1000);
                                    }else{
                                        $.toast(res.msg);
                                    }
                                })
                            }
                        },
                    ]
                })
            }
        }
    </script>
@stop

@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav c-bg y-noborafter">
        <a class="button button-link button-nav pull-left c-white" href="#" data-transition='slide-out'>赚钱</a>
        <a class="button button-link button-nav pull-right c-white" href="{{u('UserCenter/balance')}}" data-transition='slide-out'>我的余额</a>
    </header>
@stop

@section('content')

    @include('wap.community._layouts.bottom')

    <div class="content" id=''>

        <div class="c-bg tc c-white" onclick="$.href('{{u('UserCenter/balance')}}')">
            <p>今日收入</p>
            <p class="f24 p15">{{$data['returnFee'] or 0}}</p>
        </div>

        <div class="y-myteam y-makemoney">
            <div class="buttons-tab y-borafter">
                <a href="#" class="tab-link button">
                    <div class="icon iconfont y-makeml f28">&#xe669;</div>
                    <div class="y-makemr">
                        <p class="c-red f16">{{$data['distributionMoney'] or 0}}</p>
                        <p class="c-gray2 f12">累计收入</p>
                    </div>
                </a>
                <a href="#" class="tab-link button">
                    <div class="icon iconfont y-makeml f28">&#xe688;</div>
                    <div class="y-makemr">
                        <p class="c-black f16">{{$data['waitMoney'] or 0}}</p>
                        <p class="c-gray2 f12">待入账</p>
                    </div>
                </a>
            </div>
            <div class="buttons-tab y-borafter">
                <a href="#" class="tab-link button">
                    <div class="icon iconfont y-makeml f24">&#xe63d;</div>
                    <div class="y-makemr">
                        <p class="c-black f16">{{$data['orderCount'] or 0}}</p>
                        <p class="c-gray2 f12">订单总数</p>
                    </div>
                </a>
                <a href="#" class="tab-link button">
                    <div class="icon iconfont y-makeml f24">&#xe676;</div>
                    <div class="y-makemr">
                        <p class="c-black f16">{{$data['orderMoney'] or 0}}</p>
                        <p class="c-gray2 f12">订单总额</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="y-tccenter c-bgfff p10 f13">
            <div>订单详情</div>
            <a href="{{u('MakeMoney/order')}}" class="c-gray2 f13">查看<i class="icon iconfont f14 vat ml5">&#xe602;</i></a>
        </div>

        <div class="y-myteam mt10 y-makemoney">
            <div class="buttons-tab">
                <a href="javascript:$.href('{{ u("Invitation/userlists") }}')" class="button" >
                    <div class="icon iconfont y-makeml f24 c-red">&#xe68b;</div>
                    <div class="y-makemr">
                        <p class="c-black f16">我的好友</p>
                        <p class="c-gray2 f12">共计{{$data['userCount']}}人</p>
                    </div>
                </a>
                <a href="javascript:$.href('{{ u("Invitation/index") }}')" class="button">
                    <div class="icon iconfont y-makeml f22 c-blue3">&#xe68d;</div>
                    <div class="y-makemr">
                        <p class="c-black f16">推荐有礼</p>
                        <p class="c-gray2 f12">推荐好友返利</p>
                    </div>
                </a>
            </div>
        </div>

    </div>

@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            //BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";
        });
    </script>
@stop
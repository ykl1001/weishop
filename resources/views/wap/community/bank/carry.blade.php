@extends('wap.community._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left"  href="#" onclick="$.href('{{ u('UserCenter/balance') }}')"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a  href="#" onclick="$.href('{{ u('Bank/withdrawlog') }}')" class="button button-link button-nav pull-right">提现记录</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section($css)
    <style>
        .y-paybtn_ccc{
            background: #ccc;
        }
    </style>
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    <div class="content" id=''>
        <div class="admin-shop-money-carry   lists_item_ajax">
            @include("wap.community.bank.carry_item")
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
                $(document).off("click",".page-current .ajax-success-bnt");
                $(document).on("click",".page-current .ajax-success-bnt",function(){
                    var money = $(".page-current input[name='carry_money']").val();
                    if(!money){
                        $.toast("提现金额不能为空");
                        return false;
                    }
                    if(money < 100){
                        $.toast("单次提现不能低于100");
                        return false;
                    }
                    $.post("{{ u('Bank/withdraw') }}",{'amount': money},function(res){
                        $.toast(res.msg);
                        if(res.code == 0){
                            var url = "{{u('UserCenter/balance')}}";
                            $.href(url);
                        }
                    },"json");
            });
        });
    </script>
@stop

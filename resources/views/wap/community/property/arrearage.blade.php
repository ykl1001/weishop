@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" onclick="javascript:$.back();" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">生活缴费</h1>
        <a class="button button-link button-nav pull-right open-popup toedit pageloading changeTo" href="#" data-popup=".popup-about">缴费记录</a>
    </header>
@stop

@section('content')
    @if($arrearage['Code'] != 0)
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">
                {{$arrearage['Msg']}}
            </p>
        </div>
    @else
        <div class="y-jdf">
            <p class="c-gray3 f14">账户余额(元)</p>
            <p class="c-black f36">{{$arrearage['Data']['Balances']['Balance'][0]['Balance']}}</p>
            <input type="number" placeholder="请输入充值金额" id="money">
        </div>
        <div class="list-block media-list y-syt lastbor">
            <ul>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title f14 c-black">缴费单位</div>
                            <div class="item-after f14 c-gray2">{{$args['unitname']}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title f14 c-black">户号</div>
                            <div class="item-after f14 c-gray2">{{$arrearage['Data']['Account']}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title f14 c-black">户名</div>
                            <div class="item-after f14 c-gray2">{{$arrearage['Data']['Balances']['Balance'][0]['AccountName']}}</div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <p class="y-bgnone"><a href="javascript:;payMoney()" class="y-paybtn f16">立即缴费</a></p>
    @endif
@stop

@section($js)
    <script>
        function payMoney(){
            var money = $("#money").val();
            var qian_money = "{{$arrearage['Data']['Balances']['Balance'][0]['Balance']}}";
            if(qian_money == "" || qian_money == 0){
                $.toast("没有查到数据或不欠费");
                return false;
            }
            if(qian_money < money){
                $.toast("输入金额必须大于等于账户余额");
                return false;
            }
            if(isFloat(money)) {
                str = money.split(".");
                console.log(str);
                if(str[1] == undefined){

                }else{
                    if (str[1].length > 2)//小数部分大于4
                    {
                        $.toast("小数部分最多两位数");
                        return false;
                    }
                }
            }else{
                $.toast("请输入数字");
                return false;
            }

            var title = "{{$args['productName']}}";
            var args2 = "{{$args2}}";
            $.router.load("{!! u('Order/livepay') !!}?money="+money+"&title="+title+"&args="+args2);
        }

        function isFloat(oNum)//判断是否为浮点数的函数
        {
            if(!oNum)
                return false;
            var strP=/^\d+(\.\d+)?$/;
            if(!strP.test(oNum))
                return false;
            try{
                if(parseFloat(oNum)!=oNum)
                    return false;
            }catch(ex){
                return false;
            }
            return true;
        }
    </script>
@stop
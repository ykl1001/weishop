@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-bartopnav">
        <div class="tc c-white pt10 pb10">
            <p>佣金总额</p>
            <p class="f24 mt5">{{$fx_userinfo['money'] or 0}}</p>
        </div>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="y-amount">
            <div class="y-amountinput">
                <input type="text" placeholder="最大兑换：{{$fx_userinfo['money']}}" class="f14 commission_money">
                <span class="f18 vam c-gray">=</span>
                <span class="f12 vam c-red y-exchangemoney"><span class="f18 exchange_money">0</span>元</span>
            </div>
        </div>
        <div class="p10"><a class="y-paybtn f16 fxexchangemake">兑换</a></div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            //兑换金额
            var commission_money = parseFloat("{{$fx_userinfo['money'] or 0}}");
            //兑换比例
            var fx_exchange_percent = parseFloat("{{$fx_exchange_percent}}");


            $(".commission_money").keyup(function(){
                var value = $(this).val() ? parseFloat($(this).val()) : 0;
                if(isNaN(value) || $.trim(value) == '')
                {
                    $(this).val('');
                }

                if(value > commission_money)
                {
                    value = commission_money;
                    $(this).val(value);
                }

                var money = parseFloat(value / fx_exchange_percent).toFixed(2);
                $(".exchange_money").text(money);
            });

            $(document).off('click', '.fxexchangemake');
            $(document).on('click', '.fxexchangemake', function(){
                $.showPreloader('兑换中...');
                var money = $(".commission_money").val() ? parseFloat($(".commission_money").val()) : 0;
                if(money <= 0)
                {
                    $.hidePreloader();
                    $.alert('兑换佣金必须大于0');
                    return false;
                }

                $.post("{{u('UserCenter/fxexchangemake')}}", {'money':money}, function(res){
                    $.hidePreloader();
                    if(res.code == 0)
                    {
                        $.alert('兑换成功！', function(){
                            $.href("{{u('UserCenter/index')}}");
                        });
                    }
                    else
                    {
                        $.alert('兑换失败！');
                    }
                    
                })
            })
        })
    </script>
@stop


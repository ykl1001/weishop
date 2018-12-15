@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left isExternal" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的余额</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content c-bgfff infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="y-balance c-bgfff">
            <p class="c-gray f12">账户余额(元)</p>
            <p class="f24 c-red tc mt20 mb20 pt5 pb5">{{$balance or '0.00'}}</p>
            <p class="f12 c-gray tc  mb20 pb5">冻结金额:{{$lockAmount or '暂无冻结金额'}}</p>
        </div>
		<div class="buttons-tab y-couponsnav y-borbefore">
                <a href="#" onclick="$.href('{{ u('UserCenter/recharge') }}')" class="tab-link button">
					<img src="{{asset('wap/community/newclient/images/y16.png')}}" width="20" class="va-4 mr5">充值
				</a>

                <a href="#" @if(!$bank) onclick="$.ShowBank()"  @else onclick="$.href('{{ u('Bank/carry') }}')" @endif class="tab-link button">
					<img src="{{asset('wap/community/newclient/images/y17.png')}}" width="20" class="va-3 mr5">
					提现
				</a>
            </div>
        <div class="content-block-title f14 c-gray y-blocktitle">余额交易记录</div>
        <div class="list-block media-list y-syt lastbor">
            <ul id="list">
                @if($data['paylogs'])
                    @include('wap.community.usercenter.balance_item')
                @else
                    <li class="x-null pa w100 tc mt20">
                        <i class="icon iconfont" style>&#xe645;</i>
                        <p class="f12 c-gray mt10">没有交易记录</p>
                    </li>
                @endif
            </ul>
        </div>
        <!-- 加载完毕提示 -->
        <div class="pa w100 tc allEnd none">
            <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
        </div>
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>
@stop

@section($js)

    <script>
        $(function() {

            $.ShowBank = function (){
                $.modal({
                    title:  '提示',
                    text: '您还没有可用于提现的银行卡，请先添加银行卡后使用提现',
                    buttons: [
                        {
                            text: '取消'
                        },
                        {
                            text: '添加银行卡',
                            onClick: function() {
                                $.href("{{u('Bank/bank')}}",'#seller_bank_view',2);
                            }
                        }
                    ]
                });
            }
            $(".y-records").css("min-height",$(window).height()-247);

            // 加载开始
            // 上拉加载
            var groupLoading = false;
            var groupPageIndex = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');

                groupLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;

                $.post("{{ u('UserCenter/balanceList') }}", data, function(result){

                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result.length!=0 || result != "") {
                        groupLoading = false;
                        groupPageIndex++;
                        $('#list').append(result);
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                    }
                });
            });

            // 下拉刷新
            $(document).off('refresh', '.pull-to-refresh-content');
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                groupLoading = true;
                var data = new Object;
                data.page = 1;

                $.post("{{ u('UserCenter/balanceList') }}", data, function(result){

                    result  = $.trim(result);
                    if (result.length!=0 || result != "") {
                        groupPageIndex = 2;
                        groupLoading = false;
                    }
                    $('#list').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });
            // 加载结束
            
            //部分IOS返回刷新
            if($.device['os'] == 'ios')
            {
                $(".isExternal").addClass('external');
            }
        })
    </script>

@stop
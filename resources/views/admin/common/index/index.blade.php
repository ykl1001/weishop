@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <div class="" style="padding: 0 20px;"><h3><b>待办事项：</b></h3></div>
    <div class="p10" style="padding-right: 0;">
        @yizan_yield("total")
        <div class="m-zxnavct">
            <ul class="clearfix">
                <li class="ls1 curl-top-left">
                    <a href="{{u('SellerApply/index',['isCheck' => 2])}}">
                        <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['seller']}}</b></p>
                        <p class="clearfix"><span class="fr f16 mr10">待审核商家</span></p>
                    </a>
                </li>
                <li class="ls2 curl-top-left">
                    <a href="{{u('Order/index')}}">
                        <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['order']}}</b></p>
                        <p class="clearfix"><span class="fr f16 mr10">订单管理</span></p>
                    </a>
                </li>
                <li class="ls3 curl-top-left">
                    <a href="{{u('UserRefund/index',array('status'=>0,'nav'=>1))}}">
                        <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['refund']}}</b></p>
                        <p class="clearfix"><span class="fr f16 mr10">待处理退款</span></p>
                    </a>
                </li>
                <li class="ls4 curl-top-left">
                    <a href="{{u('SellerWithdraw/index')}}">
                        <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['withdraw']}}</b></p>
                        <p class="clearfix"><span class="fr f16 mr10">待处理提现</span></p>
                    </a>
                </li>
                <li class="ls5 curl-top-left">
                    <a href="{{u('PropertyApply/index')}}">
                        <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['propertyApply']}}</b></p>
                        <p class="clearfix"><span class="fr f16 mr10">待审核物业</span></p>
                    </a>
                </li>
            </ul>
        </div>
        @yizan_stop
        <div class="m-ddsjct" style="display: none;">
            <div class="f-tt clearfix">
                <b class="fl f16 ml10">订单数据</b>
                <ul class="fr clearfix">
                    <li class="@if(Input::get('type') == 1 || Input::get('type') == '') on @endif wobble-top">
                        <a href="{{u('Index/index',array('type'=>1))}}">今天</a>
                    </li>
                    <li class="@if(Input::get('type') == 2) on @endif wobble-top">
                        <a href="{{u('Index/index',array('type'=>2))}}">昨天</a>
                    </li>
                    <li class="@if(Input::get('type') == 3) on @endif wobble-top">
                        <a href="{{u('Index/index',array('type'=>3))}}">本周</a>
                    </li>
                    <li class="@if(Input::get('type') == 4) on @endif wobble-top">
                        <a href="{{u('Index/index',array('type'=>4))}}">本月</a>
                    </li>
                </ul>
            </div>
            <!-- 订单表 -->
            <div class="m-orderct">
                <div class="u-orderct">
                </div>
            </div>
        </div>
        <div class="m-statistics-info ds-new">
            <div class="title">网站统计</div>
            <table class="tab-dasb">
                <tr>
                    <td rowspan="2">商城</td>
                    <td>商品数</td>
                    <td>今日订单</td>
                    <td>今日营业额</td>
                    <td>本月订单</td>
                    <td>本月营业额</td>
                    <td></td>
                </tr>
                <tr class="bold">
                    <td>{{ $storeInfo['goodsTotal'] }}</td>
                    <td>{{ $storeInfo['ordersDay'] }}</td>
                    <td>{{ $storeInfo['salesAmountDay'] }}</td>
                    <td>{{ $storeInfo['ordersMonth'] }}</td>
                    <td>{{ $storeInfo['salesAmountMonth'] }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td rowspan="2">加盟</td>
                    <td>商家总数</td>
                    <td>物业总数</td>
                    <td>今日订单</td>
                    <td>今日营业额</td>
                    <td>本月订单</td>
                    <td>本月营业额</td>
                </tr>
                <tr class="bold">
                    <td>{{ $sellerInfo['sellerTotal'] }}</td>
                    <td>{{ $sellerInfo['propertyTotal'] }}</td>
                    <td>{{ $sellerInfo['ordersDay'] }}</td>
                    <td>{{ $sellerInfo['salesAmountDay'] }}</td>
                    <td>{{ $sellerInfo['ordersMonth'] }}</td>
                    <td>{{ $sellerInfo['salesAmountMonth'] }}</td>
                </tr>
                <tr>
                    <td rowspan="2">网站</td>
                    <td>会员总数</td>
                    <td>小区总数</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="bold">
                    <td>{{ $userInfo['userTotal'] }}</td>
                    <td>{{ $userInfo['districTotal'] }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="m-account-info ds-new">
            <div class="title">账户信息</div>
            <table class="tab-dasb">
                <tr>
                    <td width="120" class="tar">管理员帐号：</td>
                    <td class="tal">{{ $accountInfo['account'] }}</td>
                </tr>
                <tr>
                    <td class="tar">管理员组：</td>
                    <td class="tal">{{ $accountInfo['roleName'] }}</td>
                </tr>
                <tr>
                    <td class="tar">最后登陆时间：</td>
                    <td class="tal">{{ date('Y-m-d H:i:s',$accountInfo['loginTime']) }}</td>
                </tr>
                <tr>
                    <td class="tar">最后登录IP/地址：</td>
                    <td class="tal">{{ $accountInfo['loginIp'] }}</td>
                </tr>
                <tr>
                    <td class="tar">登录次数：</td>
                    <td class="tal">{{ $accountInfo['loginCount'] }}</td>
                </tr>
            </table>
        </div>
        <div class="m-system-info ds-new">
            <div class="title">系统信息</div>
            <table class="tab-dasb">
                <tr>
                    <td width="120" class="tar">当前版本：</td>
                    <td class="tal"><b>{{ $sysVersion }}</b></td>
                </tr>
            </table>
        </div>
    </div>
    @yizan_end
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            $('.u-orderct').highcharts({
                title: {
                    text: '订单数据概览',
                    x: -20 //center
                },
                xAxis: {
                    categories: [@foreach($data['time'] as $val)'{{$val}}',@endforeach]
                },
                yAxis: {
                    min:0,
                    title: {
                        text: ''
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [
                    @foreach($data['data'] as $val)
                    {
                        name: '{{$val['name']}}',
                        data: [@foreach($val['val'] as $vo){{$vo}},@endforeach]
                    },
                    @endforeach
                    ]
            });
        });
    </script>
@stop

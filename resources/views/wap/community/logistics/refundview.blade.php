@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav">
    <a class="pull-left" href="#" onclick="$.href('{{u('Logistics/index')}}')">
		<span class="icon iconfont">&#xe600;</span>返回
	</a>
	<a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
		<span class="icon iconfont">&#xe692;</span>
		@foreach($indexnav as $key => $i_nav)			
		@if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
			<span class="y-redc"></span>
		@endif			
		@endforeach
	</a>
	<h1 class="title f16">退款详情</h1>
</header>
@stop

@section('content')
<ul class="x-ltmore f12 c-gray current_icon none">
<link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
@foreach($indexnav as $key => $i_nav)
	<li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')"><i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
		{{$i_nav['name']}}
	</li>
@endforeach  
</ul>
@if($data['status'] == 0)
<nav class="bar bar-tab">
    <div class="y-xddxqbtn">
        <a href="#" id="udb_cr_btn">取消退款</a>
    </div>
</nav>
@endif
@if(in_array($data['status'],[2,6]))
    <nav class="bar bar-tab">
        <div class="y-xddxqbtn">
            <a onclick="$.href('{{u('Logistics/ckservice',['type'=>1,'id'=>$data['orderId']])}}')" href="#" >重新申请</a>
        </div>
    </nav>
@endif
<div class="content c-bgfff">
	<ul class="y-refunddetails">
        @if(in_array($data['status'],[6]))
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">平台核审拒绝</span>
                    <span class="f_999 f12">{{$data['adminDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    <p>{{$data['adminDisposeContent']}}、本次拒绝，如果有疑问请联系平台客服：{{$site_config['wap_service_time']}}</p>
                </div>
            </li>
        @endif
        @if(in_array($data['status'],[5]))
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">平台核审通过</span>
                    <span class="f_999 f12">{{$data['adminDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    @if($refund['stepThree']['status'] == 1 && $data['status'] == 5)
                        <div class="y-stepnumber"></div>
                        <div class="y-titlerow">
                            <span class="y-refundtitle">退款成功</span>
                            <span class="f_999 f12"></span>
                        </div>
                        <div class="f_999 f12">
                            <p>{{$refund['stepThree']['brief']}}</p>
                        </div>
                    @endif
                </div>
            </li>
        @endif

        @if(in_array($data['status'],[4,5,6]) &&  $data['refundType']== 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家确认收货</span>
                    <span class="f_999 f12">{{$data['staffDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    <p>商家已确认收货,等待平台处理</p>
                </div>
            </li>
        @endif

        @if(in_array($data['status'],[3,4,5,6]) &&  $data['refundType']== 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">买家已退货</span>
                    <span class="f_999 f12">{{$data['userDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    <p>物流名称：{{$data['userDisposeName']}}</p>
                    <p>运 单 号：{{$data['userDisposeNumber']}}</p>
                    <p>退货地址：{{$data['sellerAddress']}}</p>
                    @if($data['userDisposeImages'][0])
                    <p>上传凭证</p>
                        <p class="mt5 y-average">
                            @foreach($data['userDisposeImages'] as $img)
                                <img src="{{$img}}" width="24%" class="vat">
                            @endforeach
                        </p>
                    @endif
                </div>
            </li>
        @endif
        @if($data['status']  == 2)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家拒绝</span>
                    <span class="f_999 f12">{{$data['sellerDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    <p>原因：{{$data['sellerDisposeContent']}};</p>
                    @if($data['sellerDisposeExplain'])
                    <p>说明：{{$data['sellerDisposeExplain']}};</p>
                    @endif
                    <p>本次@if($data['refundType'] == 1)退款退货@else仅退款@endif申请被拒绝，您可以再次发起;</p>
                    @if($data['sellerDisposeImages'][0])
                        <p class="mt5 y-average">
                            @foreach($data['sellerDisposeImages'] as $img)
                                <img src="{{$img}}" width="24%" class="vat">
                            @endforeach
                        </p>
                    @endif
                </div>
            </li>
        @endif
        @if(in_array($data['status'],[1,3,4,5,6]))
            @if($data['refundType'] == 1)
                    @if($data['status'] == 1)
                    <li onclick="@if($data['status'] == 1)$.href('{{u('Logistics/logistics',['orderId'=>$data['orderId']])}}') @else return false; @endif">
                        <div class="y-stepnumber"></div>
                        <div class="y-titlerow">
                            <span class="y-refundtitle">点击"填写物流信息"</span>
                            <span class="f_999 f12">{{$data['userDisposeTime']}}</span>
                        </div>
                    </li>
                    @endif
            @else
                    @if(in_array($data['status'],[1,3,4]))
                       <li>
                            <div class="y-stepnumber"></div>
                            <div class="y-titlerow">
                                <span class="y-refundtitle">待平台处理</span>
                            </div>
                           <div class="f_999 f12">
                               <p>请勿相信任何人给您发来的可以退款的链接，以免钱款被骗。</p>
                           </div>
                       </li>
                    @endif
            @endif
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家处理通过</span>
                    <span class="f_999 f12">{{$data['sellerDisposeTime']}}</span>
                </div>
                <div class="c-gray f12">
                    @if($data['refundType'] == 1)
                        <p>退货地址：{{$data['sellerAddress']}}</p>
                        <p>商家同意了本次售后服务申请。请将退货商品包装好，且商品不影响二次销售；请勿发平邮或到付件，商品寄出后，需及时在每笔退款上操作“填写物流信息”，以免影响退款进度</p>
                    @else
                        <p>本次退款申请达成</p>
                    @endif
                </div>
            </li>
        @endif
        @if($data['status']  ==0)
        <li>
            <div class="y-stepnumber"></div>
            <div class="y-titlerow">
                <span class="y-refundtitle">待商家处理</span>
            </div>
            <div class="c-gray f12">
                <p>如商家同意，请按照给出的退货地址退货</p>
                <p>如商家拒绝，您可以再次发起，商家会重新处理。</p>
                <p>请勿相信任何人给您发来的可以退款的链接，以免钱款被骗。</p>
                <p></p>
            </div>
        </li>
        @endif
        <li>
            <div class="y-stepnumber"></div>
            <div class="y-titlerow">
                <span class="y-refundtitle">{{$data['order']['users']['name']}}:发起了申请</span>
                <span class="c-gray f12">{{$data['createTime']}}</span>
            </div>
            <div class="c-gray f12">
                <p>发起了@if($data['refundType'] == 1)退款退货@else仅退款@endif申请，原因:{{$data['content']}}@if($data['refundExplain'])，说明：{{$data['refundExplain']}}@endif</p>
                @if($data['images'][0])
					
                    <p class="mt5 y-average">
                        @foreach($data['images'] as $img)
                            <img src="{{$img}}" width="24%" class="vat">
                        @endforeach
                    </p>
                @endif
            </div>
        </li>
	</ul>
</div>
@stop

@section($js)
<script type="text/javascript">
    $(function(){
		$(document).off("click", ".y-splistcd");
		$(document).on("click", ".y-splistcd", function(){
			if($(".x-ltmore").hasClass("none")){
				$(".x-ltmore").removeClass("none");
			}else{
				$(".x-ltmore").addClass("none");
			}
		});

        $(document).off("click", "#udb_cr_btn");
        $(document).on("click", "#udb_cr_btn", function(){
            $.post('{{u('Logistics/refundDel')}}',{id:"{{$data['id']}}"},function(res){
                if(res.code == "81003"){
                    var url = "{{u('Order/detail',['id'=>$data['orderId']])}}"
                    $.href(url);
                }else{
                    $.toast("取消失败");
                }
            });
        });


        $(document).on("click", ".content", function(){
			$(".x-ltmore").addClass("none");
		});
    });
</script>
@stop
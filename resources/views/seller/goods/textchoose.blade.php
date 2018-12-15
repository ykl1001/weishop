@extends('seller._layouts.base')
@section('content') 
	<div>
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">系统服务须知</span>
					<a href="{{u('Goods/quickchoose',$args)}}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="f-bhtt f14 clearfix" id="u-fwcztj" style="border-top:none;">
					<form id="form-quick" action="{{u('Goods/quickchoose')}}" method="GET" >
					<div class="por ml10 fl">
						<input type="search" class="f-ipt" name="name" value="{{$args['name']}}" placeholder="请输入关键词搜索" style="width:265px;height:30px; padding-right:25px;box-sizing:border-box;">
						<a href="javascript:;" class="f-search search-btn fa fa-search"></a>
					</div>
					<a href="javascript:;" class="f-seebtn search-btn fl ml10">查询</a>
					<label for="" class="ml15 ml10 fl"><label for="" class="ml5"></label></label>
					</form>
				</div>
				<div class="m-quyu1" style="padding-bottom:0;">
					<div class="g-crtserct" style="width:100%;">
						<div class="clearfix pl20 pr20 m-fwzljs" style="height:auto;overflow:hidden;">
							<ul class="m-fwzllst">
								@foreach($list as $item)
								<li class="clearifx goods_item" data-id="{{$item['id']}}">
									<div class="m-zjj fl">
										<p class="clearfix">
											<span class="fl">{{$item['name']}}</span>
											<span class="fr">服务计费：{{$item['price']}}/@if($item['priceType'] == 1) 次 @else 小时 @endif</span>
										</p>
										<p>
											{{$item['brief']}}
										</p>
									</div>
									<p class="fr m-xzct">
										<a href="javascript:;" class="btn f-80btn">选择</a>
									</p>
								</li> 
								@endforeach
							</ul>
						</div>	
						<!-- 分页 -->
						@include("seller._layouts.pager")
						<p class="tc m-addfwanct none">
							<a href="javascript:;" class="btn f-170btn ml20">完成</a>
						</p>
					</div>
				</div>
				<div class="m-quyu1 mt10" style="padding-bottom:0;">
					<div class="g-crtserct" style="width:100%;border-top:1px solid #ccc;">
						<p style="height:1px;"></p>
						<div class="m-fwwzc" style="border-top:0px;">
							<p>
								当平台推荐的服务没有您所从事的，可通过新增服务制定自己个性服务，制定的服务通过总后台工作人员审核(审核1至2个工作日完成)，
								全平台服务人员将可以选择您的个性化服务！！

							</p>
						</div>
						<p class="tc" style="margin:35px 0;">
							<a href="{{u('Goods/create')}}" class="btn f-170btn ml20">新增服务</a>
						</p>									
					</div>	
				</div>
			</div>
		</div>
	</div>
@stop
@section('js')
<script type="text/javascript">
	$(".goods_item").click(function(){
		window.location.href = "{{u('Goods/create')}}?id="+$(this).data('id');
	});
	$(".search-btn").click(function(){
		$("#form-quick").submit();
	});
</script>
@stop

@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.ml12{margin-left: 12px;}
	.p-acttopbtn{padding: 7px 30px;font-size: 16px;color: #666;border: solid #ccc;border-width: 1px 1px 0 1px;background-color: #ccc;}
	.g-szzllst{margin-top: -1px;position: relative;z-index: 0;}
	.bfff{background: #fff;position: relative;z-index: 1;}

	/*特价商品*/
	.w195{width: 195px;}
    .ml25{margin-left: 25px;}
    .mt8{margin-top: 8px;}
    .p-addbtn{padding: 5px 10px;cursor: pointer;}
    .p-sellerlist{width: 600px;margin-top: 5px;cursor: pointer;border: solid #ccc;border-width: 1px 1px 0;}
    .p-sellerlist li:hover{background-color: #eee;}
    .p-sellerlist li{border-bottom: 1px solid #ccc;margin: 0;padding: 5px 10px;}

    .p-sellerlist i.fa{float: right;font-size: 16px;}
    .p-sellerlist i.fa:hover{color: red;}
	
	.f-btn{line-height: 28px;}
	.m-tab table tbody td{padding: 8px;}
    button.zfbtn{cursor: pointer;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<search>
								<row>
									<item name="name" label="商品名称"></item>
									<btn type="search" css="f-btn"></btn>
								</row>
							</search>
					        <btns>
					            <btn label="确认添加" css="btn-green useGoods"></btn>
					            <linkbtn label="返回" url="{{ u('ActivityAdd/index') }}?checkType=special" css="f-btn"></linkbtn>
					        </btns>
							<table checkbox="1">
								<columns>
					                <column code="name" label="商品名称" width="80%" align="left">
					                	{{$list_item['name']}}
					                	@if($list_item['checkedDisabled'] == 1)
					                	<span class="ml5 gray">(已参与)</span>
					                	@endif
					                </column>
					            </columns>
							</table>
					    </yz:list>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		//保存以选择的列表
		$(".useGoods").click(function(){
			var goodsIds = [];
			$.each($("table tr .checker span.checked input[name='key']"), function(k, v){
				goodsIds[k] = $(this).val();
			});
			if(goodsIds.length < 1)
			{
				$.ShowAlert('请至少选择一件商品');
				return false;
			}

			$.post("{{ u('ActivityAdd/saveGoodsIds') }}", {'goodsIds':goodsIds}, function(res){
				window.location.reload();
			});
		});
	});
</script>
@stop

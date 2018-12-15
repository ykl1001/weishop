@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	#listWord{text-align: right;margin: -8px 70px 0 0;}
</style>
@stop
@section('content')
	<div class="">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">其他设置</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:750px;"> 
						@yizan_begin
						<yz:form id="yz_form" action="updaterest">
							<yz:fitem name="brief" label="简介" type="textarea" append="1" attr="maxlength=200">
								<p id="listWord">限制&nbsp;<span>{{mb_strlen($data['brief']) > 0 ? mb_strlen($data['brief']) : 0}}</span>/200&nbsp;字</p>
							</yz:fitem>
							<yz:fitem name="status" label="状态">
								<yz:radio name="status" options="0,1" texts="停业,正常" checked="$data['status']"></yz:radio>
							</yz:fitem>
							<yz:fitem name="sort" label="排序"></yz:fitem>
						</yz:form>		
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js') 
<script type="text/javascript">
	$(function(){
		//字数统计显示
		$("#brief").keyup(function(){
			$("#listWord span").text($(this).val().length);
			if($(this).val().length == 200)
			{
				$("#listWord").css({'color':'red'});
			}
			else
			{
				$("#listWord").css({'color':'#000'});
			}
		});
	});
</script>
@stop

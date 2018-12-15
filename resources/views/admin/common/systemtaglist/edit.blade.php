@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="标签名称" attr="maxlength='20'"></yz:fitem>
			<yz:fitem label="所属标签">
				<yz:select name="pid" options="$tagList" textfield="name" valuefield="id" selected="$data['pid']"></yz:select>
			</yz:fitem>
	        <div class="notTopTag @if($data['pid'] == 0) none @endif">
		        <yz:fitem label="标签分类">
					<yz:select name="systemTagId" options="$tag" textfield="name" valuefield="id" selected="$data['systemTagId']"></yz:select>
		        </yz:fitem>
				<yz:fitem name="img" label="标签图标" type="image" append="1">
					<div><small class='cred pl10 gray'>建议尺寸：512px*512px，支持JPG/PNG格式</small></div>
				</yz:fitem>
			</div>
			<yz:fitem label="排序">
				<input type="text" name="sort" class="u-ipttext" value="{{ $data['sort'] or 100}}"onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
			</yz:fitem>
			<yz:fitem label="状态">
				<yz:radio name="status" options="1,0" texts="开启,关闭" default="1" checked="$data['status']"></yz:radio>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
	$(function(){

		//显示隐藏标签分类和分类图标
		$.notTopTag = function(value)
		{
			if(value == 0)
			{
				//顶级分类
				$(".notTopTag").addClass("none");
			}
			else
			{
				$(".notTopTag").removeClass("none");
			}
		}

		//进入检测
		var tagpid = $("#pid").val();
		$.notTopTag(tagpid);

		//切换检测
		$("#pid").change(function(){
			$.notTopTag($(this).val());
		});


	});
	
</script>
@stop
@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			@yizan_yield("search")
			<search> 
				<row>
					<item name="name" label="商家名"></item>
					<item label="审核状态">
						<yz:select name="isCheck" options="2,1" texts="待审核,拒绝" selected="$search_args['isCheck']"></yz:select>
					</item>
					<btn type="search"></btn>
				</row>
			</search>
			@yizan_stop
			@yizan_yield("btn")
			<btns>
				<linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			@yizan_stop
			<table checkbox="1">
				<columns>
					<column code="id" label="编号" width="40"></column>
					<column label="加盟类型">
						@if($list_item['type'] == 1)
							个人加盟
						@elseif($list_item['type'] == 2)
							商家加盟
						@elseif($list_item['type'] == 3)
							物业公司
						@else
							未知
						@endif
					</column>
					<column label="店铺类型">
						<!-- 物业不显示店铺类型 -->
						@if($list_item['type'] == 3)
							-
						@else
							@if($list_item['storeType'] == 1)
								全国店
							@elseif($list_item['storeType'] == 0)
								周边店
							@endif
						@endif
					</column>
					<column code="name" label="商家名" align="left"></column>
					<column code="contacts" label="联系人">
					{{ $list_item['contacts'] ? $list_item['contacts'] : $list_item['name']}}
					</column>
					<column code="mobile" label="电话" align="left" width="100">
					</column>
					<column code="createTime" label="申请时间">
						{{ yztime($list_item['createTime']) }}
					</column>
					<column code="isCheck" label="状态" width="40">
						@if($list_item['isCheck'] == 1)
						<p>已通过</p>
						@elseif($list_item['isCheck'] == -1)
						<p>拒绝</p>
						@else
						<p>待审核</p>
						@endif
					</column>
					<column code="checkVal" label="操作理由"></column>
					<actions width="60">
						<action type="edit" css="blu"></action>
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop
@section('js')
@stop


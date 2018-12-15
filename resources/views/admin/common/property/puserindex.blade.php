@extends('admin._layouts.base')

@section('css')
<style type="text/css">
	td p{padding: 2px;}
</style>
@stop

<?php
$type = ['业主','租客','业主家属'];
?>


@section('right_content')
	@yizan_begin
	<yz:list>
		<search> 
			@if($seller)
			<row>
				<item label="物业公司">
					{{$seller['name']}}
				</item>
				<item label="小区名称">
					{{$seller['district']['name']}}
				</item>
			</row>
			@endif
			<row>
				<item name="name" label="业主名称"></item>  
				<item name="build" label="楼栋号"></item>
                <item name="roomNum" label="房间号"></item>
                <btn type="search"></btn>
			</row>
		</search>
		<btns>
            <div class="list-btns">
                <a id="export" href="javascript:;$.export(1)" target="_self" class="btn mr5 btn-gray">
                    导出到EXCEL
                </a>
            </div>
            <script>
                $.export = function(page){
                    $.post("{!! urldecode(u('Property/puserexport', ['sellerId'=>$seller['id']] )) !!}",{page:page,json:1},function(result){
                        if(result.code == 0){
                            $.ShowAlert("导完了");
                        }else{
                            window.open("{!! urldecode(u('Property/puserexport', ['sellerId'=>$seller['id']] )) !!}"+"&page="+page);
                            page++;
                            setTimeout(function(){
                                $.export(page);
                            }, 1000);
                        }
                    },'json');
                }
            </script>
		</btns>
		<table>
			<columns>
				<column code="id" label="编号" width="30"></column>
				<column code="name" label="楼栋号" width="50">
					<p>{{ $list_item['build']['name'] }}</p>
				</column>
				<column code="roomNum" label="房间号" width="50">
					<p>{{ $list_item['room']['roomNum'] }}</p>
				</column>
				<column code="name" label="姓名" width="50"></column>
				<column code="mobile" label="电话" width="80"></column>
				<column code="fee" label="物业费(元/月)" width="80">
					<p>{{ $list_item['room']['propertyFee'] }}</p>
				</column>
                <column label="认证身份" width="50">
                    {{$type[$list_item['type']]}}
                </column>
				<column code="accessStatus" label="是否申请门禁" width="80">
					@if($list_item['accessStatus'] == 1)
					<p>是</p>
					@else
					<p>否</p>
					@endif
				</column>
				<actions width="80">
					<action label="门禁" >
						<attrs>
							<url>{{ u('Property/pusercheck',['puserId'=>$list_item['id'], 'sellerId'=>$list_item['seller']['id']]) }}</url>
						</attrs>
					</action>
					<action label="删除" css="red">
						<attrs>
							<click>$.RemoveItem(this, '{!!u('Property/puserdestroy',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']])!!}', '你确定要删除该数据吗？');</click>
						</attrs>
					</action>
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
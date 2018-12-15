@extends('admin._layouts.base')

@section('css')
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="name" label="代理账户"></item>  
					<yz:fitem name="provinceId" label="所在地区">
						<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" new="1" aval="$search_args['areaId']" showtip="1"></yz:region>
					</yz:fitem>
	                <item label="审核状态">
	                    <yz:select name="isCheck" options="0,1,2,3" texts="全部,已拒绝,待审核,已审核" selected="$search_args['isCheck']"></yz:select> 
	                </item>
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			<table checkbox="1"> 
				<columns>
					<column code="id" label="编号" width="40"></column> 
					<column code="name" label="代理账户" ></column>   
					<column code="level" label="代理等级" ></column>   
					<column code="realName" label="联系人" ></column>   
					<column code="mobile" label="电话" ></column>
                    <column label="城市">
                        @if(!in_array($list_item['province']['id'],$zx))
                            {{ $list_item['city']['name'] }}
                        @else
                            {{ $list_item['province']['name'] }}
                        @endif
                    </column>
                    <column label="行政区">
                        @if(!in_array($list_item['province']['id'],$zx))
                            {{ $list_item['area']['name'] }}
                        @else
                            {{ $list_item['city']['name'] }}
                        @endif
                    </column>
					<column code="thirdArea" label="自定义" >
						{{$list_item['thirdArea']}}
					</column>    
					<column label="创建时间" >{{yztime($list_item['createTime'])}}</column>   
					<column code="isCheck" label="状态" width="40">
						@if($list_item['isCheck'] == 1)
						<p>已通过</p>
						@elseif($list_item['isCheck'] == -1)
						<p>拒绝</p>
						@else
						<p>待审核</p>
						@endif
					</column>
					<actions width="60">
						<action label="编辑" type="edit" css="blu"></action>
						<!-- @if($list_item['canDelete'] == 1) -->
						<action label="删除" type="destroy" css="red"></action>
						<!-- @else -->
						<action type="destroy" click="javascript:;" style="color:#ccc;cursor:default"></action>
						<script type="text/javascript">
	                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled');
	                    </script>
	                    <!-- @endif -->
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop

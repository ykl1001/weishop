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
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="添加代理" url="{{ u('Proxy/create') }}" css="btn-green"></linkbtn>
				<linkbtn label="导出到EXCEL" type="export">
					<attrs>
						<url>{{ u('Proxy/export', $search_args ) }}</url>
					</attrs>
				</linkbtn>
				<linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			<table checkbox="1">
				<columns>
					<column code="id" label="编号" width="40"></column>
					<column code="name" label="代理账户" ></column>
					<column code="level" label="代理等级" ></column>
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
					<column code="status" label="状态" type="status" ></column>   
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

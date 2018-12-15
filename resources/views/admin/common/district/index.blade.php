@extends('admin._layouts.base')

@section('css')
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="districtName" label="小区名称"></item>  
					<yz:fitem name="provinceId" label="所在地区">
						<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" new="1" aval="$search_args['areaId']" showtip="1"></yz:region>
					</yz:fitem>
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="添加小区" url="{{ u('District/create') }}" css="btn-green"></linkbtn>
				<linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			<table checkbox="1"> 
				<columns>
					<column code="id" label="编号" width="40"></column> 
					<column code="name" label="小区" align="left"></column>
					<column label="城市" width="200">
                        @if(!in_array($list_item['province']['id'],$zx))
                            {{ $list_item['city']['name'] }}
                        @else
                            {{ $list_item['province']['name'] }}
                        @endif
					</column>
					<column label="行政区" width="200">
                        @if(!in_array($list_item['province']['id'],$zx))
                            {{ $list_item['area']['name'] }}
                        @else
                            {{ $list_item['city']['name'] }}
                        @endif
					</column>
					<column code="status" label="物业进驻状态"  width="60">
						@if($list_item['sellerId'] > 0)
						是
						@else
						否
						@endif
					</column>
					<actions width="100">
						<action label="编辑" type="edit" css="blu"></action>
						@if($list_item['sellerId'] < 1)
						<action label="删除" type="destroy" css="red"></action>
						@else
						<script type="text/javascript">
	                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled');
	                    </script>
						@endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop

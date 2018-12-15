@extends('proxy._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		@if($proxy)
		<div id="name-form-item" class="u-fitem clearfix " style="margin-left: 5px;">
            <span class="f-tt">
                 代理账户:{{$proxy['name']}}
            </span> 
        </div>
		@else
		<search url="index">
			<row>  
				<item name="name" label="代理账户"></item>
                <item name="mobile" label="联系电话"></item>  
				<yz:fitem name="provinceId" label="所在地区">
					<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1" new="1"></yz:region>
				</yz:fitem>
				<btn type="search"></btn>
			</row>
		</search> 
		@endif
		@if($login_proxy['level'] < 3)
		<btns>
			<linkbtn label="添加代理" url="{{ u('Proxy/create') }}" css="btn-green"></linkbtn>
		</btns>
		@endif
		<table >
		<columns>
			<column code="id" label="编号" width="40"></column>
			<column code="name" label="代理账户" align="left" width="80"></column>
			<column code="level" label="级别" align="center" width="50"></column>
            <column code="realName" label="真实姓名" align="center" width="80"></column> 
            <column code="mobile" label="联系电话" width="80"></column>
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
			<column code="status" label="状态" width="40" >@if($list_item['status']) 开启 @else 关闭 @endif</column> 
			<actions width="100">  
				<a href="{{ u('Proxy/edit',['id'=>$list_item['id']]) }}" class=" blu" target="_self">编辑</a>
				@if($list_item['level'] < 3)
				<a href="{{ u('Proxy/index',['id'=>$list_item['id']]) }}" class=" blu" data-pk="1" target="_self">代理列表</a>
				@endif
			</actions> 
		</columns>  
	</table>
	</yz:list>
	@yizan_end
@stop 
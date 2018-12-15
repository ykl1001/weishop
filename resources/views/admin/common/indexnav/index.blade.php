@extends('admin._layouts.base')

@section('css')
<link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="name" label="名称"></item>  
					<yz:fitem name="cityId" label="城市">
			            <yz:select name="cityId" css="type" options="$citys" textfield="name" valuefield="id" selected="$search_args['cityId']"></yz:select>
			        </yz:fitem>
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="添加系统导航" url="{{ u('IndexNav/create') }}" css="btn-green"></linkbtn>
				<linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			<table checkbox="1">
				<columns>
					<column code="id" label="编号" width="40"></column> 
					<column code="name" label="名称" ></column>   
					<php>
					$current_icon = explode(',', $list_item['icon']); 
					$icon_str = '';
					$i = 0;
					foreach($current_icon as $icon){
						$i++;
						if($i == count($current_icon)){
							$icon_str .= ($icon . ";");
						} else {
							$icon_str .= $icon . "; ";
						}
					}
					</php>
					<column label="图标" ><span class="icon iconfont">{{$icon_str}}</span></column>   
					<column label="城市" >{{ $list_item['cityId']?$list_item['city']['name']:'所有城市'}}</column>   
					<column label="链接类型" >{{ Lang::get('api_system.index_link_type.'.$list_item['type']) }}</column> 
					<column code="sort" label="排序" ></column>   
					<column code="status" label="状态" type="status" ></column>   
					<!--column code="isSystem" label="是否系统内置" >{{$list_item['isSystem'] ? '是' : '否'}}</column-->   
					<actions width="60"> 
						<action label="编辑" type="edit" css="blu"></action>  
						<action label="删除" type="destroy" css="red"></action>   
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop

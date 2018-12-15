@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<yz:list>
		<btns>
            <linkbtn type="add" url="create"></linkbtn>
            @if($is_show_adv == 1)
                <linkbtn type="add" label="添加广告" url="create_adv"></linkbtn>
            @endif
			<linkbtn label="删除" type="destroy"></linkbtn>
		</btns>
		<table relmodule="Adv" checkbox="1">
			<columns>
				@yizan_yield('adv_wapmodule')
					<column code="position" label="广告位">
						{{$list_item['position']['name']}}
					</column>
					<column code="name" label="名称"></column>
                    <column code="city" label="城市">
                        @if(!empty($list_item['city'])) {{ $list_item['city']['name'] }} @else 所有城市 @endif
                    </column>
                <column code="image" label="图片" type="image" width="60" iscut="1">
						<php>
							$color = empty($list_item['color']) ? '#ccc' : $list_item['color'];
						</php>
                        @if(!empty($list_item['image']))
                            <a href="{{ $list_item['image'] }}" target="_blank" style="display:block; width:60px; height:60px;border:solid 1px {{ $color }}; background:#ccc;">
                                <img src="{{ formatImage($list_item['image'],60,60,1) }}"/>
                            </a>
                        @endif

					</column>
					<column code="sort" width="50" label="排序"></column>
					<column code="status" width="50" label="状态"  type="status"></column>
					<column code="createTime" label="时间" type="time" width="200"></column>
				@yizan_stop
				<actions width="100">
                    @if($list_item['position']['id'] == 3)
                        <a href="{{u('UserAppAdv/edit_adv',['id'=>$list_item['id']])}}" class=" blu" data-pk="65" target="_self">编辑</a>
                    @else
                        <action type="edit" css="blu"></action>
                    @endif
					<action type="destroy" css="red"></action>
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop
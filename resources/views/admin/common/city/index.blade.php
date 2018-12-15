@extends('admin._layouts.base')
@section('css')
    <style>
        .m-tab table tbody td{}
        .y-tcmain{padding:1.5rem;}
    </style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<yz:fitem name="provinceId" label="所在地区">
						<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" new="1" aval="$search_args['areaId']" showtip="1"></yz:region>
					</yz:fitem>
					<item name="cityName" label="城市名称"></item>
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="添加城市" url="{{ u('City/create') }}"></linkbtn>
				<linkbtn label="删除" type="destroy"></linkbtn>
                <linkbtn label="一键开通" click="111"></linkbtn>
            </btns>
			<table checkbox="1">
				<columns>
					<column code="id" label="编号"  width="30" ></column>
					<column code="name" label="城市名称" width="180">
                        @if($list_item['level'] == 1)
                            <p>{{$list_item['name']}}</p>
                        @elseif(in_array($list_item['pid'],$zx))
                            <p>&nbsp;&nbsp;{{$list_item['cityname']}}|{{$list_item['name']}}</p>
                        @elseif($list_item['level'] == 2)
                            <p>&nbsp;&nbsp;{{$list_item['provincename']}}|{{$list_item['name']}}</p>
                        @elseif($list_item['level'] == 3)
                            <p>&nbsp;&nbsp;{{$list_item['provincename']}}|{{$list_item['cityname']}}|{{$list_item['name']}}</p>
                        @endif
                    </column>
					<column code="firstChar" label="城市首字母"></column>
					<column code="sort" label="排序"></column>
					<column label="默认城市" width="80">
						@if($list_item['isDefault']==true)
						是
						@else
                            @if($list_item['level'] < 3)
                                @if(!in_array($list_item['pid'],$zx))
                                    @if($list_item['level'] ==1)
                                        @if(in_array($list_item['id'],$zx))
                                            <a href="javascript:;" class=" blu" onclick="isdefault({{$list_item['id']}})" data-pk="{{$list_item['id']}}" target="_self">设为默认</a>
                                        @endif
                                    @else
                                        <a href="javascript:;" class=" blu" onclick="isdefault({{$list_item['id']}})" data-pk="{{$list_item['id']}}" target="_self">设为默认</a>
                                    @endif
                                @endif
                            @else
                            @endif
						@endif
					</column>
					<actions width="30">
						<!-- @if($list_item['canDelete'] == 1) -->
						<action type="destroy" css="red"></action>
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
<script type="text/javascript">
	function isdefault (id) {
		$.post("{{ u('City/isdefault') }}",{'id':id},function(result){
			window.location.reload();
		});
	}

    function openCity() {
        var dialog = $.zydialogs.open("<div class='y-tcmain'>您确定开通所有城市么？</div>", {
            boxid:'SET_GROUP_WEEBOX',
            width:300,
            title:'提醒',
            showClose:true,
            showButton:true,
            showOk:true,
            showCancel:true,
            okBtnName:"确定",
            cancelBtnName: '取消',
            contentType:'content',
            onOk: function(){
                dialog.setLoading();
                $.post("{{ u('City/open')  }}",function(result){
                    dialog.setLoading(false);
                    if(result.status == true){
                        window.location.reload();
                    }else{
                        $.ShowAlert(result.msg);
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                },'json');
            },
            onCancel:function(){
                $.zydialogs.close("SET_GROUP_WEEBOX");
            }
        });
    }


</script>

@stop

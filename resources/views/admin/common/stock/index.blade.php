@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    @yizan_begin
        <yz:list>
            <btns>
                <linkbtn label="添加存库库" type="add" css="btn-green"></linkbtn>
                <btn label="批量删除"  type="destroy" css="destroyStock"></btn>
            </btns>
			<table relmodule="Stock" checkbox="1">
				<columns>
                    <column code="id" label="编号" iscut="1" width="80"></column>
                    <column code="name" label="活动名称" iscut="1" width="180" ></column>
                    <column label="属性"  iscut="1">
                       {{  implode(",", $list_item['stock'])  }}
                    </column>
                    <column label="创建时间" code="createTime" type="time" iscut="1"></column>
					<actions width="100">
                        @if($list_item['checkedDisabled'] == 0)
                            <action type="edit" css="blu"></action>
                            <action type="destroy" css="red"></action>
                        @else
                            <a href="javascript:;" class="gray" onclick="$.ShowAlert('已有商品数据禁止编辑')" data-pk="9" target="_self">编辑</a>
                            <a href="javascript:;" class="gray" onclick="$.ShowAlert('已有商品数据禁止删除')" data-pk="9" target="_self">删除</a>
                       @endif
                    </actions>
                </columns>
			</table>
        </yz:list>
    @yizan_end
@stop

@section('js')
    <script>
        $(document).on('click','.destroyStock',function(event){
            $.ShowAlert("正在删除...");
            var ids= [];
            $.each($("Input[name=key]"),function(){
                ids.push($(this).val());
            });
            var data = {
                    id :ids
            }
            $.post("{{ u('Stock/destroy') }}",data,function(res){
                $.ShowAlert(res.msg);
                if(res.status == true){
                    location.reload(true);
                }
            })
        });
    </script>
@stop
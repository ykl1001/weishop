@extends('admin._layouts.base')
@section('css')
    <style type="text/css">
        ._green{color: green;}
        ._red{color: red;}
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search>
            <row>
                <item name="name" label="服务名称"></item>
                <item name="sellerName" label="服务人员"></item>
                <item label="分类">
                    <select name="cateId" class="sle">
                        <option value="0">全部</option>
                        @foreach($cate as $cate)
                            <option value="{{ $cate['id'] }}"  @if((int)Input::get('cateId') == $cate['id']) selected @endif>{{ $cate['name'] }}</option>
                        @endforeach
                    </select>
                </item>
                <btn type="search"></btn>
            </row>
        </search>
        <btns>
            <linkbtn label="添加服务" css="btn-green">
                <attrs>
                    <url>{{ u('OneselfService/create') }}</url>
                </attrs>
            </linkbtn>
            <linkbtn label="删除" type="destroy" click="btnDestroy()"></linkbtn>
            <!--<linkbtn label="导出到Excel" type="export" url="{{ u('SellerGoods/export?'.$excel) }}"></linkbtn>-->
        </btns>
        <table relmodule="goods" checkbox="1">
            <columns>
                <column code="id" label="编号" width="40">
                    <p>{{ $list_item['id'] }}</p>
                </column>
                <column code="name" label="名称" align="left">
                    <p>{{ $list_item['name'] }}</p>
                </column>
                <column label="商品标签" align="left" width="15%">
                    <p>{{$list_item['systemTagListPid']['name'] or '无'}}|{{$list_item['systemTagListId']['name'] or '无'}}</p>
                </column>
                <column code="cate" label="分类" width="10%">
                    {{ $list_item['cate']['name']}}
                </column>
                <column code="price" label="价格/时长" align="left" width="100">
                    <p>￥{{ $list_item['price'] }} / {{ (int)$list_item['duration'] }}@if((int)$list_item['unit'] == 0)分钟@else小时@endif</p>
                </column>
                <column code="sort" label="排序" width="40"></column>
                <column code="status" type="status" label="上架\下架服务" width="100"></column>
                <actions width="90">
                    <action css="blu" label="编辑">
                        <attrs>
                            <url>{{ u('OneselfService/edit',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId']]) }}</url>
                        </attrs>
                    </action>
                    <!-- <action type="edit" css="blu"></action> -->
                    <action type="destroy" css="red">
                        <attrs>
                            <url>{{ u('OneselfService/destroy',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId'], 'type'=>$list_item['type']]) }}</url>
                        </attrs>
                    </action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
    function btnDestroy() {
        var id = new Array();
        var sellerId = "{{ $list[0]['sellerId'] ?  $list[0]['sellerId'] : 0}}";
        $("div.checker span.checked").each(function(k, v){
            id[k] = $(this).find("input[name='key']").val();
        });

        if(id.length < 1)
        {
            $.ShowAlert('请选择要删除的项');
            return false;
        }
        console.log(id);
        $.ShowConfirm('确认删除吗？', function(){
            $.post("{{ u('OneselfService/destroy') }}", {'id':id, 'sellerId':sellerId, 'type':2}, function(res){
                if(res.status)
                {
                    window.location.reload();
                }
            });
        });        
    }
</script>
@stop

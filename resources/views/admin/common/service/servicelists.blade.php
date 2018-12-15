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
                <input type="hidden" name="sellerId" value="{{$search_args['sellerId']}}">
                <btn type="search"></btn>
            </row>
        </search>
        <btns>
            <linkbtn label="添加服务" css="btn-green">
                <attrs>
                    <url>{{ u('service/createService', ['sellerId'=>$args['sellerId']]) }}</url>
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
                <column code="seller" label="所属商家" width="100">
                    <p>{{ $list_item['seller']['name'] }}</p>
                </column>
                <!-- <column code="image" label="图片" type="image" width="60" iscut="1"></column> -->
                <column label="商品标签" align="left">
                    <p>{{$list_item['systemTagListPid']['name'] or '无'}}|{{$list_item['systemTagListId']['name'] or '无'}}</p>
                </column>
                <column code="name" label="服务信息" align="left">
                    <p>{{ $list_item['name'] }}</p>
                </column>
                <column code="cate" label="分类">
                    {{ $list_item['cate']['name']}}
                </column>
                <column code="price" label="价格/时长" align="left" width="100">
                    <p>￥{{ $list_item['price'] }} /
                        @if((int)$list_item['unit'] == 2)
                            次
                        @else{{ (int)$list_item['duration'] }}
                        @if((int)$list_item['unit'] == 0)
                            分钟
                        @else
                            小时
                        @endif
                        @endif
                    </p>
                </column>
                <column label="上下架" width="40">
                    <!-- @if( $list_item['saleStatus'] == 0 ) -->
                    <i class="fa fa-arrow-down _red" title="下架服务"></i>
                    <!-- @else if( $list_item['seleStatus'] == 1 ) -->
                    <i class="fa fa-arrow-up _green" title="正常服务"></i>
                    <!-- @endif -->
                </column>
                <column code="status" type="status" label="状态" width="30"></column>
                <actions width="90">
                    <action css="blu" label="编辑">
                        <attrs>
                            <url>{{ u('Service/serviceEdit',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId']]) }}</url>
                        </attrs>
                    </action>
                    <!-- <action type="edit" css="blu"></action> -->
                    <action type="destroy" css="red">
                        <attrs>
                            <url>{{ u('Service/goodsDestroy',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId'], 'type'=>$list_item['type']]) }}</url>
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
            $.ShowConfirm('确认删除吗？', function(){
                $.post("{{ u('Service/goodsDestroy') }}", {'id':id, 'sellerId':sellerId, 'type':2}, function(res){
                    if(res.status)
                    {
                        window.location.reload();
                    }
                });
            });
        }
    </script>
@stop

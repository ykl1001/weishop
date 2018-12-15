@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        @yizan_yield("search")
        <search>
            <row>
                <input type="hidden" name="sellerId" value="{{$args['sellerId']}}" />
                <item name="name" label="商品名称"></item>
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
            <linkbtn label="添加商品" css="btn-green">
                <attrs>
                    <url>{{ u('OneselfGoods/create') }}</url>
                </attrs>
            </linkbtn>
            <linkbtn label="选择通用商品库" css="btn-gray" click="$.show_tag()"></linkbtn>
            <linkbtn label="删除" type="destroy" click="btnDestroy()"></linkbtn>
        </btns>
        @yizan_stop
        <table relmodule="SystemGoods" checkbox="1">
            <columns>
                <column code="id" label="编号" width="40">
                    <p>{{ $list_item['id'] }}</p>
                </column>
                <column code="name" label="名称" align="left">
                    <p>{{ $list_item['name'] }}</p>
                </column>
                <column label="商品标签" align="left"  width="15%">
                    <p>{{$list_item['systemTagListPid']['name'] or '无'}}|{{$list_item['systemTagListId']['name'] or '无'}}</p>
                </column>
                <column code="cate" label="分类"  width="10%">
                    {{ $list_item['cate']['name']}}
                </column>
                <column code="price" label="价格" align="left" width="50">
                    <p>￥{{ $list_item['price'] }}</p>
                </column>
                <column code="sort" label="排序" width="40"></column>
                <column code="status" type="status" label="状态" width="40"></column>
                <actions width="60">
                    <action css="blu" label="编辑">
                        <attrs>
                            <url>{{ u('OneselfGoods/edit',['id'=>$list_item['id']]) }}</url>
                        </attrs>
                    </action>
                    <!-- <action type="edit" css="blu"></action> -->
                    <action type="destroy" css="red">
                        <attrs>
                            <url>{{ u('OneselfGoods/destroy',['id'=>$list_item['id'], 'type'=>$list_item['type']]) }}</url>
                        </attrs>
                    </action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
    <script type="text/tpl" id="SellerGoodsTag">
        <div id="-form-item" class="u-fitem " style="padding:10px;">
            <div class="f-boxr">
                一级分类： <select name="systemTagListPid" class="sle" id="systemTagListPid" style="width:180px">
                    @foreach($systemTagListPid as $val)
                        <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                    @endforeach
        </select>
        <br>
        二级分类： <select name="systemTagListId" class="sle" id="systemTagListId"  style="width:180px">
                <option value="0">请选择</option>
        </select>
</div>
</div>
</script>
    <script type="text/javascript">
        $.show_tag = function(){
            var dialog = $.zydialogs.open($("#SellerGoodsTag").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:'请选择分类',
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '进入商品库',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    var  systemTagListPid = $("select[name=systemTagListPid]  option:selected").val();
                    var  systemTagListId = $("select[name=systemTagListId]  option:selected").val();
                    if(systemTagListPid <= 0){
                        $.ShowAlert("请选择一级分类");
                        return false;
                    }
                    if(systemTagListId <= 0){
                        $.ShowAlert("请选择二级分类");
                        return false;
                    }
                    if(systemTagListPid > 0&& systemTagListId > 0){
                        var  url = "{{u('OneselfGoods/systemGoods')}}?systemTagListPid="+systemTagListPid+"&systemTagListId="+systemTagListId;
                        window.location.href = url;
                    }
                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
            //标签
            $("#systemTagListPid").change(function(){
                var tagId = $(this).val();
                if(tagId == 0)
                {
                    var html = '<option value=0>请选择</option>';
                    $("#systemTagListId").html(html);
                }
                else
                {
                    $.post("{{ u('SystemTagList/secondLevel') }}", {'pid': tagId}, function(res){

                        if(res!='')
                        {
                            var html = '<option value=0>请选择</option>';
                            $.each(res, function(k,v){
                                html += "<option value='"+v.id+"'>"+v.name+"</option>";
                            });
                            $("#systemTagListId").html(html).removeClass('none');
                        }
                        else
                        {
                            var html = '<option value=0>请选择</option>';
                            $("#systemTagListId").html(html);
                        }

                    });
                }
            });
        }

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
                $.post("{{ u('OneselfGoods/destroy') }}", {'id':id, 'sellerId':sellerId, 'type':1}, function(res){
                    if(res.status)
                    {
                        window.location.reload();
                    }
                });
            });        
        }
    </script>
@stop
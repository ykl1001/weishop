@extends('admin._layouts.base')

@section('right_content')
    <div>
        <div class="m-zjgltbg">
            <div class="">
                <!-- 服务表格 -->
                <div class="m-tab m-smfw-ser">
                    @yizan_begin
                    <yz:list>
                        @yizan_yield("search")
                        <search method="get">
                            <row>
                                <item name="name" label="商品名称"></item>
                                <btn type="search"></btn>
                            </row>
                        </search>
                        @yizan_stop
                        <btns>
                            <linkbtn label="添加商品" url="{{ u('SystemGoods/create') }}" css="btn-gray"></linkbtn>
                            <linkbtn label="删除" type="destroy"></linkbtn>
                        </btns>
                        <table css="goodstable" relmodule="GoodsSeller" checkbox="1">
                            <columns>
                                <column code="id" label="ID" width="50"></column>
                                <column label="图片"  width="200" align="center">
                                    <a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img ">
                                        <img src="{{formatImage($list_item['image'],100)}}" alt="">
                                    </a>
                                </column>
                                <column code="name" label="名称" width=""></column>
                                <column code="price" label="价格" width="150"></column>
                                <column code="status" label="状态" width="80">
                                    <!-- @if($list_item['status']==1) -->
                                    正常使用
                                    <!-- @else -->
                                    <span style="color:red">禁止使用</span>
                                    <script type="text/javascript">
                                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").removeAttr('disabled');  //移除status导致的checkbox不能勾选
                                    </script>
                                    <!-- @endif -->
                                </column>
                                <actions width="100">
                                    <action label="编辑" type="edit"  css="blu"></action>
                                    @if($list_item['goods'] == "")
                                    <action label="删除" type="destroy"  css="red"></action>
                                    @else
                                    <script type="text/javascript">
                                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled'); //添加禁用
                                    </script>
                                    @endif
                                </actions>
                            </columns>
                        </table>
                    </yz:list>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

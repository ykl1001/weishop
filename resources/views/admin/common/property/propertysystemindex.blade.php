@extends('admin._layouts.base')

@section('right_content')
    @yizan_begin
    <yz:list>
        <btns>
            <btns>
                <linkbtn label="添加物业配置">
                    <attrs>
                        <url>{{ u('Property/propertysystemcreate',['sellerId'=>$sellerId]) }}</url>
                    </attrs>
                </linkbtn>
            </btns>
        </btns>
        <table css="goodstable" relmodule="PropertySystem">
            <columns>
                <column code="id" label="编号" align="center"></column>

                <column code="name" label="物业配置名称" align="center"></column>
                <column code="status" label="状态" width="50">
                    @if($list_item['status'] == 1)
                        <i title="停用" class="fa fa-check text-success table-status table-status1" status="0" field="status"> </i>
                    @else
                        <i title="启用" class="fa table-status fa-lock table-status0" status="1" field="status"> </i>
                    @endif
                </column>

                <!-- <column code="content" label="公告内容" align="left"></column>   -->
                <column code="createTime" label="发布日期" align="center">
                    {{ yztime($list_item['createTime']) }}
                </column>

                <column code="sort" label="排序" width="50"></column>
                <actions>
                    <action label="编辑" >
                        <attrs>
                            <url>{{ u('Property/propertysystemedit',['sellerId'=>$list_item['sellerId'], 'id'=>$list_item['id']]) }}</url>
                        </attrs>
                    </action>
                    <action label="删除" css="red">
                        <attrs>
                            <click>$.RemoveItem(this, '{!!u('Property/propertysystemdestroy',['sellerId'=>$list_item['sellerId'], 'id'=>$list_item['id']])!!}', '你确定要删除该数据吗？');</click>
                        </attrs>
                    </action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop
@section('js')
@stop

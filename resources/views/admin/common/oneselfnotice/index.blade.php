@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <btns>
            <linkbtn label="添加公告" url="{{ u('OneselfNotice/create') }}" css="btn-gray"></linkbtn>
            <linkbtn label="删除" type="destroy"></linkbtn>
        </btns>
        <table pager="no" css="goodstable" relmodule="Article" checkbox="1">
            <columns>
                <column code="title" label="公告标题" align="center"></column>
                <!-- <column code="content" label="公告内容" align="left"></column>   -->
                <column code="createTime" label="发布日期" align="center">
                    {{ yztime($list_item['createTime']) }}
                </column>
                <actions>
                    <action type="edit" css="blu"></action>
                    <action type="destroy" css="red"></action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop
@section('js')
@stop

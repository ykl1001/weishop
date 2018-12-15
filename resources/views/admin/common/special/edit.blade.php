@extends('admin._layouts.base')
@section('css')
    <style type="text/css">

    </style>
@stop

<?php
$type = [
        '1'=>'满减',
        '2'=>'满免',
        '3'=>'免运费',
        '4'=>'折扣商品',
];

?>
@section('right_content')

    @yizan_begin
    <yz:form id="yz_form" action="save">
            <yz:fitem label="专题名称">
                <input type="text" class="u-ipttext" name="name" id="name" value="{{$data['name']}}" maxlength="20">
                {{--<span class="ts">限20个字符内</span>--}}
            </yz:fitem>
            <yz:fitem name="image" label="专题banner" val="{{$data['image']}}" type="image" ></yz:fitem>
        <div id="seller-cate-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                     主题类型:
                </span>
            <div class="f-boxr">
                 &nbsp; &nbsp;   {{$type[$data['type']]}}

                {{--<select id="type" name="type" style="min-width:234px;width:auto" class="sle">--}}
                    {{--@foreach($type as $item)--}}
                            {{--<option data-type={{$item['id']}} value="{{$item['id']}}" @if($data['type'] == $item['id'])selected="selected"@endif>{{$item['name']}}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
                <span class="ts ts1"></span>
            </div>
        </div>
            <yz:fitem name="content" label="专题介绍">
                <yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor>
            </yz:fitem>

        <yz:fitem name="status" label="专题状态">
            <yz:radio name="status" options="0,1" texts="否,是" checked="$data['status']"></yz:radio>
        </yz:fitem>


    </yz:form>
    @yizan_end
@stop
@section('js')

@stop

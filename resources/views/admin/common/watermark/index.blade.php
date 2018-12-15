@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 165px;}
	.ts{color: #999;margin-left: 5px;}
</style>

@stop
@section('right_content')
	@yizan_begin
	    <yz:form id="yz_form" action="save">
            @foreach($list as $key => $value)
                <php>
                    $code = $value['code'];
                    $data[$code] = $value['val'];
                    $showType = $value['showType'];
                    $val = trim($value['val']);
                    $name = $value['name'];
                </php>
                @if( $value['showType'] == 'image' )
                    @if( $code === 'watermark_logo' )
                    <yz:fitem name="watermark_logo" label="{{$name}}" val="{{$val}}" type="image" ></yz:fitem>
                    @endif
                @else
                    <yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" append="1">
                        <span class="ts">{{$value['tooltip']}}</span>%
                    </yz:fitem>
                @endif
            @endforeach
        </yz:form>
	@yizan_end
@stop

@section('js')

@stop

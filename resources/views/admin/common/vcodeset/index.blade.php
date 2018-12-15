@extends('admin._layouts.base')

@section('css')
<style type="text/css">
    .ml100{margin-left:100px; }
    .vodeimg{width: 300px;}
    .vat{vertical-align: top;}
</style>
@stop

@section('right_content')
    @yizan_begin
        <yz:form id="yz_form" action="save"> 
            <div id="watermark_logo-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                    默认验证码:
                </span>
                <div class="f-boxr">
                    <input type="radio" name="vcodeType" value="0" @if($data['val']==0) checked="true" @endif>
                    <span class="vat">
                        <img src="{{ asset('images/vcode1.png') }}" class="vodeimg" alt="">
                    </span>
                </div>
            </div>
            <div id="watermark_logo-form-item" class="u-fitem clearfix">
                <span class="f-tt">
                     互动验证码:
                </span>
                <div class="f-boxr">
                    <input type="radio" name="vcodeType" value="1" @if($data['val']==1) checked="true" @endif>
                    <span class="vat">
                        <img src="{{ asset('images/vcode2.png') }}" class="vodeimg" alt="">
                        <img src="{{ asset('images/vcode3.png') }}" class="vodeimg" alt="">
                        <img src="{{ asset('images/vcode4.png') }}" class="vodeimg" alt="">
                    </span>
                </div>
            </div>
        </yz:form>
    @yizan_end
@stop   
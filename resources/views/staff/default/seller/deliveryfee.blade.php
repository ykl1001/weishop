@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left save" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" onclick="save()">保存</a>
        <h1 class="title">{{$title}}</h1>
        <script type="text/javascript">
            function save(){
                var data = {};
                data.deliveryFee = $("input[name=deliveryFee]").val();
                data.isAvoidFee = $("input[name=isAvoidFee]").prop("checked")?1:0;
                data.avoidFee = $("input[name=avoidFee]").val();
                $.showIndicator();
                $.post('{{u('Seller/savedeliveryfee')}}',data,function(){
                    JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)
                },"json");
                $.hideIndicator();
            }
        </script>
    </header>
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="blank050"></div>
    <form action="" class="dataform">
        <div class="fwfw">
            <span>配送费：</span>
            <input type="text" id = "save_info_name" name="deliveryFee"  value="{{$seller['deliveryFee']}}" style="border-bottom: 1px solid #aaa;" data-type="deliveryFee"  placeholder="输入配送费" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" />
        </div>
        <div class="fwfw"><span>设置满免：</span>
            <input type="checkbox" class="uniform " name="isAvoidFee" style="width: 3em;height:1.5em;" @if($seller['isAvoidFee']) checked @endif onclick="if($(this).prop('checked')) $('.manmian').show(); else $('.manmian').hide();" />
            <br/>
            <br/>
            <div class="manmian" style="@if(!$seller['isAvoidFee'])display:none;@endif">
                满<input type="text" name="avoidFee" style="width: 5em;border-bottom: 1px solid #aaa;" class="u-ipttext ml5 mr5 p-avoidFee p-disabled"  placeholder="输入金额" value="{{$seller['avoidFee']}}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" />元免配送费
            </div>
        </div>
    </form>
@stop

@section('js')

@append
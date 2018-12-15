@extends('staff.default._layouts.base')

@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/index') }}','#seller_index_view',2)"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">订单验证</h1>
    </header>
@stop

@section('contentcss')admin-order-bmanage infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop

@section('content')
    <div class="content-block-title y-conttitle" style="margin-top:3rem;">请先验证消费码，再接待</div>
    <div class="y-ddyz">
        <input type="text" id="code">
        <p><a class="button button-fill button-danger" onclick="$.checkCode()">确定</a></p>
    </div>
@stop

@section($js)
<script type="text/javascript">
    $(function(){
        $.checkCode = function() {
            var code = $.trim( $("#code").val() );
            var data = new Object();
            data.code = code;
            if(data.code == ''){
                $.alert("请填写正确的消费码");
                return false;
            }

            $.post("{{ u('Seller/checkCode') }}",data, function(res){
                if(res.code == 0){
                    JumpURL("{{ u('Seller/orderAuthCode') }}?id="+res.data.id,'#seller_orderAuthCode_view',2);
                }else{
                    $.alert(res.msg);
                }
            });
        }
    });
</script>

@stop
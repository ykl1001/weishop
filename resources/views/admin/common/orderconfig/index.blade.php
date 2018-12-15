@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 180px;}
	.ts{color: #999;margin-left: 5px;}
	.p-title{background-color: #ccc;padding: 5px 0px;text-align: center;width: 419px;margin-bottom: 10px;}
</style>
@stop
@section('right_content')
	@yizan_begin
	<yz:form id="yz_form" action="save">
		@foreach($list as $key => $value)
			<php>
				$code = $value['code']; 
				$data[$code] = $value['val'];
				$val = trim($value['val']); 
				$name = $value['name'];
				$default_val = explode(',', $value['defaultVals']);
				$default_names = explode(',', $value['defaultNames']);
			</php>
			<!-- @if( $value['showType'] == 'image' ) -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" type="image"></yz:fitem>
			<!-- @elseif( $value['showType'] == 'textarea' ) -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" type="textarea"></yz:fitem>
			<!-- @elseif( $value['showType'] == 'select' ) -->
			<yz:fitem label="{{$name}}" name="{!! $code !!}">
				<yz:select name="{!! $code !!}" options="$default_val" texts="$default_names" selected="$val"></yz:select>
			</yz:fitem>
            <!-- @elseif( $value['showType'] == 'radio' ) -->
            <yz:fitem label="{{$name}}" name="{!! $code !!}">
                <yz:radio name="{!! $code !!}" options="$default_val" texts="$default_names" checked="$val"></yz:radio>
            </yz:fitem>
			<!-- @else -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" append="1">
				<span class="ts">{{$value['tooltip']}}</span>
			</yz:fitem>
			<!-- @endif -->
		@endforeach
		<div style="color:red;margin-left:185px">
			注:提交之前请仔细确认参数的单位！
		</div>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
	var actionurl = $("#yz_form").attr('action');
	$(function(){
		$("#system_order_pass").attr("onKeyUp", "amounts(this)").attr("onBlur", "overFormats(this)");
		$("#system_buyer_order_confirm").attr("onKeyUp", "amounts(this)").attr("onBlur", "overFormats(this)");
		$("#system_order_pass_all").attr("onKeyUp", "amounts(this)").attr("onBlur", "overFormats(this)");
		$("#system_buyer_order_confirm_all").attr("onKeyUp", "amounts(this)").attr("onBlur", "overFormats(this)");
		var staff_deduct_type = $("#staff_deduct_type").val();
		if (staff_deduct_type == 2) {
			$("#staff_deduct_value").next().text('%');
		} else {
			$("#staff_deduct_value").next().text('元');
		}

		$('#staff_deduct_type').change(function(){
			var staff_deduct_type = $(this).val();
			if (staff_deduct_type == 2) {
				$("#staff_deduct_value").next().text('%');
			} else {
				$("#staff_deduct_value").next().text('元');
			}
		});

		var html1 = "<p class='p-title'>周边店订单配置</p>";
		var html2 = "<p class='p-title'>全国店订单配置</p>";
		$("#is_refund_balance-form-item").after(html1);
		$("#system_buyer_order_confirm-form-item").after(html2);


		$('#yz_form').submit(function(){
            var system_order_pass = $("#system_order_pass").val();
            var system_buyer_order_confirm = $("#system_buyer_order_confirm").val();
            var system_order_pass_all = $("#system_order_pass_all").val();
            var system_buyer_order_confirm_all = $("#system_buyer_order_confirm_all").val();
            
            $("#yz_form").attr('action', '');

            if(system_order_pass == 0 || system_order_pass.trim() == '') {
                $.ShowAlert("订单过期时间【周边店】错误");
                return false;
            }
            if(system_buyer_order_confirm == 0 || system_buyer_order_confirm.trim() == '') {
                $.ShowAlert("确认订单完成时间【周边店】错误");
                return false;
            }
            if(system_order_pass_all == 0 || system_order_pass_all.trim() == '') {
                $.ShowAlert("订单过期时间【全国店】错误");
                return false;
            }
            if(system_buyer_order_confirm_all == 0 || system_buyer_order_confirm_all.trim() == '') {
                $.ShowAlert("确认订单完成时间【全国店】错误");
                return false;
            }
            
            $("#yz_form").removeClass('sumit-loading');
            $("#yz_form").attr('action', actionurl);
        });
	})

	function amounts(th){
        var regStrs = [
            ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
            ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
            ['\\.(\\d?)+', '$1'], //禁止录入两个以上的点
            ['^(\\d+\\.\\d{0}).+', '$1'] //禁止录入小数点后两位以上
        ];
        for(i=0; i<regStrs.length; i++){
            var reg = new RegExp(regStrs[i][0]);
            th.value = th.value.replace(reg, regStrs[i][1]);
        }
    }

    function overFormats(th){
        var v = th.value;
        if(v === ''){
            v = '0';
        }else if(v === '0.'){
            v = '0';
        }else if(/^0+\d+\.?\d*.*$/.test(v)){
            v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
            v = inp.getRightPriceFormat(v).val;
        }else if(/^0\.\d$/.test(v)){
             v = v + '0';
        }else if(!/^\d+\.\d{2}$/.test(v)){
            if(/^\d+\.\d{2}.+/.test(v)){
                v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
            }else if(/^\d+\.$/.test(v)){
                v = v.substring(0, v.length-1);
            }else if(/^\d+\.\d$/.test(v)){
                v = v + '0';
            }else if(/^[^\d]+\d+\.?\d*$/.test(v)){
                v = v.replace(/^[^\d]+(\d+\.?\d*)$/, '$1');
            }else if(/\d+/.test(v)){
                v = v.replace(/^[^\d]*(\d+\.?\d*).*$/, '$1');
                ty = false;
            }else if(/^0+\d+\.?\d*$/.test(v)){
                v = v.replace(/^0+(\d+\.?\d*)$/, '$1');
                ty = false;
            }else{
                v = '0';
            }
        }
        th.value = v;
    }
</script>
@stop

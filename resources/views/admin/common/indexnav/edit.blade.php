@extends('admin._layouts.base')
@section('css') 
<link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="名称" ></yz:fitem>    
			<yz:fitem name="cityId" label="城市">
	            <yz:select name="cityId" css="type" options="$citys" textfield="name" valuefield="id" selected="$data['cityId']"></yz:select>
	        </yz:fitem>
			<div id="icon-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 图标代码:
	            </span>
	            <php>
	            $current_icon = explode(',', $data['icon']);
	            </php>
	            <div class="f-boxr" style="width: 600px;"> 
              		@foreach($icon_lists as $icon) 
    				<span class="icon icon_item iconfont @if(in_array($icon, $current_icon)) red @endif"  data-val="{{$icon}}" style="font-size: 30px !important;cursor: pointer;">{{$icon.';'}}</span> 
					@endforeach 
					<input id="icon" type="hidden" name="icon" value="{{$data['icon']}}" />
					<br>
					<span>请点击选择2个图标</span>
	            </div>
	        </div>
			<yz:fitem label="链接地址"> 
				<yz:select name="type" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="加盟首页,商品分类,平台自营商城,购物车,生活圈,物业,个人中心,签到,积分商城,系统消息,赚钱,订单" selected="$data['type']"></yz:select>
			</yz:fitem>
			<yz:fitem name="sort" label="排序"></yz:fitem>  
			<!--yz:fitem label="系统内置">
				<php> $isSystem = isset($data['isSystem']) ? $data['isSystem'] : 0 </php>
				<yz:radio name="isSystem" options="1,0" texts="开启,关闭" checked="$isSystem"></yz:radio>
			</yz:fitem -->
            {{--<div id="isIndex-form-item" class="u-fitem clearfix ">--}}
		            {{--<span class="f-tt">--}}
		                 {{--是否是首页:--}}
		            {{--</span>--}}
                {{--<div class="f-boxr">--}}
                    {{--<label>--}}
                        {{--<div class="radio"><span @if($data['isIndex'] == 1) class="checked" @endif ><input type="radio" class="uniform " name="isIndex" value="1" @if($data['isIndex'] == 1) checked="" @endif ></span></div>--}}
                        {{--<span>是</span>--}}
                    {{--</label>--}}
                    {{--<span>&nbsp;&nbsp;</span>--}}
                    {{--<label>--}}
                        {{--<div class="radio"><span @if($data['isIndex'] == 0) class="checked" @endif><input type="radio" class="uniform " name="isIndex" value="0" @if($data['isIndex'] == 0) checked="" @endif></span></div>--}}
                        {{--<span>不是</span>--}}
                    {{--</label>--}}
                    {{--<span>&nbsp;&nbsp;</span>--}}
                {{--</div>--}}
            {{--</div>--}}

            <yz:fitem label="是否是首页">
                <php> $isIndex = isset($data['isIndex']) ? $data['isIndex'] : 0 </php>
                <yz:radio name="isIndex" options="1,0" texts="是,不是" checked="$isIndex"></yz:radio>
            </yz:fitem>
            <div id="-form-item" class="u-fitem clearfix" style="margin-top: -18px;">
                <span class="f-tt">
                    &nbsp;&nbsp;
                </span>
                <div class="f-boxr">
                    (当设置物业为首页时，不可再设置其他链接类型为首页)
                    <span>&nbsp;&nbsp;</span>
                </div>
            </div>


			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form> 
	@yizan_end
	<script type="text/javascript"> 
		$(function(){ 
			$(".icon_item").click(function(){
				if($(".icon_item.red").length == 2 && !$(this).hasClass('red')){
					$.ShowAlert('一个导航最多只能添加2个图标');
					return;
				}
				if($(this).hasClass('red')){
					$(this).removeClass('red');
				} else {
					$(this).addClass('red');
				}   
				var iconstr = "";
				var i = 0;
				$(".icon_item.red").each(function(){
					i++;
				    if(i == $(".icon_item.red").length){
				    	iconstr += $(this).data('val');
				    } else {
				    	iconstr += ($(this).data('val') + ",");
				    }
			  	}); 
			  	$("#icon").val(iconstr);
			});
		})
	</script>
@stop
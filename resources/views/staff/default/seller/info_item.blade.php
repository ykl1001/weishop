<div class="fine-bor pl-085 bg_fff">
    <div class="fine-bor pr-085  info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/name') }}','#seller_name_view',2)"  class=" w_b">
            <p class="w_b_f_1 ">店铺名称: {{$seller['name']}}</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    <table width="100%" class="fine-bor info-tr">
        <tr>
            <td >
                <p class="w_b_f_1 ">店铺LOGO:</p>
            </td>
            <td class="w340">
                <form>
                    <label id="imglabel" class="img-up-lb">
                        @if(!empty($seller['img']))
                            <img class="img_box avatar_img" src="{{ formatImage($seller['img'],100,100) }}" alt="">
                        @else
                            <img class="img_box avatar_img" src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                        @endif
                        
                    </label>
					<input type="file" id="image-form-file" accept="image/*" style="display:none" />
                </form>
            </td>
            <td  class="tr w130">
                <i class="icon iconfont">&#xe64b;</i>
            </td>
        </tr>
    </table>
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/announcement') }}','#seller_announcement_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">店铺公告: {{$seller['article']}}</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
</div>

<div class="blank070"></div>

<div class="fine-bor fine-bor-top pl-085 bg_fff">
    <div class="fine-bor pr-085  info-tr">
        <div href="#"  class=" w_b">
            <p class="w_b_f_1 status_msg">营业状态:@if($seller['status'])正常营业 @else暂停营业 @endif</p>
            <label class="label-switch status_checkbox">
                <input type="checkbox" name="status_checkbox" @if($seller['status']) checked="true" @endif>
                <div class="checkbox status_checkbox_dsy"></div>
            </label>
        </div>
    </div>
    @if($seller['storeType'] != 1)
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/time') }}','#seller_time_view',2)"  class=" w_b">
            <p class="w_b_f_1 text-overflow">营业时间: &nbsp;{{$seller['businessHour']}}</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
    @if($seller['storeType'] != 1)
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/delivery') }}','#seller_delivery_view',2)" class=" w_b">
            <p>配送时间: &nbsp;</p>
            <p class="w_b_f_1 text-overflow">
                @foreach($seller['deliveryTime']['stimes']  as $k => $v)
                    {{$v}}-{{$seller['deliveryTime']['etimes'][$k]}}<br/>
                @endforeach
            </p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/tel') }}','#seller_tel_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">联系电话:{{$seller['tel']}}</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @if($seller['storeType'] != 1)
    <div class="fine-bor pr-085 info-tr">
        <a  href="#" onclick="JumpURL('{{ u('Seller/serviceFee') }}','#seller_serviceFee_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">起送价:{{$seller['serviceFee'] or 0}}元</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
    @if($seller['storeType'] != 1)
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/deliveryFee') }}','#seller_deliveryFee_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">配送费:{{$seller['deliveryFee'] or 0}}元</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/deliveryFee') }}','#seller_deliveryFee_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">满免金额:@if($seller['isAvoidFee']){{$seller['avoidFee'] or 0}}元 @else 未设置 @endif</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
    <div class="fine-bor pr-085 info-tr">
        <a href="#" class=" w_b">
            <p class="w_b_f_1 text-overflow">佣金比例:{{$seller['deduct'] or 0}}%</p>
        </a>
    </div>
    @if($seller['storeType'] != 1)
    <div class="fine-bor pr-085  info-tr">
        <div class=" w_b">
            <p class="w_b_f_1 h_checkbox_msg">货到付款:@if($seller['isCashOnDelivery'])开启 @else关闭 @endif</p>
            <label class="label-switch h_checkbox">
                <input type="checkbox" name="h_checkbox" @if($seller['isCashOnDelivery']) checked="true" @endif>
                <div class="checkbox h_checkbox_dsy"></div>
            </label>
        </div>
    </div>
    @endif
</div>

<div class="blank070"></div>

<div class="fine-bor fine-bor-top pl-085 bg_fff">
    <div class="pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/region') }}','#seller_region_view',1)" class=" w_b">
            <p class="w_b_f_1 text-overflow">所在地区: {{$seller['region']}} </p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @if($seller['storeType'] == 1)
    <div class="fine-bor pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/refundaddress',['refundaddress'=>$seller['refundAddress']]) }}','#seller_refundaddress_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">退货地址:{{$seller['refundAddress']}}</p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
    @if($seller['storeType'] != 1)
    <div class="pr-085 info-tr">
        <a href="#" onclick="JumpURL('{{ u('Seller/map') }}','#seller_map_view',2)" class=" w_b">
            <p class="w_b_f_1 text-overflow">服务范围: {{$seller['serviceRange']}} </p>
            <i class="icon iconfont">&#xe64b;</i>
        </a>
    </div>
    @endif
</div>

<div class="blank070"></div>

<div class="fine-bor  fine-bor-top pl-085 bg_fff">
    <table width="100%" class="fine-bor info-tr" onclick="JumpURL('{{ u('Seller/brief') }}','#seller_map_view',2)">
        <tr class="url_brief">
            <td >
                <p class="w_b_f_1 ">店铺介绍:</p>
                <p class="p_con">
                    {{$seller['brief']}}
                </p>
            </td>
            <td  class="tr w130">
                <i class="icon iconfont">&#xe64b;</i>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
    $(document).on('click','.avatar_img', function () {
        $(this).fanweImage({
            width:320, 
            height:320, 
            callback:function(url, target) {
                $('.avatar_img').get(0).src = url; 
                $.post("{{ u('Seller/savelogo') }}", {"img": url }, function (res)
                {
                    if (res.code == '99996')
                    {
                        $.toast("登录已退出,重新登录");
                        window.location.href = "{{ u('User/login') }}";
                    } 
                }, "json")
            }
        });
    }); 
    Zepto(function($){ 
	
        @if($isV)
        $(document).on('click', '#{{$id_action.$ajaxurl_page}} .status_checkbox .status_checkbox_dsy', false);
        $(document).on('click', '#{{$id_action.$ajaxurl_page}} .h_checkbox .h_checkbox_dsy', false);
        @endif
        $(document).on('click','#{{$id_action.$ajaxurl_page}} .status_checkbox .status_checkbox_dsy',function () {
                    var bln = $("#{{$id_action.$ajaxurl_page}} input[name='status_checkbox']").is(':checked');
                    if(bln){
                        bln = 0;
                    }else{
                        bln = 1;
                    }
                    $.post('{{u("Seller/isStatus")}}',{"status":bln},function(res){
                        if(bln == false){
                            $("#{{$id_action.$ajaxurl_page}} .status_msg").html("营业状态:暂停营业");
                        }else{
                            $("#{{$id_action.$ajaxurl_page}} .status_msg").html("营业状态:正在营业");
                        }
                        return false;
                    },'json');
                });

        $(document).on('click','#{{$id_action.$ajaxurl_page}}  .h_checkbox .h_checkbox_dsy',function () {
            var bln = $("#{{$id_action.$ajaxurl_page}}  input[name='h_checkbox']").is(':checked');
            if(bln){
                bln = 0;
            }else{
                bln = 1;
            }
            $.post('{{u("Seller/isDelivery")}}',{"delivery":bln},function(){
                if(bln == false){
                    $("#{{$id_action.$ajaxurl_page}}  .h_checkbox_msg").html("货到付款:已关闭");
                }else{
                    $("#{{$id_action.$ajaxurl_page}}  .h_checkbox_msg").html("货到付款:已开启");
                }
                return false;
            },'json');

        });

    });
</script>
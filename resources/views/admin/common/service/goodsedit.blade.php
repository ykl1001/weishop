@extends('admin._layouts.base')
@section('css')
    <style type="text/css">
        #searchSeller{margin-left: 5px;}
        #mobile{width: 100px;}
        .setprice{width: 60px;margin: 0px 5px;}
        .allprice{margin-left: 20px; color: #999;}
        .ts,.ts3{color: #999;margin-left: 5px;vertical-align:middle;}
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        .m-spboxlst li{margin-bottom: 0px;}
        #tags_goods-form-item .f-boxr {width:550px;}
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        .m-spboxlst li{margin-bottom: 0px;}
        #tags_goods-form-item .f-boxr {width:550px;}
        .f-boxr .btn{background: #efefef; border-color: #dfdfdf; width: 100px; color: #555;}
        .x-gebox{border: 1px solid #ddd; padding: 5px 20px;}
        .x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
        .closege{width: 20px; height: 20px; background: url("{{ asset('wap/community/client/images/ico/close.png') }}"); background-size: 100% 100%; display: inline-block; cursor: pointer; vertical-align: middle; margin-top: -2px;}
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="{{$systemgoodssave ? $systemgoodssave : 'serviceSave'}}">
        <input type="hidden" name="type" value="{{$systemgoodssave ? 1 : $args['type']}}" />
        <input type="hidden" name="sellerId" value="{{$args['sellerId']}}" />
        <yz:fitem name="name" label="商品标题"></yz:fitem>
        <yz:fitem name="goodsSn" label="商品编码">
            @if(!$data)
                <INPUT class="u-ipttext" maxlength="16" size="16" name=goodsSn id="goodsSn" onKeyUp="value=value.replace(/[\W]/g,'')">
                <span>(请输入1-16位字母和数字组合的编码)</span>
            @else
                @if($systemgoodssave)
                    <INPUT class="u-ipttext" maxlength="16" size="16" name=goodsSn id="goodsSn" onKeyUp="value=value.replace(/[\W]/g,'')">
                    <span>(请输入1-16位字母和数字组合的编码)</span>
                @else
                    {{$data['goodsSn'] or "无"}}
                @endif
            @endif
        </yz:fitem>
        <yz:fitem label="商品分类">
            <yz:select name="cateId" options="$cate" textfield="name" valuefield="id" selected="$data['cate']['id']"></yz:select>
        </yz:fitem>
        <yz:fitem label="商品标签">
            @if($systemgoodssave != "" || $data['systemGoodsId'] > 0)
                <yz:select  disabled='disabled' name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
                <yz:select disabled='disabled' name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
                <input type="hidden"  name="systemTagListPid" value="{{$data['systemTagListPid']}}"/>
                <input type="hidden"  name="systemTagListId" value="{{$data['systemTagListId']}}"/>
            @else
                <yz:select  name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
                <yz:select  name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
            @endif
        </yz:fitem>
        <yz:fitem name="price" label="价格"></yz:fitem>
        <yz:fitem name="stock" label="库存" val="0"></yz:fitem>
        <!--yz:fitem name="totalStock" attr="readonly" label="总库存" val="0"></yz:fitem-->
        <div id="norms-form-item" class="u-fitem clearfix">
            <input type="hidden" name="id" value="{{$data['id']}}">
            @include('_layouts.stock')
        </div>
        <yz:fitem label="商品图片">
            <yz:imageList name="images." images="$data['images']"></yz:imageList>
        </yz:fitem>
        <yz:fitem name="buyLimit" label="每人限购"></yz:fitem>
        <yz:fitem name="brief" label="商品描述">
            <yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
        </yz:fitem>
        <yz:fitem label="商品状态">
            <php> $status = (int)$data['status'] </php>
            <yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
        </yz:fitem>
        <yz:fitem name="sort" label="排序"></yz:fitem>
        @if($fx)
            <!-- 全国店商品添加分销模式 -->
            <!-- <div class="u-rtt"><span class="ml15"></span>分销系统参数</div>
				<yz:fitem label="分销通道">
	                <yz:select name="passageId" options="$passageId" textfield="name" valuefield="id" selected="$data['fanwefx']['passageId']"></yz:select>
				</yz:fitem>
				<yz:fitem label="分销方案">
	                <yz:select name="schemeId" options="$schemeId" textfield="name" valuefield="id" selected="$data['fanwefx']['schemeId']"></yz:select>
				</yz:fitem>
				<yz:fitem name="baseMoney" label="分销金额基数" val="{{$data['fanwefx']['baseMoney']}}" append="1">
					<span class="ml5">元</span>
					<span class="ml5 red">*用于给用户分成的金额</span>
				</yz:fitem>
				<yz:fitem name="limitMoney" label="分销金额上限" val="{{$data['fanwefx']['limitMoney']}}" append="1">
					<span class="ml5">元</span>
					<span class="ml5 red">*获得分销返佣金币的上限，0表示不设置上限</span>
				</yz:fitem>
				<input type="hidden" name="fx" value="1"> -->
        @endif
    </yz:form>
    @yizan_end
@stop
@section('js')
    <script type="text/tpl" id="normsrow">
	<div class="x-gebox" style="margin-top:3px;">
		型号：<input type="text" name="_name[]" class="u-ipttext" />
		价格：<input type="text" name="_price[]" class="u-ipttext" />
		库存：<input type="text" name="_stock[]" class="u-ipttext" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
		<i class="closege"></i>
    </div>
</script>
    <script type="text/javascript">
        var cate = eval( <?php echo json_encode($cate); ?> );
        var editId = "<?php if(isset($data['seller']['id'])){ echo $data['seller']['id'];} ?>";
        var cateId = "<?php if(isset($data['cate']['id'])){ echo $data['cate']['id'];} ?>";
        var priceType = "{{ $priceType }}";
        $(".add_norms").click(function(){
            $(".norms_panel").append($("#normsrow").html());
            if($(".x-gebox").length > 0){
                $(".norms_panel").parent().show();
            }
        });
        $(document).on('click','.closege',function(){
            $(this).parent().remove();
            if($(".x-gebox").length <= 0){
                $(".norms_panel").parent().hide();
            }
        });
        $(function(){
            if( priceType == 1 ) {
                $("#ci").show();
            }
            else if(  priceType == 2 ) {
                $("#shi").show();
            }

            $("input[name='priceType']").change(function(){
                //按次计费
                if( $(this).val() == 1 ){
                    $("#shi").hide();
                    $("#ci").show();
                }
                //按时计费
                else{
                    $("#ci").hide();
                    $("#shi").show();
                }
            });

            $('#setprice_hour').blur(function(){
                var hour = $(this).val();
                if( hour > 0 ) {
                    $('.ts4').text( hour );
                }else{
                    $('.ts4').text( 0 );
                }
            });

            $('#setprice_money').blur(function(){
                var money = $(this).val();
                if( money > 0 ) {
                    $('.ts5').text( money );
                }else{
                    $('.ts5').text( 0 );
                }
                $('.city_price_box input.price').val(money);
            });

            $('#setprice_price').blur(function(){
                $('.city_price_box input.price').val( $(this).val() );
            });


            {{--if( editId > 0 ) {--}}
            {{--$("#sellerId").html("<option value='"+editId+"' selected>{{$data['seller']['name']}}</option>");--}}
            {{--$('.ts1').text( cate[cateId]['levelrel'] );--}}
            {{--}--}}

            {{--$('#cateId').change(function(){--}}
            {{--$('.ts1').text( cate[$(this).val()]['levelrel'] );--}}
            {{--});--}}

            $('#searchSeller').click(function(){
                clearts();
                var mobileName = $('#mobile').val();
                $.post("{{u('Order/getSellerInfo')}}",{"mobileName":mobileName},function(res){
                    res = eval(res);
                    if(res.length>0){
                        var html = "";
                        $.each(res,function(n,value) {
                            if(n<1){
                                $('#mobile').val(value.mobile);
                            }
                            html += "<option value='"+value.id+"' data-mobile='"+value.mobile+"'>"+value.name+"</option>";
                        });
                        $("#sellerId").html(html);
                    }else{
                        $("#sellerId").html("<option value='0'>请输入手机号或昵称</option>");
                        $(".ts2").text('未查询到相关服务人员');
                    }


                });
            });

            $("#sellerId").change(function(){
                $('#mobile').val( $("#sellerId option:checked").data('mobile') );
            });
        })

        function clearts() {
            $('.ts').text('');
        }

        $("#systemTagListPid").change(function(){
            var tagId = $(this).val();
            if(tagId == 0)
            {
                $("#systemTagListId").html('').addClass('none');
            }
            else
            {
                $.post("{{ u('SystemTagList/secondLevel') }}", {'pid': tagId}, function(res){

                    if(res!='')
                    {
                        var html = '<option value=0>请选择</option>';
                        $.each(res, function(k,v){
                            html += "<option value='"+v.id+"'>"+v.name+"</option>";
                        });
                        $("#systemTagListId").html(html).removeClass('none');
                    }
                    else
                    {
                        $("#systemTagListId").html('').addClass('none');
                        alert("当前分类暂无二级分类，请重新选择！");
                    }

                });
            }
        });
    </script>
@stop

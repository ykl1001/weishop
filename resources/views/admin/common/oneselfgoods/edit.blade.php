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
        <input type="hidden" name="type" value="{{$args['type'] or $data['type']}}" />
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
        <!-- <yz:fitem label="商品标签">
            <yz:select name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
            <yz:select name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
        </yz:fitem> -->
        <yz:fitem label="商品标签">
            @if($systemgoodssave != "" || $data['systemGoodsId'] > 0)
                <yz:select  disabled='disabled' name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
                <yz:select disabled='disabled' name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
                <input type="hidden"  name="systemTagListPid" value="{{$data['systemTagListPid']}}"/>
                <input type="hidden"  name="systemTagListId" value="{{$data['systemTagListId']}}"/>
                <input type="hidden"  name="sellerId" value="{{$args['sellerId']}}"/>
            @else
                <yz:select  name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
                <yz:select  name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
            @endif
        </yz:fitem>
        <div id="price-form-item" class="u-fitem clearfix">
            <span class="f-tt">
                价格:
            </span>
            <div class="f-boxr">
                <input type="text" name="price" id="price" class="u-ipttext" value="{{ $data['price'] ? $data['price'] : 0 }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
            </div>
        </div>
        <!-- <yz:fitem name="price" label="价格"></yz:fitem> -->
        <div id="stock-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                库存:
            </span>
            <div class="f-boxr">
                <input type="text" name="stock" id="stock" class="u-ipttext" value="{{ $data['stock'] ? $data['stock'] : 0 }}" onKeyUp="amounts(this)" onBlur="overFormats(this)">
            </div>
        </div>
        <!-- <yz:fitem name="stock" label="库存" val="0"></yz:fitem> -->
        <div id="norms-form-item" class="u-fitem clearfix">
            @include('_layouts.stock')
        </div>
        <div id="norms-form-item" class="u-fitem clearfix x-addge">
            <span class="f-tt">&nbsp;</span>
            <div class="f-boxr norms_panel">
                @foreach($data['norms'] as $item)
                    <div class="x-gebox">
                        <input type="hidden" name="norms[ids][]" value="{{$item['id']}}" >
                        型号：<input type="text" name="norms[name][]" value="{{$item['name']}}" class="u-ipttext" />
                        价格：<input type="text" name="norms[price][]" value="{{$item['price']}}" class="u-ipttext" onKeyUp="amount(this)" onBlur="overFormat(this)" />
                        库存：<input type="text" name="norms[stock][]" value="{{$item['stock']}}" class="u-ipttext" onKeyUp="amounts(this)" onBlur="overFormats(this)" />
                        <i class="closege"></i>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="-form-item" class="u-fitem clearfix ">
            <yz:fitem label="商品图片">
                <yz:imageList name="images." images="$data['images']"></yz:imageList>
                <div><small class='cred pl10 gray'>建议尺寸：750px*750px，支持JPG/PNG格式</small></div>
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
        </div>
    </yz:form>
    @yizan_end
@stop
@section('js')
    <script type="text/tpl" id="normsrow">
        <div class="x-gebox" style="margin-top:3px;">
            型号：<input type="text" name="norms[name][]" class="u-ipttext" />
            价格：<input type="text" name="norms[price][]" class="u-ipttext" onKeyUp="amount(this)" onBlur="overFormat(this)" />
            库存：<input type="text" name="norms[stock][]" class="u-ipttext" onKeyUp="amounts(this)" onBlur="overFormats(this)" />
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

            $('#cateId').change(function(){
                $('.ts1').text( cate[$(this).val()]['levelrel'] );
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

        /**
         * 实时动态强制更改用户录入
         * arg1 inputObject
         **/
        function amount(th){
            var regStrs = [
                ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
                ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
                ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
                ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
            ];
            for(i=0; i<regStrs.length; i++){
                var reg = new RegExp(regStrs[i][0]);
                th.value = th.value.replace(reg, regStrs[i][1]);
            }
        }
        /**
        * 录入完成后，输入模式失去焦点后对录入进行判断并强制更改，并对小数点进行0补全
        * arg1 inputObject
        * 这个函数写得很傻，是我很早以前写的了，没有进行优化，但功能十分齐全，你尝试着使用
        * 其实有一种可以更快速的JavaScript内置函数可以提取杂乱数据中的数字：
        * parseFloat('10');
        **/

        function overFormat(th){
            var v = th.value;
                if(v === ''){
                    v = '0.00';
                }else if(v === '0'){
                    v = '0.00';
                }else if(v === '0.'){
                    v = '0.00';
                }else if(/^0+\d+\.?\d*.*$/.test(v)){
                    v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
                    v = inp.getRightPriceFormat(v).val;
                }else if(/^0\.\d$/.test(v)){
                     v = v + '0';
                }else if(!/^\d+\.\d{2}$/.test(v)){
                    if(/^\d+\.\d{2}.+/.test(v)){
                        v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
                    }else if(/^\d+$/.test(v)){
                        v = v + '.00';
                    }else if(/^\d+\.$/.test(v)){
                        v = v + '00';
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
                        v = '0.00';
                    }
                }
            th.value = v;
        }

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

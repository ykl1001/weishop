<style>
    .ax_popup{z-index: 999}
    .wd-m-sku{width:100%;padding:5px 10px;min-width:800px;border: 1px solid #dfdfdf;border-radius:5px;}
    .ma-sku-group{width:100%;padding:10px 0;position:relative;}
    select.sku-group{width:120px;}
    .list-table table.sku-t td{text-align:left;}
    .remove-sku{position:absolute;right:10px;top:10px;}
    .addnew{padding:10px;}
    #wdmaskustock {background: #fff none repeat scroll 0 0;max-width: 1200px;}
    .ma-sku-group .sku-item{margin:2px 2px 2px 0;}
    .ma-sku-group .sku-item-add-btn{margin:2px 10px;}
    .ma-sku-group .sku-item-name{width:75px!important;}
    .custom-sku{width:120px;height:25px;pading:2px;margin:5px;padding:2px 5px;}
    .sub-sku{width:120px;}
    .sub-sku-tmp .btn-sm{width:50px;pading:2px;margin:0 5px;}
    .sku-cus{}
    .sub-sku-list .sub-sku-item{width:119px;text-align:center;font-size:12px;padding:1px;margin-right:12px;display:inline-block;position:relative;}
    .sub-sku-list .sub-sku-item span{width:115px;overflow: hidden;text-overflow: ellipsis;text-align:center;font-size:12px;padding:5px;display:inline-block;border:1px solid #afafaf;border-radius:5px;}
    .sub-sku-list .remove-sub-sku,.sub-sku-tmp .cancel{width:16px;height:16px;text-align:center;line-height:16px;position:absolute;right:-7px;top:-7px;border-radius:8px;background:#aaa;opacity:0.5;cursor:pointer;}
    .sub-sku-list .remove-sub-sku:hover,.sub-sku-tmp .cancel:hover{background:#000;color:#fff;opacity:1;}
    .sub-sku-tmp{background:#fff;border: 1px solid #e1e1e1;display: inline-block;padding: 10px;position: absolute;position:absolute;z-index:1010;border-radius:2px;-webkit-box-shadow:0px 1px 6px rgba(0,0,0,0.4);box-shadow:0px 1px 6px rgba(0,0,0,0.4)}
    .sub-sku-tmp .holder{padding:10px 0;}
    .sub-sku-tmp .holder .sub-sku-item{width:100px;text-align:center;font-size:12px;padding:5px;margin-right:12px;display:inline-block;position:relative;padding:3px;margin:3px 0 3px 5px;position:relative;line-height:13px;}
    .sub-sku-tmp .holder .sub-sku-item span{width:100px;overflow: hidden;text-overflow: ellipsis;text-align:center;display:inline-block;border:1px solid #afafaf;border-radius:5px;padding:5px 0;line-height:13px;}
    .sub-sku-tmp .holder .remove-sub-sku{width:16px;height:16px;position:absolute;right:0px;top:8px;cursor:pointer;opacity:0.5;}
    .sub-sku-tmp .holder .remove-sub-sku:hover{font-weight:bold;opacity:1;}
    .sub-sku-item span{color:#333;cursor:default;border:1px solid #aaaaaa;border-radius:3px;-webkit-box-shadow:0 0 2px #fff inset,0 1px 0 rgba(0,0,0,0.05);box-shadow:0 0 2px #fff inset,0 1px 0 rgba(0,0,0,0.05);background-clip:padding-box;-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-color:#e4e4e4;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#f4f4f4', GradientType=0);background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eee));background-image:-webkit-linear-gradient(to bottom, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);background-image:-moz-linear-gradient(to bottom, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);background-image:-webkit-gradient(linear, left top, left bottom, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), to(#eee));background-image:-webkit-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);background-image:-moz-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);background-image:linear-gradient(to bottom, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%)}
    .table-sku-stock{width:100%;font-size: 12px;margin-bottom:2px;}
    .batch-setting{font-size: 12px;text-align:right;width:100%;margin:15px 0 0;}
    .batch-setting a{font-size: 9px;}
    .table-sku-stock .error{color: #d22527;}
    .list-table table.table-sku-stock td{padding:5px;vertical-align:m !important;}
    .table-sku-stock th ,.table-sku-stock td{border-collapse:collapse;border:1px solid #e1e1e1;border-left:0;min-height:40px;}
    .table-sku-stock input{display:inline-block;height:25px;padding:4px 6px;margin:0px;font-size:12px;line-height:25px;color:#555;vertical-align:middle;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;}
    .table-sku-stock input{background-color:#fff;border:1px solid #ccc;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border linear .2s,box-shadow linear .2s;-moz-transition:border linear .2s,box-shadow linear .2s;-o-transition:border linear .2s,box-shadow linear .2s;transition:border linear .2s,box-shadow linear .2s;}
    .table-sku-stock input:focus{border-color:rgba(82,168,236,.8);outline:0;outline:dotted thin\9;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(82,168,236,.6);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(82,168,236,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(82,168,236,.6);}
    .table-sku-stock th{font-weight:bold;text-align:center;line-height:35px;border:0;}
    .th-price,.th-market-price{width:100px;}
    .th-stock{width:100px;}
    .th-code{width:100px;}
    .th-sale{width:100px;border:0;}
    td.td-sale{width:100px;border-right:0;}
    input.mini{width:50px;}
    .sub-sku-item .image-add-btn {
        height: 85px;
        width:118px;
        font-size: 16px;
        line-height: 84px;
        background: rgba(0,0,0,0.7);
        color: #fff;
        text-align: center;
        position:relative;
        display: block;}
    .sub-sku-item img{width:100px;height:85px;width:118px;position:absolute;top:0;left:0;}
    .sku-img-wrap{width:119px;height:85px;position:relative;}
    .sku-img-wrap .sku-image-upload-btn{
        position: absolute;
        background: rgba(190,190,190,0.7);
        bottom: 0;
        left: 0;
        width: 100%;
        height: 30px;
        line-height: 30px;
        color: #fff;
        width: 100%;
        text-align: center;
    }
    .sku-image-delete{content: "\f00d";
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        padding: 0 3px;
        color: #fff;
        width: 16px;
        text-align: center;
        line-height: 16px;
        border-radius: 0 0 0 5px;
        font-weight:bold;
    }
    body{background: #fff;}
    .sub-sku-tmp {
        width: 386px;
        height: 82px;
        border: 2px solid #000000;
        border-radius: 8px;
        color: #999999;
        position: absolute;
        background: #fff;
        margin-left: 40%;
        margin-top: -26px;
        padding: 10px 10px;
        display: none;
    }
    .custom-sku {
        height: 42px;
        border: 1px solid #ccc;
        width: 244px;
        color: #464646;
        margin: 0px;
        padding: 0px 0px;
    }
    .add-custom-sku {
        width: 48px !important;
        margin-left: 6px !important;
        margin: 0 0  !important;
        margin-right: 6px !important;
        height: 30px;
        border: 1px solid #797979;
        background: #bcbcbc;
        color: #fff;
        border-radius: 4px;
        padding:0;
    }
    .confirm-sub-sku {
        width: 48px !important;
        padding:0;
        margin: 0 0  !important;
        height: 30px;
        border: 1px solid #797979;
        background: #f9f9f9;
        color: #333;
        border-radius: 4px;
    }
    .list-table table td {
        text-align: center;
        vertical-align: middle!important;
    }
    .add-custom-sku:hover{background: #bcbcbc;border: 1px solid #797979;}
    .confirm-sub-sku:hover{background: #f9f9f9; color: #333;border: 1px solid #797979;}
    .btn-default:focus{background: #fff;}
    .btn-default:hover{background: #fff;}
    .sub-sku-list .sub-sku-item {
        float: left;
        width: 83px;
        height: 30px;
        border: 1px solid #ccc;
        padding: 0;
    }
    .sub-sku-list{
        padding: 0;
        margin: 0;
    }
    .sub-sku-list .sub-sku-item span{
        background:#fff;
        border: 0;
        line-height: 27px;
        float: left;
        font-size: 14px;
        text-align: center;
        color: #656565;
        width: 52px;
        height: 27px;
        padding:0;
    }
    .sub-sku-list .remove-sub-sku, .sub-sku-tmp .cancel{
        width: 28px;
        height: 29px;
        line-height: 25px;
        background: #656565;
        float: right;
        font-size: 28px;
        color: #fff;
        border: 1px solid #656565;
        position: relative;
        top: 0;
        right: 0;
        border-radius:0;
        opacity:1;
    }
    .ax_insert{margin-bottom:10px;margin-top:10px;height: 30px;line-height: 30px;margin-left: 100px;}
    .ax_insert input{height: 30px;width: 102px;border: 1px solid #ccc;}
    .ax_insert button{height: 32px;width: 85px;border-radius:4px;border: 1px solid #ccc;margin-left: 5px;}
    .ax_name{float: left;margin-top: 20px;width:100px;height: 16px;text-align: right;}
    .ax_select {float: left;width: 232px;}
    .ax_input {margin-left: 0px;margin-margin-left: 100px;margin-top: 16px;width: 232px;height: 30px;}
    .wd-m-sku{border:1px solid #fff;}
    .wd-m-sku-n-btn{margin-left: 90px;}
    #wdmaskustock{max-width:692px !important;background: #fff;}
    .table-sku-stock{max-width: 692px;margin-left: 90px;border: 1px solid #e4e4e4;}
    .table-sku-stock th{width: 116px;border: 1px solid #e4e4e4;}
    .table-sku-stock td{width: 116px;border: 1px solid #e4e4e4;}
    .table-sku-stock td input{width: 87px;height: 22px;border: 1px solid #a9a9a9;text-align: center;}
</style>
<div class="ax_list">
    <div id="seller-cate-form-item" class="u-fitem clearfix ">
        <span class="f-tt">
             属性分类:
        </span>
        <div class="f-boxr">
            <select id="stock" name="stock_id" style="min-width:234px;width:auto" class="sle  ">
                <option value="0">请选择</option>
                @foreach($stock as $s)
                    <option value="{{$s['id']}}" @if($data['stockTypeId'] == $s['id']) selected @endif data-val="{{ json_encode($s['stock'])  }}">{{$s['name']}}</option>
                @endforeach
            </select>
        </div><span style="color: #ff0000">(* 每个子属性限5个值)</span>
    </div>
</div>
<div class="ax_list">
    <div class="ax_nature">
        @foreach($stockItem['skuItemsGroup'] as $k => $items)
            <?php
            if( $k == 1){
                $type = 'first';
            }else if($k==2){
                $type = 'second';
            }else{
                $type = 'vessel';
            }
            ?>
            <div class="ax_cell ma-sku-group ax_cell_{{$items[0]['groupId']}}_{{$k}}" data-id='{{$k}}'>
                <span class="gval">{{$items[0]['groupName']}}：</span>
                <div class="ax_grid-add who-{{$type}} sub-sku-list">
                    <div class="ax_popup">
                        <div class="ax_import">
                            <input type="text" name="" class="ax_input_text">
                            <button type="button" class="ax_no">取消</button>
                            <button type="button" class="ax_yes" data-type="{{$type}}">保存</button>
                        </div>
                        可批量添加，用空格隔开
                    </div>
                    @foreach($items as $keyItme => $timeVal)
                        <div class="sub-sku-item" data-value="{{$timeVal['name']}}">

                            @if(!$locking)
                                <div class="remove-sub-sku" data-type="{{$type}}">X</div>
                            @endif
                            <span>{{$timeVal['name']}}</span>
                            <input type="hidden" name="sku_item[{{$type}}][]" value="{{$timeVal['name']}}" readonly="readonly">
                        </div>
                    @endforeach
                </div>
                <div class="holder" style="display: none">

                </div>
                @if(!$locking)
                    <a data-who="{{$type}}" class="type-{{$type}} @if(count($items) >= 5) none @endif">添加+</a>
                @endif
            </div>
        @endforeach
    </div>
    <?php
    $firstSkuGroup = $stockItem['skuItemsGroup'][1];
    if(isset( $stockItem['skuItemsGroup'][2])) $secSkuGroup =  $stockItem['skuItemsGroup'][2];
    if(isset( $stockItem['skuItemsGroup'][3])) $thirdSkuGroup =  $stockItem['skuItemsGroup'][3];
    $index = 0;
    $secTotal =  count($secSkuGroup);
    $thirdTotal =  count($thirdSkuGroup);
    $skuStock = $stockItem['skuStock'];
    ?>

    @if(!$locking)
        <div class="ax_insert @if(!$stockItem['skuStock']) none @endif  ax_insert_batch_management ">
            <span>售价：</span>
            <input type="text"  name="addmoney" class="u-ipttext" onKeyUp="amounts(this)" onBlur="overFormats(this)">
            <span>元，库存</span>
            <input type="text" name="addstock" class="u-ipttext" onkeyup="if(!/^\d+$/.test(this.value)); this.value=this.value.replace(/[^\d]+/g,'');">
            <button type="button" class="batch_management">批量设置</button>
        </div>
    @endif
    <div id="goodsSkuStockBox"  class="form-group list-table" style="" >
        <div>
            <div class="wd-m-sku" style="@if(!$stockItem['skuStock']) display:none; @endif" id="wdmaskustock" >
                <table class="table-sku-stock">
                    <thead>
                    <tr>
                        @if($firstSkuGroup) <th class="">{{$firstSkuGroup[0]['groupName']}}</th> @endif
                        @if($secSkuGroup) <th class="">{{$secSkuGroup[0]['groupName']}}</th> @endif
                        @if($thirdSkuGroup) <th class="">{{$thirdSkuGroup[0]['groupName']}}</th> @endif
                        <th class="th-price pin">售价</th>
                        <th class="th-market-price pin">库存</th>
                        <th class="th-sale pin">销量</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($skuStock)
                        @foreach($firstSkuGroup as $key_f => $group_1)
                            @if($secSkuGroup)
                                @foreach($secSkuGroup as $key_s => $group_2)
                                    @if($thirdSkuGroup)
                                        @foreach($thirdSkuGroup as $key_t => $group_3)
                                            <?php $index = $group_1['id'].':'.$group_2['id'].':'.$group_3['id']; ?>
                                            <tr>
                                                @if($key_s == 0 && $key_t == 0) <td rowspan="{{$secTotal*$thirdTotal}}">{{$group_1['name']}}</td>@endif
                                                @if($key_t == 0) <td rowspan="{{$thirdTotal}}">{{$group_2['name']}}</td> @endif
                                                <td rowspan="1">{{$group_3['name']}}</td>
                                                <td><input type="text" name="sku_price[]" class="js-price input-mini currency" value="{{$skuStock[$index]['price']}}" maxlength="10" onKeyUp="amounts(this)" onBlur="overFormats(this)"></td>
                                                <td><input type="text" name="sku_stock[]" class="js-stock-num input-mini number" value="{{$skuStock[$index]['stockCount']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="9"></td>
                                                <td class="td-sale">{{$skuStock[$index]['saleCount']  or 0}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <?php $index = $group_1['id'].':'.$group_2['id']; ?>
                                        <tr>
                                            @if($key_s == 0) <td rowspan="{{$secTotal}}">{{$group_1['name']}}</td> @endif
                                            <td>{{$group_2['name']}}</td>
                                            <td><input type="text" name="sku_price[]" class="js-price input-mini currency" value="{{$skuStock[$index]['price']}}" maxlength="10" onKeyUp="amounts(this)" onBlur="overFormats(this)"></td>
                                            <td><input type="text" name="sku_stock[]" class="js-stock-num input-mini number" value="{{$skuStock[$index]['stockCount']}}" maxlength="9" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></td>
                                            <td class="td-sale">{{$skuStock[$index]['saleCount'] or 0}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <?php  $index = $group_1['id']; ?>
                                <tr>
                                    <td rowspan="1">{{$group_1['name']}}</td>
                                    <td><input type="text" name="sku_price[]" class="js-price input-mini currency" value="{{$skuStock[$index]['price']}}" maxlength="10" onKeyUp="amounts(this)" onBlur="overFormats(this)"></td>
                                    <td><input type="text" name="sku_stock[]" class="js-stock-num input-mini number" value="{{$skuStock[$index]['stockCount']}}" maxlength="9" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></td>
                                    <td class="td-sale">{{$skuStock[$index]['saleCount'] or 0 }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/tpl" id="goodsSkuStockTpl">
	<tr><td rowspan="1">skuname</td>
	<td><input type="text" name="sku_price[]" class="js-price input-mini currency" value="" maxlength="10" onKeyUp="amounts(this)" onBlur="overFormats(this)"></td>
	<td><input type="text" name="sku_stock[]" class="js-stock-num input-mini currency" value="" maxlength="10" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></td>
	<td class="td-sale">0</td></tr>
</script>
<script type="text/tpl" id="stockVal">
<div class="ax_cell ma-sku-group ax_cell_STOCKVALIDS_STOCKIDS" data-id='STOCKVALIDS'>
    <span class="gval">NAME：</span>
    <div class="ax_grid-add who-STOCKIDTYPE sub-sku-list">
        <div class="ax_popup">
            <div class="ax_import">
                <input type="text" name="" class="ax_input_text">
                <button type="button" class="ax_no">取消</button>
                <button type="button" class="ax_yes" data-type=STOCKIDTYPE>保存</button>
            </div>
            可批量添加，用空格隔开
        </div>
    </div>
    <div class="holder" style="display: none">

    </div>
    <a data-who="STOCKIDTYPE" class="type-STOCKIDTYPE">添加+</a>
</div>

</script>
<script  type="text/javascript">
    $(function(){
        var stcokId = 0;
        var who = '';
        @if($stockItem['skuItemsGroup'])
        $('input[name=price]').attr('readonly','readonly');
        $('input[name=stock]').attr('readonly','readonly');
        @endif

        $("select[name=stock_id]").change(function() {
                    $('select[name=stock_id] option:selected').attr('readonly','readonly');
                    var datas = $('select[name=stock_id] option:selected');
                    $('input[name=addmoney]').val("");
                    $('input[name=addstock]').val("");
                    if(datas.val() == 0){
                        $('input[name=price]').removeAttr('readonly');
                        $('input[name=stock]').removeAttr('readonly');
                        $(".ax_insert_batch_management").addClass('none');
                    }else if(datas.val() != 0){
                        $(".ax_nature,#goodsSkuStockBox,.ax_insert_batch_management ").removeClass('none');
                        $('input[name=price]').attr('readonly','readonly').val("");
                        $('input[name=stock]').attr('readonly','readonly').val("");
                        $("#wdmaskustock table tbody").html("");
                        $.each($("#wdmaskustock table thead th"),function(){
                            if(!$(this).hasClass("pin")){
                                $(this).remove();
                            }
                        });

                        $(".ax_nature").html("");
                        stcokId = datas.val();
                        var val =eval(datas.attr('data-val'));
                        val.reverse();
                        $.each(val,function(i,s) {
                            var html = $("#stockVal").html();
                            if (val.length == 1){
                                html = html.replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first');
                            }else if (val.length == 2){
                                if (i == 0) {
                                    html = html.replace('STOCKIDTYPE', 'second').replace('STOCKIDTYPE', 'second').replace('STOCKIDTYPE', 'second').replace('STOCKIDTYPE', 'second');
                                } else if (i == 1) {
                                    html = html.replace('STOCKIDTYPE', "first").replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first');
                                }
                            }else{
                                if (i == 0) {
                                    html = html.replace('STOCKIDTYPE', 'vessel').replace('STOCKIDTYPE', 'vessel').replace('STOCKIDTYPE', 'vessel').replace('STOCKIDTYPE', 'vessel');
                                } else if (i == 1) {
                                    html = html.replace('STOCKIDTYPE', "second").replace('STOCKIDTYPE', 'second').replace('STOCKIDTYPE', 'second').replace('STOCKIDTYPE', 'second');
                                } else {
                                    html = html.replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first').replace('STOCKIDTYPE', 'first');
                                }
                            }
                            var html = html.replace('NAME',s)
                                    .replace('STOCKVALIDS',stcokId)
                                    .replace('STOCKVALIDS',stcokId)
                                    .replace('STOCKIDS',i);
                            $(".ax_nature").prepend(html);
                        });
                    }
                });

        // 修改库存
        $(document).on('change','.js-stock-num',function(){
            countStock();
        });

        $(document).on('click', '.ax_popup .ax_gridadd button', function(e){
            $(this).parents(".ax_gridadd").hide();
        });
        $(document).on('click', '.ax_popup .ax_no', function(e){
            $(this).parents(".ax_popup").hide();
        });
        $(document).on('click', '.ax_cell a', function(e){
            who = $(this).attr('data-who');
            if(who == "second"){ //判断第一级有没有
                var is_first = 0;
                $(".who-first .sub-sku-item").each(function(){
                    is_first++;
                })
                if(is_first == 0){
                    $.ShowAlert('请先选择第一级');
                    return false;
                }
            }else if(who == "vessel"){ //判断第二级有没有
                var is_sec = 0;
                $(".who-second .sub-sku-item").each(function(){
                    is_sec++;
                })
                if(is_sec == 0){
                    $.ShowAlert('请先选择第二级');
                    return false;
                }
            }
            $(this).siblings(".ax_grid-add").children(".ax_popup").show();
            $('.ax_input_text').val("");
        });
        $(document).on('click', '.batch_management', function(e){
            var addmoney = $("input[name=addmoney]").val();
            var addstock = $("input[name=addstock]").val();
            if(addmoney > 0){
                $(".js-price").val(addmoney);
            }
            if(addstock > 0){
                $(".js-stock-num").val(addstock);
            }
            // 修改库存
            countStock();
        });

        $(document).on('click', '.ax_popup .ax_yes', function(e){
            var type = $(this).data("type");
            $(".ax_insert_batch_management").removeClass('none');
            var val = $(this).siblings('input').val().split('　').join(' ');
            var val = val.split(' ');
            var val = val.unique();
            var append_html = "";
            var itemVal = [];
            var html = '';
            if(type != ""){
                $.each($(".who-"+type+" .sub-sku-item"),function(){
                    itemVal.push($(this).attr("data-value"));
                });
            }
            var hasLength = document.getElementsByName("sku_item["+type+"][]").length;
            for (i = val.length - 1;  i >= 0; i--) {
                if (val[i] === "") {
                    val.splice(i, 1);
                }
            }
            var key = 0;
            var value = [];
            if(hasLength + val.length > 5) {
                key = 5 - hasLength;
                for (i = 0;  i <= key - 1; i++) {
                    value.push(val[i]);
                }
            } else {
                value = val;
            }

            $.each(value,function(i,v){
                if(v && !in_array(v,itemVal)){
                    append_html += '<div class="sub-sku-item" data-value="'+v+'">';
                    append_html +=	'<span>'+v+'</span>';
                    append_html +=	'</div>';
                }
            });
            $(this).parents('.sub-sku-list').siblings('.holder').html(append_html);
            updateSkuList(this);
            $(this).parents(".ax_popup").hide();
            $("#wdmaskustock").show();
            var counts = $(".who-"+type+" .sub-sku-item").length;
            $.each(value,function(i,v){
                if(v && !in_array(v,itemVal)){
                    html += '<div class="sub-sku-item" data-value="'+v+'" data-id="'+counts+'"><div class="remove-sub-sku" data-type="'+type+'">X</div><span>'+v+'</span><input type="hidden" name="sku_item['+type+'][]" value="'+v+'" readonly="readonly"></div>';
                }
                counts++;
            });
            $(this).parents(".ax_grid-add").append(html);

            var length = document.getElementsByName("sku_item["+type+"][]").length;
            if(length >= 5) {
                $(".type-"+type).addClass("none");
            }
        });
        $(document).on('click','.remove-sub-sku',function(){
            var type = $(this).data("type");
            var datavalue = $(this).parent().attr('data-value');
            $(this).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").each(function(){
                if($(this).attr('data-value') == datavalue){
                    $(this).remove();
                }
            })
            //更新库存
            deleteSku(this);
            $(this).parent().remove();
            var length = document.getElementsByName("sku_item["+type+"][]").length;
            if(length < 5) {
                $(".type-"+type).removeClass("none");
            }
        })
    })    //更新库存列表
    function updateSkuList(obj){
        var skuOrder = $(obj).parents(".ma-sku-group").index() + 1;
        var data = new Object();
        var skuNewGroup =  $(obj).parents(".ma-sku-group").find("span.gval").text().replace('：','');
        switch(skuOrder){
            case 1:
                if($("#wdmaskustock thead tr th.th-price").index()==0){
                    //新规格组
                    $("#wdmaskustock thead tr th.th-price").before("<th>"+skuNewGroup+"</th>");
                }
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格
                var thirdSkuList = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item");//三级规格

                $(obj).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").each(function(){

                    var firstSkuItem = this;
                    if(secSkuList.length>0){
                        var trlength = $("#wdmaskustock .table-sku-stock tbody tr").length;
                        var rowspan = secSkuList.length * thirdSkuList.length;
                        secSkuList.each(function(){
                            var secSkuItem = this;
                            if(thirdSkuList.length>0){
                                thirdSkuList.each(function(){

                                    var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                                    $("#wdmaskustock .table-sku-stock tbody").append(trSku);
                                });
                                //td head
                                $("#wdmaskustock .table-sku-stock tbody tr").eq($("#wdmaskustock .table-sku-stock tbody tr").length-thirdSkuList.length).prepend("<td rowspan='"+thirdSkuList.length+"'>"+$(secSkuItem).find("span").text()+"</td>");
                            }else{
                                //insert
                                var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                                $("#wdmaskustock .table-sku-stock tbody").append(trSku);
                            }

                        });
                        //td head
                        $("#wdmaskustock .table-sku-stock tbody tr").eq(trlength).prepend("<td rowspan='"+($("#wdmaskustock .table-sku-stock tbody tr").length-trlength)+"'>"+$(firstSkuItem).find("span").text()+"</td>");
                    }else{
                        var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                        $("#wdmaskustock .table-sku-stock tbody").append(trSku);
                    }

                });
                break;
            case 2:
                if($("#wdmaskustock thead tr th.th-price").index()==1){
                    //新规格组
                    $("#wdmaskustock thead tr th.th-price").before("<th>"+skuNewGroup+"</th>");
                }
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格

                var thirdSkuList = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item");//三级规格

                var secSkuTotal = $(".ma-sku-group").eq(1).find(".sub-sku-item").length + $(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").length;
                var secSkuExists = $(".ma-sku-group").eq(1).find(".sub-sku-item").length;
                var	secOffset = 0;
                $(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").each(function(){

                    var firstSkuOffset = 0;
                    var secSkuItem = this;

                    if(thirdSkuList.length>0){
                        //TODO
                        var firstOffset = 0;
                        firstSkuList.each(function(){

                            var thirdOffset = 0;
                            thirdSkuList.each(function(){
                                var insertOffset = (firstOffset * (((secOffset+secSkuList.length) * thirdSkuList.length )+thirdSkuList.length)) + ((secOffset+secSkuList.length) * thirdSkuList.length + thirdOffset - 1);

                                var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).after(trSku);

                                if(thirdOffset==0){
                                    $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset+1).children("td").eq(0).before("<td rowspan='"+thirdSkuList.length+"'>"+ $(secSkuItem).find("span").text() +"</td>");
                                }
                                thirdOffset++;
                            });

                            if(secOffset==0){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(firstOffset*(secSkuList.length+1)*thirdSkuList.length).children("td").eq(0).attr("rowspan",parseInt(($(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").length+secSkuList.length)*thirdSkuList.length));
                            }
                            firstOffset++;
                        });

                    }else{
                        if(secSkuList.length>0){//已有二级规格
                            var firstOffset = 0;
                            firstSkuList.each(function(){
                                var insertOffset = firstOffset * (secOffset+secSkuList.length+1) + (secOffset+secSkuList.length-1);

                                var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(secSkuItem).find("span").text());
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).after(trSku);

                                if(secOffset==0){
                                    $("#wdmaskustock .table-sku-stock tbody tr").eq((insertOffset+1)-secSkuList.length).children("td").eq(0).attr("rowspan",parseInt($(obj).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").length+secSkuList.length));
                                }
                                firstOffset++;
                            });

                        }else{
                            if($(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").length==1){

                                $("#wdmaskustock .table-sku-stock tbody tr").each(function(){
                                    $(this).children("td").eq(0).after("<td>"+ $(secSkuItem).find("span").text() +"</td>");
                                });
                            }else if($(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").length>1){//一次添加大于一个

                                var firstOffset = 0;
                                firstSkuList.each(function(){
                                    var insertOffset = firstOffset * (secOffset+1) + secOffset-1;
                                    if(secOffset>0){

                                        var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(secSkuItem).find("span").text());
                                        $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).after(trSku);

                                    }else{
                                        $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).children("td").eq(0).after("<td>"+ $(secSkuItem).find("span").text() +"</td>");
                                        $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).children("td").eq(0).attr("rowspan",$(obj).parents('.sub-sku-list').siblings('.holder').find(".sub-sku-item").length);
                                    }
                                    firstOffset++;
                                });

                            }
                        }

                    }
                    firstSkuOffset++;
                    secOffset++;
                });

                break;
            case 3:
                if($("#wdmaskustock thead tr th.th-price").index()==2){
                    //新规格组
                    $("#wdmaskustock thead tr th.th-price").before("<th>"+skuNewGroup+"</th>");
                }
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格
                var thirdSkuList = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item");//三级规格
                var thirdSkuAppendTotal = $(obj).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").length;
                var thirdOffset = 0;

                var firstOffset = 0;
                firstSkuList.each(function(){
                    var secOffset = 0;
                    secSkuList.each(function(){
                        if(thirdSkuList.length>0){
                            //已存在三级
                            var thirdOffset = 0;
                            $(obj).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").each(function(){

                                var thirdSkuItem = this;
                                var insertOffset = (firstOffset * secSkuList.length * (thirdSkuAppendTotal+thirdSkuList.length)) + (secOffset * (thirdSkuAppendTotal+thirdSkuList.length)) + thirdOffset + thirdSkuList.length-1;

                                var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(thirdSkuItem).find("span").text());
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).after(trSku);

                                thirdOffset++;
                            });
                        }else{
                            var thirdOffset = 0;
                            $(obj).parents('.sub-sku-list').siblings(".holder").find(".sub-sku-item").each(function(){

                                var thirdSkuItem = this;
                                var insertOffset = (firstOffset * secSkuList.length * thirdSkuAppendTotal) + (secOffset * thirdSkuAppendTotal) + thirdOffset-1;

                                if(thirdOffset>0){
                                    var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(thirdSkuItem).find("span").text());
                                    $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset).after(trSku);
                                }else{
                                    $("#wdmaskustock .table-sku-stock tbody tr").eq(insertOffset+1).find("input.js-price").parent().before("<td>"+ $(thirdSkuItem).find("span").text() +"</td>");
                                }
                                thirdOffset++;
                            });

                        }
                        var rospanOffset = 0;
                        if(secOffset==0)
                            rospanOffset = 1;
                        $("#wdmaskustock .table-sku-stock tbody tr").eq(firstOffset*secSkuList.length*(thirdSkuAppendTotal+thirdSkuList.length) + secOffset * (thirdSkuAppendTotal+thirdSkuList.length)).children("td").eq(rospanOffset).attr("rowspan",(thirdSkuAppendTotal+thirdSkuList.length));
                        secOffset++;
                    });
                    $("#wdmaskustock .table-sku-stock tbody tr").eq(firstOffset*secSkuList.length*(thirdSkuAppendTotal+thirdSkuList.length)).children("td").eq(0).attr("rowspan",secSkuList.length*(thirdSkuAppendTotal+thirdSkuList.length));
                    firstOffset++;
                });
                break;
        }
        $("#wdmaskustock").show();
    }
    function showdiv(){
        var my = document.getElementByName("ax_grid");
        if (my != null)
            my.parentNode.removeChild(my);
    }
    //删除规格项
    function deleteSku(obj){
        var skuOrder = $(obj).parents(".ma-sku-group").index() + 1;
        var skuItemOrder = $(obj).parents(".sub-sku-item").index() - 1;
        switch(skuOrder){
            case 1:
                var firstSkuListLength = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item").length;
                var secSkuListLength = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item").length;
                var thirdSkuListLength = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item").length;

                if(firstSkuListLength==1){
                    deleteSkuGroup(obj);
                    $(obj).parents(".ma-sku-group").next(".ma-sku-group").find('.sub-sku-item').each(function(){
                        $(this).remove();
                    });
                    $(obj).parents(".ma-sku-group").next(".ma-sku-group").next(".ma-sku-group").find('.sub-sku-item').each(function(){
                        $(this).remove();
                    });
                }else{
                    if(secSkuListLength==0){
                        var offset = skuItemOrder;
                        $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();
                    }else{
                        if(thirdSkuListLength==0){
                            var range = secSkuListLength;
                            var offset = skuItemOrder * secSkuListLength;
                            for(var i=0;i<range;i++){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();
                            }
                        }else{
                            var range = secSkuListLength * thirdSkuListLength;
                            var offset = skuItemOrder * range;
                            for(var i=0;i<range;i++){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();
                            }
                        }
                    }


                }
                break;
            case 2:
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格
                var thirdSkuList = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item");//三级规格
                var firstSkuListLength = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item").length;
                var secSkuListLength = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item").length;
                var thirdSkuListLength = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item").length;
                if(secSkuListLength==1){
                    deleteSkuGroup(obj);
                    $(obj).parents(".ma-sku-group").next(".ma-sku-group").find('.sub-sku-item').each(function(){
                        $(this).remove();
                    })
                }else{
                    for(var i=firstSkuListLength;i>=0;i--){
                        if(thirdSkuListLength==0){
                            var offset = secSkuListLength * i + skuItemOrder;
                            if(skuItemOrder==0){
                                var tmpTD = $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0);
                            }
                            $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();
                            if(skuItemOrder==0){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0).before(tmpTD);
                            }
                            $("#wdmaskustock .table-sku-stock tbody tr").eq(i*secSkuListLength).children("td").eq(0).attr("rowspan",(secSkuListLength - 1));
                        }else{
                            var range = thirdSkuListLength;
                            var offset = secSkuListLength * thirdSkuListLength * i + skuItemOrder * thirdSkuListLength;
                            for(var n=0;n<range;n++){
                                if(skuItemOrder==0 && n==0){
                                    var tmpTD = $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0);
                                }
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();
                            }
                            if(skuItemOrder==0){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(i*secSkuListLength*thirdSkuListLength).children("td").eq(0).before(tmpTD);
                            }
                            $("#wdmaskustock .table-sku-stock tbody tr").eq(i*secSkuListLength*thirdSkuListLength).children("td").eq(0).attr("rowspan",(secSkuListLength - 1)*thirdSkuListLength);
                        }
                    }
                }
                break;
            case 3:
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格
                var thirdSkuList = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item");//三级规格
                var firstSkuListLength = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item").length;
                var secSkuListLength = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item").length;
                var thirdSkuListLength = $(".ma-sku-group .sub-sku-list").eq(2).find(".sub-sku-item").length;

                if(thirdSkuListLength==1){
                    deleteSkuGroup(obj);
                }else{

                    for(var i=firstSkuListLength-1;i>=0;i--){

                        for(var j=secSkuListLength-1;j>=0;j--){
                            var offset = secSkuListLength * thirdSkuListLength * i + j * thirdSkuListLength + skuItemOrder;
                            var indexSec = j==0?1:0;

                            if(skuItemOrder==0){
                                if(j==0){
                                    var tmpTD_first_sku = $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0);
                                    var tmpTD_sec_sku = $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(1);
                                }else if(j>0){
                                    var tmpTD_sec_sku = $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0);
                                }

                            }

                            $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).remove();

                            if(skuItemOrder==0){
                                $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0).before(tmpTD_sec_sku);
                                if(j==0){
                                    $("#wdmaskustock .table-sku-stock tbody tr").eq(offset).children("td").eq(0).before(tmpTD_first_sku);
                                }

                            }
                            $("#wdmaskustock .table-sku-stock tbody tr").eq(offset-skuItemOrder).children("td").eq(indexSec).attr("rowspan",thirdSkuListLength - 1);
                        }
                        $("#wdmaskustock .table-sku-stock tbody tr").eq(secSkuListLength * thirdSkuListLength * i).children("td").eq(0).attr("rowspan",secSkuListLength*(thirdSkuListLength - 1));

                    }
                }
                break;
        }

        countStock();
        $("#wdmaskustock").show();
    }
    //删除规格组
    function deleteSkuGroup(obj){
        var skuOrder = $(obj).parents(".ma-sku-group").index() + 1;
        switch(skuOrder){
            case 1:
                $("#wdmaskustock table thead th").each(
                        function(){
                            if(!$(this).hasClass("pin"))
                                $(this).remove();
                        }
                );
                $("#wdmaskustock table tbody").html("");
                break;
            case 2:
                $("#wdmaskustock table tbody").html("");
                if(!$("#wdmaskustock table thead th").eq(2).hasClass("pin")){
                    $("#wdmaskustock table thead th").eq(2).remove();
                }
                if(!$("#wdmaskustock table thead th").eq(1).hasClass("pin")){
                    $("#wdmaskustock table thead th").eq(1).remove();
                }
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                firstSkuList.each(function(){
                    var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                    $("#wdmaskustock .table-sku-stock tbody").append(trSku);
                });
                break;
            case 3:
                $("#wdmaskustock table tbody").html("");
                if(!$("#wdmaskustock table thead th").eq(2).hasClass("pin")){
                    $("#wdmaskustock table thead th").eq(2).remove();
                }
                var firstSkuList = $(".ma-sku-group .sub-sku-list").eq(0).find(".sub-sku-item");//一级规格
                var secSkuList = $(".ma-sku-group .sub-sku-list").eq(1).find(".sub-sku-item");//二级规格
                secSkuListLength = secSkuList.length;
                var firstOffset = 0;
                firstSkuList.each(function(){
                    var firstSkuItem = this;
                    var secOffset = 0;
                    secSkuList.each(function(){

                        var trSku = $("#goodsSkuStockTpl").html().replace("skuname",$(this).find("span").text());
                        $("#wdmaskustock .table-sku-stock tbody").append(trSku);
                        if(secOffset==0){
                            $("#wdmaskustock .table-sku-stock tbody tr").eq(firstOffset*secSkuListLength).prepend("<td rowspan='"+secSkuListLength+"'>"+$(firstSkuItem).find("span").text()+"</td>");
                        }
                        secOffset++;
                    });
                    firstOffset++;
                });
                break;
        }
    }
    // js限制必须输入金额
    /**
     * 实时动态强制更改用户录入
     * arg1 inputObject
     **/
    function amounts(th){
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
    function overFormats(th){
        var v = th.value;
        if(v === ''){
            v = '';
        }else if(v === '0'){
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
    Array.prototype.unique = function(){
        var res = [];
        var json = {};
        for(var i = 0; i < this.length; i++){
            if(!json[this[i]]){
                res.push(this[i]);
                json[this[i]] = 1;
            }
        }
        return res;
    }
    function in_array(search,array){
        for(var i in array){
            if(array[i]==search){
                return true;
            }
        }
        return false;
    }

    //计算总库存
    function countStock(){
        var stock_count = 0;
        $(".js-stock-num").each(function(){
            if(!isNaN(parseInt($(this).val()))) {
                stock_count += parseInt($(this).val());
            }
        });
        if(isNaN(stock_count)){stock_count = 0 ;}
        $("#stock").val(stock_count);
    }
</script>
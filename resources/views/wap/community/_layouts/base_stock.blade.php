<style>
    .popup.modal-in {
        -webkit-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
    }
    .popup.modal-in {
        -webkit-transition-duration: .4s;
        transition-duration: .4s;
    }
    .popup-transparent {
        background: transparent!important;
    }
    .y-closebg {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    .y-probox {
        width: 100%;
        /* max-height: 75%; */
        position: absolute;
        left: 0;
        bottom: 0;
        padding-bottom: 3rem;
    }
    .y-probox .y-prott {
        padding-bottom: 1rem;
        margin-bottom: .65rem;
        margin-left: .5rem;
    }
    .y-probox .y-propic {
        width: 3.65rem;
        height: 3.65rem;
        border-radius: .25rem;
        overflow: hidden;
        margin-top: -.5rem;
        float: left;
        z-index: 100;
    }
    .y-probox .y-propic {
        background: #fff;
        border: 1px solid #f5f5f5;
    }
    .y-propic img {
        width: 100%;
        min-height: 100%;
    }
    .y-probox .y-prottr {
        margin-left: 4.4rem;
        line-height: 1rem;
        position: relative;
    }
    .y-probox .y-closeico {
        position: absolute;
        top: .5rem;
        right: .5rem;
        font-size: 1.15rem;
    }
    .y-psize span {
        line-height: 1.5rem;
        padding: 0 .75rem;
        border-radius: .15rem;
        font-size: .6rem;
        margin: .25rem .9rem 0 0;
        display: inline-block;
    }
    .y-psize span {
        border: 1px solid #f5f5f5;
        background-color: #f5f5f5;
    }
    .y-probox .y-prott {
        padding-bottom: 1rem;
        margin-bottom: .65rem;
        margin-left: .5rem;
    }
    .y-closebg .y-pbtn {
        margin-top: 25px;
        position: absolute;
        bottom: 0;
        width: 100%;
    }
    .y-closebg .y-pbtn button {
        width: 100%;
        line-height: 2.25rem;
    }
    .y-closebg .y-num .num {
        width: 1.2rem;
        display: inline-block;
        height: 30px;
    }
    .y-closebg .y-num .icon {
        font-size: .8rem;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        border-radius: .15rem;
    }
    @if(!$option)
    @endif
</style>
<script src="{{ asset('js/dot.js') }}"></script>
<script type="text/tpl" id="goodsSkusTpl">
<div class="popup popup-transparent popup-skus">
    <div class="content-block">
        <div class="f-bgtk size-frame">
            <div class="y-closebg">
                <div class="y-probox c-bgfff">
                    <div class="y-prott">
                        <div class="y-propic pr" style="z-index:100;">
                            <img class="sku-image" src="@{{=it.goods.logo}}" width='75px' />
                        </div>
                        <div class="y-prottr">
                            <p class="c-red pt5 f18">￥<span class="sku-price">@{{=it.goods.price}}</span>
                            @{{? it.goods.discount_price != it.goods.price || it.goods.discount_price == 0.01 }}
                                {{--<del class="c-gray f14">￥<span class="other-price">@{{=it.goods.price}}</span></del>--}}
                            @{{?}}</p>
                            <span class="f12 c-gray">库存：</span><span class="f12 c-gray sku-stock">@{{=it.goods.stock}}</span>
                            <p class="f12 skus-tip"></p>
                            <i class="icon iconfont y-closeico c-gray" onclick="$.closeModal('.popup-skus');">&#xe604;</i>
                        </div>
                    </div>
                    @{{? it.skus.length > 0 }}
                    <div class="y-tcmaxheight">
                    @{{~it.skus :sku:skuIndex}}
                    <div class="y-prott">
                        <p class="f14">@{{=sku.name}}</p>
                        <div class="y-psize clearfix c-gray f12 skus" data-index="@{{=skuIndex}}">
                            @{{~sku.items :item:itemIndex}}
                            <span data-id="@{{=item.id}}" class="sku-item">@{{=item.name}}</span>
                            @{{~}}
                        </div>
                    </div>
                    @{{~}}
                    </div>
                    @{{?}}
                    <div class="x-pnum pl10 pr10">
                        <span class="f14">购买数量</span>
                        <div class="fr y-num">
                            <i class="icon iconfont subtractXmm">&#xe621;</i>
                            <input class="num tc pl0" value="1" readonly="readonly">
                            <i class="icon iconfont addXmm">&#xe61e;</i>
                        </div>
                    </div>
                    <div class="y-pbtn c-white">
                        <button class="f16 c-bg submit-sku">确 定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>
<script>
    var currentGoodsSkus;
    var selectedSkuData;
    var selectedSkuType;
    var skuSelectedTip;
    var isSubmitSkuSelect;
    var isSubmitSkuUrl;

    $.showGoodsSkus = function(id, type,url) {
        selectedSkuData = new Object();
        selectedSkuData.goodsId = id;
        selectedSkuData.num = 1;
        selectedSkuData.skuSn = '';
        selectedSkuData.shareUserId = "{{$option['shareUserId']}}";
        @if($option['id'])
        selectedSkuData.sellerId = "{{$option['id']}}";
        @else
        selectedSkuData.sellerId = "{{$seller['id']}}";
        @endif
        selectedSkuData.type = "{{$option['type']}}";
        selectedSkuType = type;
        isSubmitSkuUrl = url;
        isSubmitSkuSelect = false;
//        $.showPreloader('加载中···');
//        $.hidePreloader();
        $.post("{{ u('Goods/skus') }}", {id:id}, function(result) {
            result = $.parseJSON(result);
            if (result.code == 0) {
                $.popup(doT.template($("#goodsSkusTpl").html()).apply(null,[result.data]), true);

                currentGoodsSkus = result.data;
                currentGoodsSkus.items = new Object();

                $.each(currentGoodsSkus.skus, function(index, sku){
                    $.each(sku.items, function(index, item){
                        currentGoodsSkus.items['sku' + item.id] = item;
                    });
                    delete currentGoodsSkus.skus[index].items;
                });

                var stocks = new Object();
                $.each(currentGoodsSkus.stocks, function(index, stock){
                    stocks[stock.sn] = stock;
                });
                currentGoodsSkus.stocks = stocks;
            } else {
                $.toast("获取商品规格失败");
            }
        }, function(){
            $.toast("获取商品规格失败");
        }, 'json' , true);
    }
    $.updateSelectdGoodsSkus = function() {
        var unselect = '';
        var selected = '';
        var skuSn = '';
        var image = currentGoodsSkus.goods.image;
        skuSelectedTip = '';

        if ($(".popup-skus .skus").length < 1) {
            return;
        }

        $(".popup-skus .skus").each(function(){
            var selectSku = $(this).find('.active');
            if (selectSku.length > 0) {
                var skuId = selectSku.data('id');
                selectSku = currentGoodsSkus.items['sku' + skuId];
                skuSn += (skuSn == '' ? '' : ':') + skuId;
                selected += '<span>' + selectSku.name + '&nbsp</span>';
            } else {
                var skuGroup = currentGoodsSkus.skus[$(this).data("index")];
                unselect += '<span>' + skuGroup.name + '&nbsp</span>';
                skuSelectedTip += '&nbsp;' + skuGroup.name;
            }
        });
        if (unselect != '') {
            selectedSkuData.skuSn = '';
            $('.popup-skus .skus-tip').html('<span>请选择：</span>' + unselect);
        } else {
            selectedSkuData.skuSn = skuSn;
            var selectedStock = currentGoodsSkus.stocks[skuSn];
            var goodsNum = $(".num").val();
            $('.popup-skus .skus-tip').html('<span>已选择：</span>' + selected);
            if(goodsNum >= selectedStock.stock_count){
                $('.popup-skus .num').val(selectedStock.stock_count);
                selectedSkuData.num  = selectedStock.stock_count;
            } else if(goodsNum == 0) {
                $('.popup-skus .num').val(1);
                selectedSkuData.num  = 1;
            }
            if(selectedStock.stock_count == 0){
                $(".submit-sku").removeClass("c-bg");
            } else {
                $(".submit-sku").addClass("c-bg");
            }

            $(".popup-skus .sku-stock").html(selectedStock.stock_count);
            $(".popup-skus .sku-price").html(selectedStock.price);
            $(".popup-skus .other-price").html(selectedStock.price);
        }
    }

    $.checkSkuHandler = function() {
        if("{{$loginUserId or 0}}" == 0){
            $.toast("请登录");
            $.href("{{u('User/login')}}");
            return false;
        }
        if (isSubmitSkuSelect) {
            return false;
        }
        if (currentGoodsSkus.skus.length > 0 && selectedSkuData.skuSn == '') {
            $.toast("请选择" + skuSelectedTip);
            return false;
        }
        return true;
    }

    $(document).off('opened','.popup-skus');
    $(document).on('opened','.popup-skus', function () {
        $.updateSelectdGoodsSkus();
    });

    $(document).off('click','.popup-skus .sku-item');
    $(document).on('click','.popup-skus .sku-item', function () {
        if (isSubmitSkuSelect) {
            return false;
        }

        $(this).addClass("c-bg c-white active").siblings().removeClass("c-bg c-white active");
        $.updateSelectdGoodsSkus();
    });

    $(document).off('click','.popup-skus .addXmm');
    $(document).on('click','.popup-skus .addXmm', function () {
        if (!$.checkSkuHandler()) {
            return;
        }
        var stock;
        if (selectedSkuData.skuSn != '') {
            stock = currentGoodsSkus.stocks[selectedSkuData.skuSn].stock_count;
        } else {
            stock = currentGoodsSkus.goods.stock;
        }

        if (selectedSkuData.num < stock) {
            selectedSkuData.num++;
            $('.popup-skus .num').val(selectedSkuData.num);
        } else {
            $.toast("库存不足，剩余数量："+ stock);
            return;
        }
    });

    $(document).off('click','.popup-skus .subtractXmm');
    $(document).on('click','.popup-skus .subtractXmm', function () {
        if (!$.checkSkuHandler()) {
            return;
        }

        if (selectedSkuData.num > 1) {
            selectedSkuData.num--;
            $('.popup-skus .num').val(selectedSkuData.num);
        }
    });

    $(document).off('click','.popup-skus .submit-sku');
    $(document).on('click','.popup-skus .submit-sku', function () {
        if (!$.checkSkuHandler()) {
            return;
        }
        $.post("{{u('Goods/saveCart')}}", selectedSkuData, function(res){
            if(res.code == 0){
                $.closeModal('.popup-skus');
                if(selectedSkuType == 'cart'){
                    $.toast(res.msg);
                }else if(selectedSkuType == 'cartByShow'){
                    $.toast(res.msg);
                    if(isSubmitSkuUrl != undefined){
                        $.href("{{u('GoodsCart/index')}}");
                    }
                    $(".page-current #DsySale").html(res.data.sale);
                    $(".page-current #cartTotalAmount").html(res.data.totalAmount);
                    $(".page-current #cartTotalPrice").html(res.data.totalPrice);
                    $("#dsyShowUl ul").html("");
                    $.each(res.data.list,function(i,v){
                        $.each(v.goods,function(is,goods){
                            var html = $("#dsyHtml").html();
                            var normsName = goods.normsName;
                            if(goods.normsName){
                                normsName = goods.normsName.replace(/:/g, '-');
                            }
                            var normsSkuSn = '';
                            var goodsSkuSnItem = '';
                            if(goods.skuSn){
                                normsSkuSn = goods.skuSn.replace(/:/g, '_');
                                goodsSkuSnItem = goods.skuSn;
                            }
                            html = html.replace('GOODSId',goods.goodsId)
                                    .replace('GOODSId',goods.goodsId)
                                    .replace('GOODSId',goods.goodsId)
                                    .replace('GOODSId',goods.goodsId)
                                    .replace('GOODSIDS',goods.goodsId)
                                    .replace('NORMSID',normsSkuSn)
                                    .replace('NORMSID',normsSkuSn)
                                    .replace('NORMSID',normsSkuSn)
                                    .replace('NORMSIDS',goodsSkuSnItem)
                                    .replace('SALEPRICE',goods.price * (res.data.sale/10))
                                    .replace('DSYPRICE',goods.price)
                                    .replace('SERVICRTIME',0)
                                    .replace('MONERY',goods.price * goods.num)
                                    .replace('NUM',goods.num);
                            if(goods.normsName){
                                html = html.replace('NAME',goods.name+"【"+normsName+"】");
                            }else{
                                html = html.replace('NAME',goods.name);
                            }
                            $("#dsyShowUl ul").prepend(html);
                        });
                    });
                    @if($option['type'] == 1)
                    var serviceFee = "{{ $seller['serviceFee'] }}";
                    if (res.data.totalPrice < serviceFee) {
                        var differFee = parseFloat(serviceFee) - parseFloat(res.data.totalPrice);
                        $(".choose_complet").removeClass("c-bg").addClass("c-gray97").html("还差￥" + differFee.toFixed(2));
                    } else {
                        $(".choose_complet").removeClass("c-gray97").addClass("f16 c-bg").html("选好了");
                    }
                    @endif
                }else{
                    $.href("{{u('Order/order')}}?cartIds="+res.data.cartIds);
                }
            }else if(res.code == -1){
                $.toast('请先登录!');
                $.router.load("{{u('User/login')}}", true);
                return;
            }else{
                $.toast(res.msg);
            }
        });
    });
</script>
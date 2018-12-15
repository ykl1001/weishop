<div class="bar bar-footer y-shopbtm">
    <div class="x-cart pr mr10 y-cart">
        <div>
            <i class="icon iconfont c-white">&#xe673;</i>
            <span class="badge pa c-bgfff c-red" id="cartTotalAmount">{{ $cart['totalAmount'] }}</span>
        </div>
    </div>
    <div class="y-shopmoney">
        <span class="c-white f18 y-maxw40">￥<span id="cartTotalPrice">{{number_format($cart['totalPrice'], 2) }}</span></span>
        <span class="f12 pl5 ml5 y-reduc">已减<span id="DsySale">{{number_format($cart['sale'], 2) }}</span>元</span>
    </div>
    @if ($cart['totalPrice'] < $seller['serviceFee'])
        <a class="x-menuok c-gray97 c-white f13 fr choose_complet" href="javascript:$.href('{{u('GoodsCart/index',['id'=>Input::get('id'),'type'=>Input::get('type'),'t'=>rand()])}}')">还差￥{{number_format($seller['serviceFee'] - $cart['totalPrice'], 2) }}</a>
    @else
        <a class="x-menuok c-bg c-white f16 fr choose_complet" href="javascript:$.href('{{u('GoodsCart/index',['id'=>Input::get('id'),'type'=>Input::get('type'),'t'=>rand()])}}')">选好了</a>
    @endif
</div>
<div class="f-bgtk size-frame none showAlaertCartDsy">
    <div class="x-closebg">
    </div>
    <div class="y-shoppingbox">
        <div class="y-shoppingtop f14 c-gray5"><span>购物车</span><span class="fr y-delall"><i class="icon iconfont vat mr5">&#xe630;</i>删除全部</span></div>
        <div class="list-block media-list m0 y-shoppinglist f12" id="dsyShowUl">
            <ul>
                @foreach($cart['data']['goods'] as $dsyK => $dsyV)
                    <li class="item-content dsyId-{{ str_replace(':','_',$dsyV['skuSn'])}}" id="dsyId-{{$dsyV['goodsId']}}">
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title">{{$dsyV['name']}}@if($dsyV['normsName'])【{{$dsyV['normsName']}}】@endif</div>
                                <div class="item-after">
                                    <span class="c-red fl">
                                        ￥<span  id="cartTotalPrice_DsyPrice_{{$dsyV['goodsId']}}" class="cartTotalPrice_DsyPrice_{{ str_replace(':','_',$dsyV['skuSn'])}}">
                                            @if( $dsyV['sale'] == 10 )
                                                {{number_format($dsyV['num'] * $dsyV['price'], 2)}}
                                            @else
                                                {{number_format($dsyV['num'] * $dsyV['price'] * ($dsyV['sale']/10), 2)}}
                                            @endif
                                        </span>
                                    </span>
                                    <div class="x-num fr ml5">
                                        <i class="icon iconfont c-gray subtract fl">&#xe622;</i>
                                        <span class="val tc pl0 fl cartTotalPrice_DsyNum_{{ str_replace(':','_',$dsyV['skuSn'])}}" data-newold="true" data-goodsid="{{$dsyV['goodsId']}}" data-normsid="{{ $dsyV['skuSn'] }}" data-price="{{ $dsyV['price'] }}" data-saleprice="{{ ($dsyV['sale'] * $dsyV['price'] / 10)  }}" data-servicetime="{{$dsyV['serviceTime'] or 0}}" id="cartTotalPrice_DsyNum_{{$dsyV['goodsId']}}">{{$dsyV['num']}}</span>
                                        <i class="icon iconfont c-red add fl">&#xe61f;</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<script type="text/tpl" id="dsyHtml">
    <li class="item-content dsyId-NORMSID" id="dsyId-GOODSId">
        <div class="item-inner">
            <div class="item-title-row">
                <div class="item-title">NAME</div>
                <div class="item-after">
                    <span class="c-red">￥<span  id="cartTotalPrice_DsyPrice_GOODSId" class="cartTotalPrice_DsyPrice_NORMSID">MONERY</span></span>
                    <div class="x-num fr ml5">
                        <i class="icon iconfont c-gray subtract fl">&#xe622;</i>
                        <span class="val tc pl0 fl cartTotalPrice_DsyNum_NORMSID" data-newold="true" id="cartTotalPrice_DsyNum_GOODSId"  data-goodsid="GOODSIDS" data-normsid="NORMSIDS" data-price="DSYPRICE" data-saleprice="SALEPRICE" data-servicetime="SERVICRTIME">NUM</span>
                        <i class="icon iconfont c-red add fl">&#xe61f;</i>
                    </div>
                </div>
            </div>
        </div>
    </li>
</script>
<script type="text/javascript">
    Zepto(function($){
        $(document).off("click",".x-cart");
        $(document).on("click",".x-cart",function(){
            var badgeval = $(this).find(".badge").text();
            if(!$(this).hasClass("active") && badgeval > 0){
                $(this).addClass("active");
                $(".page-current .showAlaertCartDsy").removeClass("none");
            }else{
                $(this).removeClass("active");
                $(".page-current .showAlaertCartDsy").addClass("none");
            }
            $(".y-shoppinglist").css("max-height",$(".y-shoppinglist li").height()*7);
        })
        $(document).off("click",".y-delall");
        $(document).on("click",".y-delall",function(){
            $.confirm("确认清空购物车吗？", function(){
                //加载提示
                $.showPreloader("正在清空购物车<br/>请稍候...");
                $.post("{{u('Goods/cartDelete')}}", { sellerId: {{ $option['id']}} ,type: {{ $option['type']}} }, function(){
                    $.hidePreloader();
                    $(".page-current #dsyShowUl ul").html("");

                    $(".page-current .size-frame").addClass("none");
                    $(".page-current #DsySale").html("0.00");
                    $(".page-current #cartTotalAmount").html(0);
                    $(".page-current #cartTotalPrice").html("0.00");
                    $(".page-current .subtract").addClass("none");
                    $(".page-current .val").addClass("none").html(0);
                    if($(".page-current .y-ggpsize li").length > 1){
                        $(".page-current .y-ggpsize li").attr("data-ns",0);
                    }
                    var serviceFee = "{{ $seller['serviceFee'] }}";
                    var totalPrice = 0;
                    if (totalPrice < serviceFee) {
                        var differFee = parseFloat(serviceFee) - parseFloat(totalPrice);
                        $(".choose_complet").removeClass("c-bg").removeClass("f16").addClass("c-gray97").addClass("f13").html("还差￥" + differFee.toFixed(2));
                    } else {
                        $(".choose_complet").removeClass("c-gray97").addClass("c-bg").addClass("f16").removeClass("f14").html("选好了");
                    }
                });
            },function () {
                $(".modal-overlay").removeClass("modal-overlay-visible");
            });
        })
        $(document).off("click",".x-closebg");
        $(document).on("click",".x-closebg",function(){
            $(".x-cart").removeClass("active");
            $(".f-bgtk").addClass("none");
        });
    });
    // 计算合计
    function CalculationTotal(goodsid, normsId, nums, price,newGoods,newold)
    {

        if (typeof(cartgoods[goodsid]) == "undefined")
        {
            cartgoods[goodsid] = new Object();
        }
        var newNormId = 0;
        if(normsId != 0 || normsId != ''){
            newNormId = normsId.replace(/:/g, '_');
        }
        cartgoods[goodsid][normsId] = { num: nums, price: price };

        var totalAmount = 0;

        var totalPrice = 0.0;

        for(var goods in cartgoods)
        {
            delete cartgoods[goodsid][0]; //cz
            for (var item in cartgoods[goods])
            {
                totalAmount += parseInt(cartgoods[goods][item].nums);

                totalPrice += cartgoods[goods][item].nums * cartgoods[goods][item].price;
            }
        }
        if(newold == true || newold =="true"){
            if(newNormId != 0){
                $(".show_item_norms_"+goodsid+" .show_item_id_"+newNormId).attr("data-ns",nums);
            }else{
                $(".goodsId_show"+goodsid+" span").html(nums);
            }
            if(nums == 0) {
                $(".goodsId_show"+goodsid+" .subtract").addClass("none");
                $(".goodsId_show"+goodsid+" span").addClass("none");

            }else{
                $(".goodsId_show"+goodsid+" .subtract").removeClass("none");
                $(".goodsId_show"+goodsid+" span").removeClass("none");
            }
        }
        if(nums <= 0) {
            if(newNormId != 0){
                $(".page-current li.dsyId-"+newNormId).remove();
            }else{
                $(".page-current li#dsyId-"+goodsid).remove();
            }
        }
        else{
            if(newNormId != 0){
                var has = $(".page-current li.dsyId-"+newNormId);
            }else{
                var has = $(".page-current li#dsyId-"+goodsid);
            }
            //修改
            if(has.html()){
                var money = price*nums;

                var moneys = 0;
                if(money == 0 ){
                    moneys  = "0.00";
                }else{
                    moneys = money.toFixed(2)
                }
                if(newNormId != 0){
                    $(".page-current li#dsyId-"+goodsid+" .cartTotalPrice_DsyPrice_"+newNormId).html(moneys);
                    $(".page-current li#dsyId-"+goodsid+" .cartTotalPrice_DsyNum_"+newNormId).html(nums);
                }else{
                    $(".page-current #cartTotalPrice_DsyPrice_"+goodsid).html(moneys);
                    $(".page-current #cartTotalPrice_DsyNum_"+goodsid).html(nums);
                }
            }
            //追加
            else{
                var newGoodss = [];
                $.each(newGoods.data.list,function(ks,vs){
                    $.each(vs.goods,function(k,v){
                        if(v.goodsId == goodsid){
                            newGoodss['name'] = v.name;
                            newGoodss['num'] = v.num;
                            newGoodss['price'] = v.price;
                            newGoodss['goodsId'] = v.goodsId;
                            newGoodss['sale'] = v.sale;
                            newGoodss['servicetime'] = v.serviceTime;
                            newGoodss['normsId'] = v.skuSn;
                            return false;
                        }
                    });
                });
                var money =  0;
                if(newGoodss['sale'] == 10){
                    money = newGoodss['price'] * newGoodss['num'];
                }else{
                    money = newGoodss['price'] * newGoodss['num'] * (newGoodss['sale']/10);
                }
                var moneys = 0;
                if(money == 0 ){
                    moneys  = "0.00";
                }else{
                    moneys = money.toFixed(2)
                }
                var html = $("#dsyHtml").html()
                        .replace('GOODSId',goodsid)
                        .replace('GOODSId',goodsid)
                        .replace('GOODSId',goodsid)
                        .replace('GOODSId',goodsid)
                        .replace('GOODSIDS',goodsid)
                        .replace('NORMSID',newNormId ? newNormId : 0)
                        .replace('NORMSID',newNormId ? newNormId : 0)
                        .replace('NORMSID',newNormId ? newNormId : 0)
                        .replace('NORMSIDS',normsId ? normsId : 0)
                        .replace('SALEPRICE',newGoodss['price'] * (newGoodss['sale']/10))
                        .replace('DSYPRICE',newGoodss['price'])
                        .replace('SERVICRTIME',newGoodss['servicetime'] ? newGoodss['servicetime'] : 0 )
                        .replace('NAME',newGoodss['name'])
                        .replace('MONERY',moneys)
                        .replace('NUM',newGoodss['num']);
                $("#dsyShowUl ul").prepend(html);
            }
        }
        $(".page-current #DsySale").html(newGoods.data['sale'].toFixed(2));
        $(".page-current #cartTotalAmount").html(newGoods.data['totalAmount']);
        $(".page-current #cartTotalPrice").html(newGoods.data['totalPrice'].toFixed(2));

        if(newGoods.data['totalAmount'] == 0){
            $(".size-frame").addClass("none");
        }

        if(totalAmount == 0){
            $(".size-frame").addClass("none");
        }
        var serviceFee = "{{ $seller['serviceFee'] }}";
        if (newGoods.data['totalPrice'] < serviceFee) {
            var differFee = parseFloat(serviceFee) - parseFloat(newGoods.data['totalPrice']);
            $(".choose_complet").removeClass("c-bg").removeClass("f16").addClass("c-gray97").addClass("f13").html("还差￥" + differFee.toFixed(2));
        } else {
            $(".choose_complet").removeClass("c-gray97").addClass("c-bg").addClass("f16").removeClass("f14").html("选好了");
        }
    }
</script>
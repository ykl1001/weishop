<!-- 已转H5 -->
@if($seller['serviceTimesCount'] > 0)
<div class="bar bar-footer">
    <span class="x-cart pr mr10">
        <i class="icon iconfont c-gray">&#xe673;</i>
        <span class="badge pa c-bg c-white total_amount" id="cartTotalAmount">{{ $cart['totalAmount'] }}</span>
    </span>
    <span class="f14 c-gray">总价:￥</span>
    <span class="c-red f18" id="cartTotalPrice">{{number_format($cart['totalPrice'], 2) }}</span>
    @if ($cart['totalPrice'] < $seller['serviceFee'])
        <a class="x-menuok c-gray97 c-white f16 fr choose_complet" href="javascript:$.href('{{u('GoodsCart/index',['id'=>Input::get('id'),'type'=>Input::get('type'),'t'=>rand()])}}')">还差￥{{ $seller['serviceFee'] - $cart['totalPrice']}}</a>
    @else
        <a class="x-menuok c-bg c-white f16 fr choose_complet" href="javascript:$.href('{{u('GoodsCart/index',['id'=>Input::get('id'),'type'=>Input::get('type'),'t'=>rand()])}}')">选好了</a>
    @endif
</div>
@else
    <div class="bar bar-footer c-white c-blackbg">
        <i class="icon iconfont mr5">&#xe645;</i>商户休息中 不接受任何订单
    </div>
 @endif
<script>
function CalculationTotal(goodsid, normsId, num, price)
{
    if (typeof(cartgoods[goodsid]) == "undefined")
    {
        cartgoods[goodsid] = new Object();
    }

    if (normsId == "0") normsId = "null";

        cartgoods[goodsid][normsId] = { num: num, price: price };

        var totalAmount = 0;

        var totalPrice = 0.0;

        for(var goods in cartgoods)
        {
        delete cartgoods[goodsid][0]; //cz
        for (var item in cartgoods[goods])
        {
        totalAmount += parseInt(cartgoods[goods][item].num);

        totalPrice += cartgoods[goods][item].num * cartgoods[goods][item].price;
        }
        }

        $("#cartTotalAmount").html(totalAmount);

        $("#cartTotalPrice").html(totalPrice.toFixed(2));

        var serviceFee = "{{ $seller['serviceFee'] }}";
        if (totalPrice < serviceFee) {
        var differFee = parseFloat(serviceFee) - parseFloat(totalPrice);
            $(".choose_complet").removeClass("c-bg").addClass("c-gray97").html("还差￥" + differFee.toFixed(2));
    } else {
             $(".choose_complet").removeClass("c-gray97").addClass("c-bg").html("选好了");
    }
}
</script>
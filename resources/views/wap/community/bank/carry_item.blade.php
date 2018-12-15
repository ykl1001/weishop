<div class="list-block m0 nobor">
    <ul>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-title">可提现金额</div>
                <div class="item-after c-red"><span class="f_red mr5" id="is_carry_money" data="{{$bank['moneyCycle'] or 0}}">¥{{$bank['moneyCycle'] or 0}}</span></div>
            </div>
        </li>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-title">提现金额</div>
                <div class="item-after">
                    <input class="f14 y-txinput" style="border: 0" @if(!$bank['lockCycl'])@if($bank['moneyCycle'] <= 100) readonly="readonly" @else @endif @else @endif  name="carry_money" value="" placeholder="@if(!$bank['lockCycl'])@if($bank['moneyCycle'] <= 100)暂无可提现金额@else请输入提现金额@endif @else请输入提现金额@endif" onkeyup="this.value=this.value.replace(/\\D/g,'')" onafterpaste="this.value=this.value.replace(/\\D/g,'')"/>
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="account_hd_bottom content-padded">
    @if(!$bank['lockCycl'])
        <p class="mb10">
            您下次可提现日期: {{$bank['moneyCycleDay']}}
        </p>
		<p class="y-paybtn f16 y-paybtn_ccc">申请提现</p>	
    @else
        <p class="y-paybtn f16 ajax-success-bnt">申请提现</p>
    @endif
</div>
<div class="list-block">
    <ul>
        <li class="item-content">
            <div class="item-inner">
                @if($bank['bank']['bank'])
                    <div class="item-title">已绑定银行卡：<span>{{$bank['bank']['bank']}}({{ $bank['bank']['bankNo'] ? substr($bank['bank']['bankNo'],-4) : null }}) </span></div>
                    <div class="item-after"><a href="#" onclick="$.href('{{u('Bank/bank',['id'=>$bank['bank']['id']])}}')"  class="c-red">详情></a></div>
                @else
                    <div class="item-title">暂无银行卡</div>
                    <div class="item-after"><a href="#" onclick="$.href('{{u('Bank/bank')}}')"  class="c-red">去绑定></a></div>
                @endif
            </div>
        </li>
    </ul>
</div>
<div class="list-block">
    <ul>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-title f15">说明</div>
            </div>
        </li>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-text y-itemtext">
                    {!! $bank['notice'] !!}
                </div>
            </div>
        </li>
    </ul>
</div>
<script type="text/javascript">
    $(".page-current input[id='carry_money']").attr("maxlength","11").attr("onkeyup", "this.value=this.value.replace(/\\D/g,'')").attr("onafterpaste", "this.value=this.value.replace(/\\D/g,'')");
</script>
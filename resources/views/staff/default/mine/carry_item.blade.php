<div class="list-block">
    <ul>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-title">可提现金额</div>
                <div class="item-after"><span class="f_red mr5" id="is_carry_money" data="{{$staff['extend']['withdrawMoney'] or 0}}">¥{{$staff['extend']['withdrawMoney'] or 0}}</span></div>
            </div>
        </li>
        <li class="item-content">
            <div class="item-inner">
                <div class="item-title">提现金额</div>
                <div class="item-after">
                    <input class="f14 y-txinput" style="border: 0" @if(!$bank['lockCycl'])@if($staff['extend']['withdrawMoney'] <= 100) readonly="readonly" @else @endif @else @endif  name="carry_money" value="" placeholder="@if(!$bank['lockCycl'])@if($staff['extend']['withdrawMoney'] <= 100)暂无可提现金额@else请输入提现金额@endif @else请输入提现金额@endif" onkeyup="this.value=this.value.replace(/\\D/g,'')" onafterpaste="this.value=this.value.replace(/\\D/g,'')"/>
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="account_hd_bottom content-padded">
    <p class="button button-fill button-success ajax-success-bnt2">申请提现</p>
</div>
<div class="list-block">
    <ul>
        <li class="item-content">
            <div class="item-inner">
                @if($bank['bank'])
                    <div class="item-title">已绑定银行卡：<span>{{$bank['bank']}}({{ $bank['bankNo'] ? substr($bank['bankNo'],-4) : null }}) </span></div>
                    <div class="item-after"><a href="#" onclick="JumpURL('{{u('Mine/bank',['id'=>$bank['id']])}}','#seller_bank_view',2)"  class="f_red">详情></a></div>
                @else
                    <div class="item-title">暂无银行卡</div>
                    <div class="item-after"><a href="#" onclick="JumpURL('{{u('Mine/bank')}}','#seller_bank_view',2)"  class="f_red">去绑定></a></div>
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
    //提现选择处理事件 ----------------------------cz------------------------------------------------------------------
    $(document).on("click",".page-current .ajax-success-bnt2",function(){
        var money = $(".page-current input[name='carry_money']").val();
        if(!money){
            $.toast("提现金额不能为空");
            return false;
        }
        if(money < 100){
            $.toast("单次提现不能低于100");
            return false;
        }
        $.post("{{ u('Mine/withdraw') }}",{'amount': money},function(res){
            $.toast(res.msg);
            if(res.code == 0){
                var url = "{{u('Mine/account')}}?ajax=account-show&account="+res.data.money;
                JumpURL(url,'#seller_account_view',2);
            }
        },"json");
    });


    $(".page-current input[id='carry_money']").attr("maxlength","11").attr("onkeyup", "this.value=this.value.replace(/\\D/g,'')").attr("onafterpaste", "this.value=this.value.replace(/\\D/g,'')");
</script>
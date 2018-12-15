@extends('admin._layouts.base')
@section('css')
<style type="text/css">

</style>
@stop
@section('right_content')

    @yizan_begin
        <yz:form id="yz_form" action="save">
            <div class="tabs">
                <div class="tab-navs u-spnav u-orderlstnav">
                    <ul class="clearfix">
                        <li class="tab-nav on" data-id="1">
                            <a href="#">分享设置</a>
                        </li>
                        <li class="tab-nav" data-id="2">
                            <a href="#">佣金设置</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="cb"></div>
            <div class="show1">
                <yz:fitem label="链接标题">
                    <input type="text" class="u-ipttext" name="shareTitle" id="shareTitle" value="{{$data['shareTitle']}}" maxlength="20">
                    <span class="ts">限20个字符内</span>
                </yz:fitem>
                <yz:fitem label="链接内容">
                    <input type="text" class="u-ipttext" name="shareContent" id="shareContent" value="{{$data['shareContent']}}" maxlength="30">
                    <span class="ts">限30个字符内</span>
                </yz:fitem>
                <yz:fitem name="shareLogo" label="分享图标" val="{{$data['shareLogo']}}" type="image" ></yz:fitem>
                <yz:fitem name="inviteLogo" label="邀请分享页" val="{{$data['inviteLogo']}}" type="image" ></yz:fitem>
                <yz:fitem name="shareExplain" label="活动说明">
                    <yz:Editor name="shareExplain" value="{{ $data['shareExplain'] }}"></yz:Editor>
                </yz:fitem>

                <yz:fitem name="shareDescribe" label="分享说明" type="textarea"></yz:fitem>
                <yz:fitem name="pointsNoExplain" label="分拥说明" type="textarea"></yz:fitem>
            </div>
            <div class="show2 none">
                <div>
                    <div class="u-rtt f3f6fa">
                        <i class="fa fa-money mr15 ml15"></i>返现资格设置
                    </div>
                    <yz:fitem label="返现资格设置">
                        <yz:radio name="userStatus" options="0,1,2" texts="关闭,无条件开启,缴费开启" checked="$data['userStatus']" default="0"></yz:radio>
                    </yz:fitem>
                    <yz:fitem label="缴费金额">
                        <input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="protocolFee" id="protocolFee" value="{{$data['protocolFee']}}">元
                    </yz:fitem>
                    <yz:fitem name="purchaseAgreement" label="购买协议">
                        <yz:Editor name="purchaseAgreement" value="{{ $data['purchaseAgreement'] }}"></yz:Editor>
                    </yz:fitem>
                    <yz:fitem name="privilegeDetails" label="特权描述">
                        <yz:Editor name="privilegeDetails" value="{{ $data['privilegeDetails'] }}"></yz:Editor>
                    </yz:fitem>
                </div>
                <div>
                    <div class="u-rtt f3f6fa">
                        <i class="fa fa-money mr15 ml15"></i>全国店返现比例
                    </div>
                    <yz:fitem label="全国店返现比率">
                        初级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="isAllUserPrimary" id="userPrimary" value="{{$data['isAllUserPrimary']}}">%
                        <br>
                        <br>
                        一级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="isAllUserPercent" id="userPercent" value="{{$data['isAllUserPercent']}}">%
                        <br>
                        <br>
                        二级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="isAllUserPercentSecond" id="userPercentSecond" value="{{$data['isAllUserPercentSecond']}}">%
                        <br>
                        <br>
                        三级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="isAllUserPercentThird" id="userPercentThird" value="{{$data['isAllUserPercentThird']}}">%
                        <br>
                        <br>
                        <span class="ts">佣金＝（订单商品总金额－商家自营销特价/满减优惠金额）*返佣比率</span>
                    </yz:fitem>
                </div>
                <div>
                    <div class="u-rtt f3f6fa">
                        <i class="fa fa-money mr15 ml15"></i>周边店返现比例
                    </div>
                    <yz:fitem label="周店返现比率">
                        一级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="userPercent" id="userPercent" value="{{$data['userPercent']}}">%
                        <br>
                        <br>
                        二级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="userPercentSecond" id="userPercentSecond" value="{{$data['userPercentSecond']}}">%
                        <br>
                        <br>
                        三级返佣&nbsp;<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="userPercentThird" id="userPercentThird" value="{{$data['userPercentThird']}}">%
                        <br>
                        <br>
                        <span class="ts">佣金＝（订单商品总金额－商家自营销特价/满减优惠金额）*返佣比率</span>
                    </yz:fitem>
                </div>

                <div class="u-rtt f3f6fa">
                    <i class="fa fa-money mr15 ml15"></i>商家返现比例
                </div>
                <yz:fitem label="商家邀请返现">
                    <yz:radio name="sellerStatus" options="0,1" texts="关闭,开启" checked="$data['sellerStatus']" default="0"></yz:radio>
                </yz:fitem>
                <yz:fitem label="返现比率">
                    <input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="sellerPercent" id="sellerPercent" value="{{$data['sellerPercent']}}">%
                    <span class="ts">佣金 = （订单金额 - 运费） * 返现比率</span>
                </yz:fitem>
                <yz:fitem label="消费金额满">
                    <input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="fullMoney" id="fullMoney" value="{{$data['fullMoney']}}">
                    <span class="ts">元返现</span>
                </yz:fitem>
            </div>
        </yz:form>
    @yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(".tab-nav").click(function(){
        $(".tab-nav").removeClass("no");
        if($(this).data("id") == 1){
            $(this).addClass("no");
            $(".show1").removeClass("none");
            $(".show2").addClass("none");
        }else{
            $(this).addClass("no");
            $(".show1").addClass("none");
            $(".show2").removeClass("none");
        }
    });
</script>
@stop

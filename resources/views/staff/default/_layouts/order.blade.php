@if(
($data['isCanAccept'] && in_array($role,['1','3','5','7'])) ||
($data['isCanStartService'] && $role != 1 && !in_array($data['sendWay'], [2,3])) ||
($data['isCanCancel'] && in_array($role,['1','3','5','7']))||
($data['isCanFinishService'])
)
    <div class="bar bar-footer y-orderxqbtm">
        @if($data['orderType'] == 1)
            @if($data['isCanCall'] && ($role==7 || $role ==1) && $data['sellerSendWay'] == 2 && $data['sellerFee'] >= $system_send_staff_fee && ($data['sendWay'] == 1 || $data['sendWay'] == 0))
                <p class="f_aaa f12">使用{{$site_name}}配送, 仅需{{$system_send_staff_fee}}元</p>
            @endif
        @endif
        <div class="y-orderxqbtn">
            <div>
                @if($data['orderType'] == 1)
                    @if($data['isCanCall'] && ($role==7 || $role ==1) && $data['sellerSendWay'] == 2 && $data['sellerFee'] >= $system_send_staff_fee && ($data['sendWay'] == 1 || $data['sendWay'] == 0))
                        <a href="#" id="isCanCall" onclick="$.isCanCall({{$data['id']}},5 )" class="button f12">呼叫配送</a>
                    @endif
                    @if($data['isCanCancelCall'] && ($role==7 || $role ==1))
                        <a href="#" id="isCanCancelCall" onclick="$.isCanCancelCall({{$data['id']}},6 )" class="button f12">取消呼叫</a>
                    @endif
                @endif
            </div>
            <div>
                @if($data['isCanCancel'] && in_array($role,['1','3','5','7']) )
                    <a href="#" id="isCanAccept" onclick="$.isCanCancel({{$data['id']}},1 )" class=" button f12" >取消订单</a>
                @endif
                @if($data['isCanAccept'] && in_array($role,['1','3','5','7']) )
                    <a href="#" id="isCanAccept" onclick="$.isCanAccept({{$data['id']}},2 )" class="button f12 y-bgff2d4b">接单</a>
                @endif
                @if($data['orderType'] == 1)
                    @if($data['isCanStartService'] && in_array($role,['2','3','6','7']) && !in_array($data['sendWay'], [2,3]))
                            @if($role != 7 && $data['status'] == ORDER_STATUS_GET_SYSTEM_SEND)
                                <a href="#" id="isCanAccept" onclick="$.isCanStartService({{$data['id']}},4 )" class="button f12 y-bgff2d4b">开始配送</a>
                            @endif
							
							@if($role == 7 && $data['status'] == ORDER_STATUS_AFFIRM_SELLER)
                                <a href="#" id="isCanAccept" onclick="$.isCanStartService({{$data['id']}},4 )" class="button f12 y-bgff2d4b">开始配送</a>
                            @endif
                    @endif
                    @if($data['isCanFinishService'] && in_array($role,['2','3','6','7']) )
                        <a href="#" id="isCanAccept" onclick="$.isCanFinishService({{$data['id']}},3 )" class="button f12 y-bgff2d4b">配送完成</a>
                    @endif
                @else
                    @if($data['isCanStartService'] && in_array($role,['4','5','6','7']) && !in_array($data['sendWay'], [2,3]))
                        <a href="#" id="isCanAccept" onclick="$.isCanStartService({{$data['id']}},4 )" class="button f12 y-bgff2d4b">开始服务</a>
                    @endif

                    @if($data['isCanFinishService'] && in_array($role,['4','5','6','7']) )
                        <a href="#" id="isCanAccept" onclick="$.isCanFinishService({{$data['id']}},3 )" class="button  f12 y-bgff2d4b">服务完成</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endif
<style>
    /*取消原因*/
    /*.y-cancelreason{margin: -.5rem;}*/
    .y-cancelreason li{}
    .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
    .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
    .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
</style>
@if($data['orderType'] == 2)
<script type="text/tpl" id="cancehtml">
    <ul class="y-cancelreason tl f13">
        <li><span id="cancelreason1">订单太多，无法及时提供服务</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
        <li><span id="cancelreason2">信息有误，即将下架</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
        <li><span id="cancelreason3">服务人员请假</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
        <li class="y-otherrea">
            <span id="cancelreason4">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="4">
            <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
        </li>
    </ul>
</script>
@else
<script type="text/tpl" id="cancehtml">
    <ul class="y-cancelreason tl f13">
        <li><span id="cancelreason1">订单太多，无法及时送达</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
        <li><span id="cancelreason2">商品信息有误，即将下架</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
        <li><span id="cancelreason3">配送人员请假，无人配送</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
        <li class="y-otherrea">
            <span id="cancelreason4">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="4">
            <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
        </li>
    </ul>
</script>
@endif


@section($js)
    <script type="text/javascript">
        $(function() {
            {{--if($("#{{$id_action.$ajaxurl_page}} .but_box_parent").html().replace(/(^\s*)|(\s*$)/g, "") == ""){--}}
                {{--$("#{{$id_action.$ajaxurl_page}} .btn-content").addClass("none");--}}
            {{--}--}}
            $.status = function (id, status,remark) {
                $.showIndicator();
                $.post("{{ u('Order/orderReceiving') }}", {'id': id, 'status': status, 'remark': remark}, function (res) {
                    $.hideIndicator();
                    if(res.code != 0){
                        $.toast(res.msg);
                        return false;
                    }
                    if("{{explode("_",$id_action)[0]}}" == "order"){
                        JumpURL('{{ u('Order/detail',['id'=>$data['id']]) }}','#order_detail_view',1);
                    }else{
                        JumpURL('{{ u('Index/detail',['id'=>$data['id']]) }}','#index_detail_view',1);
                    }
                }, "json");
            }
            //呼叫配送
            $.isCanCall = function (id, status) {
                $.status(id, status);
            }
            //取消呼叫配送
            $.isCanCancelCall = function (id, status) {
                $.modal({
                    title:  '取消呼叫配送',
                    text: '每个订单只能取消一次，您确认取消平台配送吗？',
                    buttons: [
                        {text: '取消'},
                        {
                            text: '确定',
                            bold:true,
                            onClick: function() {
                                $.status(id, status);
                            }
                        }
                    ]
                })
            }

            //接单
            $.isCanAccept = function (id, status) {
                $.status(id, status);
            }
            //开始
            $.isCanStartService = function (id, status) {
                $.status(id, status);
            }
            //完成
            $.isCanFinishService = function (id, status) {
                $.status(id, status);
            }

            //取消
            $.isCanCancel = function (id, status) {
                var textcancel = $("#cancehtml").html();
                    $.modal({
                        title:  '取消原因',
                        text: textcancel,
                        buttons: [
                            {text: '取消'},
                            {
                                text: '确定',
                                bold:true,
                                onClick: function() {
                                    var cancelradioval = $('.y-cancelreason input[name="reason"]:checked ').val();
                                    if(cancelradioval == 4){
                                        var cancelRemark = $("#cancelreasontext").val();
                                        cancelRemark = (cancelRemark == "") ? $("#cancelreason"+cancelradioval).html() : cancelRemark;
                                    }else{
                                        var cancelRemark = $("#cancelreason"+cancelradioval).html();
                                    }
                                    $.status(id, status,cancelRemark);
                                }
                            }
                        ]
                    })
            }

            //取消原因—其他原因
            $(document).on("click",".y-cancelreason li input",function(){
                $(".y-otherreasons").addClass("none");
            }).on("click",".y-cancelreason li.y-otherrea input",function(){
                $(".y-otherreasons").removeClass("none");
            })
        });
    </script>
@stop
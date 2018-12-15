<style>
    /*取消原因*/
    /*.y-cancelreason{margin: -.5rem;}*/
    .y-cancelreason li{}
    .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
    .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
    .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
</style>
<script type="text/tpl" id="cancehtml">
    <ul class="y-cancelreason tl f13">
        <li><span id="cancelreason1">未及时付款</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
        <li><span id="cancelreason2">买家不想买了</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
        <li><span id="cancelreason3">买家信息填写错误，重新拍</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
        <li><span id="cancelreason4">恶意买家/同行捣乱</span><input type="radio" name="reason" class="fr y-radio" value="4"></li>
        <li><span id="cancelreason5">缺货</span><input type="radio" name="reason" class="fr y-radio" value="5"></li>
        <li><span id="cancelreason6">买家拍错了</span><input type="radio" name="reason" class="fr y-radio" value="6"></li>
        <li class="y-otherrea">
            <span id="cancelreason8">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="8">
            <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
        </li>
    </ul>
</script>
@section($js)
    <script type="text/javascript">
        $(function() {
            $.status = function (id, status,remark) {
                $.showIndicator();
                $.post("{{ u('Order/orderReceiving') }}", {'id': id, 'status': status, 'remark': remark}, function (res) {
                    $.hideIndicator();
                    if(res.code != 0){
                        $.toast(res.msg);
                        return false;
                    }
                    if("{{explode("_",$id_action)[0]}}" == "order"){
                        JumpURL('{{ u('Order/detail') }}?id='+id,'#order_detail_view',1);
                    }else{
                        JumpURL('{{ u('Index/detail') }}?id='+id,'#index_detail_view',1);
                    }
                }, "json");
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
            $.isCancfOrder = function (id) {
                $.modal({
                    title:  '是否同意取消',
                    text: "会员申请取消订单，是否同意",
                    buttons: [
                        {text: '取消'},
                        {
                            text: '确定',
                            bold:true,
                            onClick: function() {
                                $.status(id, {{ORDER_STATUS_CANCEL_USER_SELLER}});
                            }
                        }
                    ]
                })
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
                                    if(cancelradioval == 8){
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
@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
    </style>
@stop
<?php
$cause = [
       [
               'a' =>1,
               'c' =>"商品已影响二次销售",
        ],
        [
                'a' =>2,
                'c' =>"商品已发货"
        ],
         [
                'a' =>3,
                'c' =>"买家不想退款了"
        ],
        [
                'a' =>4,
                'c' =>"其他原因"
        ]
];
?>
@section('content')
    @yizan_begin
    <yz:form id="yz_form" action="refundSave">
        <div class="m-zjgltbg f12">
            <div class="p10">
                <!-- 设置服务种类 -->
                <p class="lh45">
                    拒绝退款申请
                    <span class="ml15 fr"><a href="{{ u('Order/detail',['orderId'=>$data['orderId']]) }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a></span>
                </p>
                <div class="g-szzllst pt10">
                    <yz:fitem label="拒绝原因">
                        <yz:select name="causeId" options="$cause" textfield="c" valuefield="a" selected="1"></yz:select>
                    </yz:fitem>
                    <div class="none udb_show_cause">
                        <yz:fitem label="其他原因" name="cause" type="textarea"></yz:fitem>
                    </div>
                    <yz:fitem label="图片凭证">
                        <yz:imageList name="images."></yz:imageList>
                        <span class="red">建议：最多可上传四张图片达到显示最佳效果</span>
                    </yz:fitem>
                    <yz:fitem label="拒绝说明" name="brief" type="textarea"></yz:fitem>
                    <yz:fitem name="orderId" type="hidden"></yz:fitem>
                </div>
            </div>
        </div>
    </yz:form>
    @yizan_end
@stop
@section('js')
    <script>
        $("select[name=causeId]").change(function() {
            var val = $(this).val();
            if(val == 4){
                $(".udb_show_cause").removeClass("none");
            }else{
                $(".udb_show_cause").addClass("none");
            }
        })
    </script>
@stop
@include('seller._layouts.alert')

@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .m-ydtt .f-tt{line-height: 30px;}
    .star-rank{
        width: 85px;
        height: 40px;
        background: url("{{ asset('wap/community/client/images/ico/star.png') }}") left center repeat-x;
        background-size: 17px 12px;
    }
    .star-score{
        height: 40px;
        background: url("{{ asset('wap/community/client/images/ico/star1.png') }}") left center repeat-x;
        background-size: 17px 12px;
    }
    .search-row .u-ipttext {
        width: 100px;
        padding-right: 5px;
    }
</style>
@stop
@section('content')
<?php 
    $reply = [
        ['id'=> 0,'name'=>'全部'],
        ['id'=>'good','name'=>'好评'],
        ['id'=>'neutral','name'=>'中评'],
        ['id'=>'bad','name'=>'差评'],
    ];
 ?>  
<div class="ma">
    <div class="m-ydtt" style="margin-top:0px;">
        <div class="x-bbmain">
            <div class="x-pjgltt">订单评价详情</div>
            @yizan_begin
                <yz:list>
                    <search>
                        <row>
                            <span>订单号：{{$list[0]['order']['sn']}}</span>
                            <span class="ml20">会员名：{{$list[0]['user']['name']}}</span>
                            <span class="ml20">店铺评分：{{$list[0]['star']}}</span>
                        </row>
                    </search>
                    <table pager="no">
                    	<div class="plall">
                        <ul class="x-plglul">
                        @foreach($list as $key => $val)
                        <li class="clearfix" style="position:relative;">
                            <div class="x-plglf9" style="width:100%">
                                <a href="javascript:;" class="x-pjimg" style="cursor:default"><img src="{{ $val['user']['avatar'] or asset('images/default_headimg.jpg')}}"  style="max-width:80px;max-height:80px;" /></a>             
                                <div class="x-pjr">
                                    <div class="x-pjname">
                                        <div class="fl"><span>商品名称：</span>{{ $val['goods']['name'] }}</div>
                                        <!-- <span class="fl ml20">规格：XXX</span> -->
                                    </div>
                                    <div style="line-height: 40px;">
                                        <span class="fl">评价星级：</span>
                                        <div class="star-rank fl">
                                            <div class="star-score" style="width:{{$val['goodsStar'] * 20}}%;"></div>
                                        </div>
                                    </div>
                                    <div class="x-yhpl mt20">
                                        <div class="x-khpj"><span>评价内容：</span>{{ $val['content'] }}</div>
                                        <ul class="x-plglimg">
                                            @foreach($val['images'] as $k => $v)
                                            <li><a href="{{ $v }}" target="_new"><img src="{{ $v }}" /></a></li> 
                                            @endforeach($list as $key => $val)
                                        </ul>
                                    </div>
                                    @if($val['replyTime'] > 0)
                                        <div class="x-pjname mt10">
                                            <div class="fl"><span>回复：</span>{{ $val['reply'] }}</div>
                                        </div>
                                    @else
                                        <div class="x-yhpl mt20">
                                            <div class="x-khpj">回复评价：</div>
                                            <div class="f-boxr">
                                                <textarea name="content" id="content" class="u-ttarea sellerReply" data-id="{{$val['id']}}" placeholder="回复评价"></textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- <div class="x-plglr" style="position:absolute;top:35px;right:0px;">
                                <a class="btn f-tj fr btn f-30btn mt5 mr15" href="{{ u('Comment/reply',['id'=>$val['id']]) }}">回复</a>
                            </div> -->
                        </li> 
                        @endforeach
                        </ul>
                        </div>
                </table>
                </yz:list>
            @yizan_end
        </div>
    </div>
    <a class="btn f-tj fl btn f-30btn mt10 mb10 ml20" href="#" onclick="$.allOrderReply()">提交</a>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(function(){
        $("input[name='beginTime'],input[name='endTime']").keyup(function(){
            $(this).val("");
        });
        $('#yzForm').submit(function(){
            var beginTime = $("#beginTime").val();
            var endTime = $("#endTime").val();
            if(beginTime!='' || endTime!='') {
                if(beginTime==''){
                    alert("开始时间不能为空");return false;
                }
                else if(endTime==''){
                    alert("结束时间不能为空");return false;
                }
                else if(endTime < beginTime){
                    alert("开始时间不能大于结束时间");return false;
                }
            }
        });

        $.allOrderReply = function() {
            var data = {};
            $(".sellerReply").each(function(index, value){
                if($.trim($(this).val()) != "")
                {
                    var d = {};
                    d['id'] = $(this).data('id');
                    d['content'] = $.trim($(this).val());
                    data[index] = d;
                }
            });
            $.post("{{ u('Comment/allreply') }}", {"data":data}, function(result){
                $.ShowAlert(result.msg);
                if(result.code == 0)
                {
                    window.location.reload();
                }
                
            })
        }
    });
</script>
@stop

@extends('admin._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/sm-ht.css') }}?{{ TPL_VERSION }}">
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
    .x-plglul li.image-box,.x-plglul li.image-add-box{width:20%!important;}
</style>
@stop

@section('right_content')
	<?php 
	    $reply = [
	        ['id'=> 0,'name'=>'全部'],
	        ['id'=>'good','name'=>'好评'],
	        ['id'=>'neutral','name'=>'中评'],
	        ['id'=>'bad','name'=>'差评'],
	    ];
	 ?> 
	@yizan_begin
	<div class="ma">
	    <div class="m-ydtt" style="margin-top:0px;">
	        <div class="x-bbmain">
            	<div class="x-pjgltt">订单评价详情</div>
                <yz:list>
                    <search>
                        <row>
                        	<span>店铺类型：{{$data[0]['seller']['storeType'] == 1 ? '全国店' : '周边店'}}</span>
                            <span class="ml20">订单号：{{$data[0]['order']['sn']}}</span>
                            <span class="ml20">会员名：{{$data[0]['user']['name']}}</span>
                            <span class="ml20">店铺评分：{{$data[0]['star']}}</span>
                        </row>
                    </search>
                    <yz:form id="yz_form" action="saveRate" > 
	                    <table pager="no">
	                    	<div class="plall">
	                        <ul class="x-plglul">
	                        @foreach($data as $key => $val)
	                        <li class="clearfix changeComment" style="position:relative;" data-id="{{$val['id']}}">
	                            <div class="x-plglf9" style="width:100%">
	                                <a href="javascript:;" class="x-pjimg" style="cursor:default">
	                                	@if($data[0]['seller']['storeType'] == 1)
	                                		<img src="{{ $val['goods']['image'] or asset('images/default_headimg.jpg')}}"  style="max-width:80px;max-height:80px;" />
	                                	@else
	                                		<img src="{{ $val['user']['avatar'] or asset('images/default_headimg.jpg')}}"  style="max-width:80px;max-height:80px;" />
	                                	@endif
	                                </a>             
	                                <div class="x-pjr">
	                                	@if($data[0]['seller']['storeType'] == 1)
		                                    <div class="x-pjname">
		                                        <div class="fl"><span>商品名称：</span>{{ $val['goods']['name'] }}</div>
		                                        <!-- <span class="fl ml20">规格：XXX</span> -->
		                                    </div>
		                                    <div style="line-height: 40px;">
		                                        <span class="fl">评价星级：</span>
		                                        <yz:radio name="{{$val['id']}}.goodsStar" options="1,2,3,4,5" texts="1星,2星,3星,4星,5星" checked="$val['goodsStar']"></yz:radio>
		                                    </div>
                                        @else
                                            <div class="x-pjname">
                                                <div class="fl"><span>商家名称：</span>{{ $val['seller']['name'] }}</div>
                                            </div>
										@endif
	                                    <div class="mt20">
	                                        <div class="x-khpj">评价内容：</div>
                                            <div class="f-boxr">
                                                <textarea name="{{$val['id']}}[content]" class="u-ttarea sellerContent" data-id="{{$val['id']}}" placeholder="评价内容">{{ $val['content'] }}</textarea>
                                            </div>
	                                        <yz:imageList name="{{$val['id']}}.images." images="$val['images']"></yz:imageList>
	                                    </div>
                                        <div class="x-yhpl mt20">
                                            <div class="x-khpj">回复评价：</div>
                                            <div class="f-boxr">
                                                <textarea name="{{$val['id']}}[reply]" class="u-ttarea sellerReply" data-id="{{$val['id']}}" placeholder="回复评价">{{ $val['reply'] }}</textarea>
                                            </div>
                                        </div>
	                                </div>
	                            </div>
	                            <input type="hidden" name="{{$val['id'][id]}}" value="{{$val['id']}}">
	                        </li> 
	                        @endforeach
	                        </ul>
	                        </div>
	                	</table>
	                </yz:form>
                </yz:list>
	        </div>
	    </div>
	</div>
	@yizan_end
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
    });
</script>
@stop

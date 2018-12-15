@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">物业费详情</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="save">
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                     楼栋:
                                </span>
                                <div class="f-boxr">
                                    {{$data['build']['name']}}
                                </div>
                            </div>
                            <div  class="u-fitem clearfix ">
                                <span class="f-tt">
                                     房间:
                                </span>
                                <div class="f-boxr">
                                    {{$data['room']['owner']}}
                                </div>
                            </div> 
                            <div  class="u-fitem clearfix ">
                                <span class="f-tt">
                                     收费项目:
                                </span>
                                <div class="f-boxr">
                                    {{$data['roomfee']['payitem']['name']}}
                                </div>
                            </div> 
                            <div  class="u-fitem clearfix ">
                                <span class="f-tt">
                                     计费方式:
                                </span>
                                <div class="f-boxr">
                                    {{ Lang::get('api_seller.property.charging_item.'.$data['roomfee']['payitem']['chargingItem']) }}
                                </div>
                            </div> 
                            <div  class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费单位:
                                </span>
                                <div class="f-boxr">
                                    {{ Lang::get('api_seller.property.charging_unit.'.$data['roomfee']['payitem']['chargingUnit']) }}
                                </div>
                            </div> 
                            <div  class="u-fitem clearfix ">
                                <span class="f-tt">
                                    备注:
                                </span>
                                <div class="f-boxr">
                                    {{ $data['roomfee']['remark'] }}
                                </div>
                            </div>
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    费用:
                                </span>
                                <div class="f-boxr">
                                    {{$data['fee']}}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费开始时间:
                                </span>
                                <div class="f-boxr">
                                    {{yztime($data['beginTime'], 'Y-m-d')}}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费结束时间:
                                </span>
                                <div class="f-boxr">
                                    {{yztime($data['endTime'], 'Y-m-d')}}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    缴费时间:
                                </span>
                                <div class="f-boxr">
                                    {{yztime($data['payTime'], 'Y-m-d')}}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    支付状态:
                                </span>
                                <div class="f-boxr">
                                    {{ $data['status'] ? '已支付':'未支付' }}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    缴费会员:
                                </span>
                                <div class="f-boxr">
                                    {{ $data['puser']['name'] }}
                                </div>
                            </div>  
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    电话:
                                </span>
                                <div class="f-boxr">
                                    {{ $data['puser']['mobile'] }}
                                </div>
                            </div>  
                        </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop 
@section('js')
<script type="text/javascript">
jQuery(function($){
    $(".u-addspbtn").hide();
});
</script>
@stop
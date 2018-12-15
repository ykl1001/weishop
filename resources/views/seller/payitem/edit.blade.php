@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10"> 
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">收费项目详情</span>
                    </p>
                </div> 
                <div class="m-tab m-smfw-ser pt20"> 
                    @yizan_begin
                    <yz:form id="yz_form" action="save">
                        <yz:fitem name="name" label="项目名称"></yz:fitem>
                        <yz:fitem name="price" label="单价（元）" ></yz:fitem>
                        <yz:fitem label="收费项目">
                            <yz:select name="chargingItem" options="$chargingItem" selected="$data['chargingItem']"></yz:select>
                        </yz:fitem>
                        <yz:fitem label="收费单位">
                            <yz:select name="chargingUnit" options="$chargingUnit" selected="$data['chargingUnit']"></yz:select>
                        </yz:fitem>
                    </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop
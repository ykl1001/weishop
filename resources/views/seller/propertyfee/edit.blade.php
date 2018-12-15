@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">添加物业费</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="save">
                            <yz:fitem name="buildId" label="楼栋号">
                                <select id="buildId" name="buildId" style="min-width:100px;width:auto" class="sle ">
                                    <option value=" " >请选择楼栋号</option>
                                    @foreach($buildIds as $val)
                                    <option value="{{$val['id']}}" @if($data['buildId'] == $val['id']) selected @endif>{{$val['name']}}</option>
                                    @endforeach
                                </select>
                            </yz:fitem>
                            <yz:fitem  name="roomId" label="房间号" tip="（不选择房间号则会给整个楼栋添加费用）">
                                <select id="roomId" name="roomId" style="min-width:100px;width:auto" class="sle ">
                                    <option value=" " >请选择房间号</option>
                                    @foreach($roomIds as $val)
                                    <option value="{{$val['id']}}" @if($data['roomId'] == $val['id']) selected @endif>{{$val['roomNum']}}</option>
                                    @endforeach
                                </select>
                            </yz:fitem>
                            <yz:fitem  name="roomFeeId" label="房间收费项目">
                                <select id="roomFeeId" name="roomFeeId" style="min-width:100px;width:auto" class="sle ">
                                    <option value="" >请选择</option>  
                                </select> 
                            </yz:fitem>
                            <div id="chargingItem-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费方式:
                                </span>
                                <div class="f-boxr">
                                    <p id="chargingItem">无</p>
                                </div>
                            </div>
                            <div id="chargingUnit-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费单位:
                                </span>
                                <div class="f-boxr">
                                    <p id="chargingUnit">无</p>
                                </div>
                            </div>
                            <div id="remark-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    备注:
                                </span>
                                <div class="f-boxr">
                                    <p id="remark">无</p>
                                </div>
                            </div>
                            <div id="fee-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    费用:
                                </span>
                                <div class="f-boxr">
                                    <p id="fee"></p>
                                </div>
                            </div> 
                            <yz:fitem name="beginTime" label="计费开始时间" type="date"></yz:fitem>
                            <yz:fitem name="num" label="收取数量" ></yz:fitem>
                            <div class="u-fitem clearfix ">
                                <span class="f-tt">
                                    &nbsp;
                                </span>
                                <div class="f-boxr">
                                    <p>不选择计费开始时间的情况下：1、取上次账单结束时间第二天为计费开始时间。2、无上次账单时，取入住时间为计费开始时间。</p>
                                </div>
                            </div>
                            
                            <input type="hidden" id="is_auto_set" name="isAutoSet" value="" />
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
    $("#buildId").change(function() {
        var buildId = $(this).val();
        var u_id = new Array(); 
        $.post("{{ u('PropertyFee/searchroom') }}",{"buildId":buildId},function(result){  
            var html = '<option value="0">请选择房间号</option>';
            var data = result.data.list;
            $.each(data, function(index,e){ 
                if (u_id.indexOf(data[index].id) == -1){
                    html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.roomNum + "</option>";                     
                }
            });
            $('#roomId').html(html).trigger('change');
        },'json');
    });

    function refreshData(obj){ 
        $.post("{{ u('RoomFee/search') }}",obj,function(res){   
           var optionsStr = ""; 
            if(res.length > 0){
                for(var i=0; i<res.length;i++){
                    optionsStr += "<option value='"+res[i].id+"' data-chargingitem='"+res[i].chargingItem+"' data-chargingunit='"+res[i].chargingUnit+"' data-remark='"+res[i].remark+"' data-fee='"+res[i].fee+"' >"+res[i].payitem.name+"</option>";
                }
            } else {
                optionsStr += "<option value='0'>此房间暂无缴费信息</option>";  
            } 
            $("#roomFeeId").html(optionsStr);
            var selRoomFee = $("#roomFeeId option:selected");
            var chargingItem = '无';
            var chargingUnit = '无';
            var remark = '无';
            var fee = '0';
            if(selRoomFee.val() > 0){ 
                chargingItem = selRoomFee.data('chargingitem');
                chargingUnit = selRoomFee.data('chargingunit');
                remark = selRoomFee.data('remark');
                fee = selRoomFee.data('fee');
            } 
            $("#chargingItem").html(chargingItem);
            $("#chargingUnit").html(chargingUnit);
            $("#remark").html(remark); 
            if (obj.roomId == 0) {
                $("#fee-form-item").hide();
            } else {
                $("#fee-form-item").show(); 
                $("#fee").html(fee); 
            }
        },'json');
    }

    $("#roomId").change(function(){ 
        var obj = new Object();
        obj.buildId = $("#buildId").val();
        obj.roomId = $(this).val(); 
        refreshData(obj);
        if(obj.roomId > 0){
            $("#beginTime-form-item").show();
            $("#is_auto_set").val('0');
        } else {
            $("#beginTime-form-item").hide(); 
            $("#is_auto_set").val('1');
        }
    }).trigger('change');  

    $("#roomFeeId").change(function(){ 
        var selRoomFee = $("#roomFeeId option:selected");
        var chargingItem = '无';
        var chargingUnit = '无';
        var remark = '无';
        var fee = '0';
        if(selRoomFee.val() > 0){ 
            chargingItem = selRoomFee.data('chargingitem');
            chargingUnit = selRoomFee.data('chargingunit');
            remark = selRoomFee.data('remark');
            fee = selRoomFee.data('fee');
        } 
        $("#chargingItem").html(chargingItem);
        $("#chargingUnit").html(chargingUnit);
        $("#remark").html(remark);
        $("#fee").html(fee); 
    });
});
</script>
@stop
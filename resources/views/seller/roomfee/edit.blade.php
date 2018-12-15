@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">房间费用管理</span>
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
                            <yz:fitem  name="payitemId" label="收费项目">
                                <select id="payitemId" name="payitemId" style="min-width:100px;width:auto" class="sle ">
                                    <option value="" >请选择</option> 
                                    @foreach($payitemlist as $payitem)
                                    <option value="{{$payitem['id']}}" data-payitem="{{ json_encode($payitem) }}" @if($payitem['id'] == $data['payitem']['id']) selected @endif>{{$payitem['name']}}</option> 
                                    @endforeach
                                </select>
                            </yz:fitem>
                            <div id="chargingItem-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费方式:
                                </span>
                                <div class="f-boxr">
                                    <p id="chargingItem"></p>
                                </div>
                            </div>
                            <div id="chargingUnit-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    计费单位:
                                </span>
                                <div class="f-boxr">
                                    <p id="chargingUnit"></p>
                                </div>
                            </div>
                            <yz:fitem name="remark" label="备注"></yz:fitem> 
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
        $.post("{{ u('RoomFee/searchroom') }}",{"buildId":buildId},function(result){  
            var html = '<option value="0">请选择房间号</option>';
            var data = result.list;
            $.each(data, function(index,e){ 
                if (u_id.indexOf(data[index].id) == -1){
                    html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.roomNum + "</option>";                     
                }
            });
            $('#roomId').html(html);
        },'json');
    });

    $("#payitemId").change(function(){ 
        var payitem = $("#payitemId option:selected").data('payitem'); 
        var payitemId = $(this).val();
        if(payitemId != ''){
            $("#chargingItem").html(payitem.chargingItem);
            $("#chargingUnit").html(payitem.chargingUnit); 
        } else {
            $("#chargingItem").html('');
            $("#chargingUnit").html(''); 
        }
    }).trigger('change'); 
});
</script>
@stop
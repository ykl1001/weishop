@extends('seller._layouts.base')
@section('css')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no, email=no">
    <style>
        #yjiuShow {
            color:#000 !important;font-family: "宋体";font-weight: bold;
            width:680px;
            height:326px;
            box-sizing: border-box;
        }
        #yjiuShow .spacing{
            letter-spacing:2px;
            padding-top:5px;
            padding-bottom:5px;
        }
        #yjiuShow td {
            border-collapse: collapse;
            padding-left:5px;
            line-height: 35px;
        }
        #yjiuShow .pdyj{
            padding:5px;
        }
        #yjiuShow .wd20{
            width:20%;
            height: 30px;
        }
        .lh12{
            line-height: 24px !important;
        }
        #yjiuShow .td td{
            border:1px solid #000 !important;
        }
    </style>
@stop
@section('content')
    <div>
        <div class="m-zjgltbg">
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">物业费列表</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser">
                    @yizan_begin
                    <yz:list>
                        <search>
                            <row>
                                <yz:fitem  name="buildId" label="楼栋号">
                                    <yz:select name="buildId" options="$builds" first="请选择楼栋" firstvalue="0" textfield="name" valuefield="id" selected="$search_args['buildId']"></yz:select>
                                </yz:fitem>
                                <yz:fitem  name="roomId" label="房间号">
                                    <select id="roomId" data-id="{{$search_args['roomId']}}" name="roomId" style="min-width:100px;width:auto" class="sle ">
                                        <option value="" >请选择房间号</option>
                                    </select>
                                </yz:fitem>
                                <item name="name" label="业主名称"></item>
                                <yz:fitem  name="payitemId" label="收费项目">
                                    <yz:select name="payitemId" options="$payitemlist" first="请选择收费项目" firstvalue="0" textfield="name" valuefield="id" selected="$search_args['payitemId']"></yz:select>
                                </yz:fitem>
                                <yz:fitem  name="status" label="状态">
                                    <yz:select name="status" testx="0,1,2" options="全部,未支付,已支付" selected="$search_args['status']"></yz:select>
                                </yz:fitem>
                                <item name="beginTime" label="开始时间" type="date"></item>
                                <item name="endTime" label="结束时间" type="date"></item>
                                <btn type="search" css="btn-gray"></btn>
                            </row>
                        </search>
                        <btns>
                            <linkbtn label="添加账单" css="btn-gray" url="{{ u('PropertyFee/create') }}"></linkbtn>
                            <button type="button" class="btn mr5 btn-gray" onclick="$.takeList(this)">收费</button>
                            <btn type="destroy" css="btn-gray" label="删除"></btn>
                            <a id="export" href="{!! urldecode(u('PropertyFee/export',$args)) !!}" target="_self" class="btn mr5 btn-gray">
                                导出到EXCEL
                            </a>
                        </btns>
                        {{--<div class="list-btns">--}}
                        {{----}}
                        {{--</div>--}}
                        <table checkbox="1">
                            <columns>
                                <column code="id" label="编号" width="40"></column>
                                <column code="build" label="楼栋号" width="50">
                                    <p>{{ $list_item['build']['name'] }}</p>
                                </column>
                                <column code="roomNum" label="房间号" width="50">
                                    <p>{{ $list_item['room']['roomNum'] }}</p>
                                </column>
                                <column code="name" label="业主" >
                                    <p>{{ $list_item['room']['owner'] }}</p>
                                </column>
                                <column label="收费项目" >
                                    <p>{{ $list_item['roomfee']['payitem']['name'] }}</p>
                                </column>
                                <column label="费用" >
                                    <p>{{ $list_item['fee'] }}</p>
                                </column>
                                <column label="计费开始时间" >
                                    <p>{{ yztime($list_item['beginTime'], 'Y-m-d') }}</p>
                                </column>
                                <column label="计费结束时间" >
                                    <p>{{ yztime($list_item['endTime'], 'Y-m-d') }}</p>
                                </column>
                                <column label="状态" >
                                    <p>{{ $list_item['status'] ? '已支付' : '未支付' }}</p>
                                </column>
                                <actions width="100">
                                    <action label="打印" >
                                        <attrs>
                                            <url>javascript:showUrl({{$list_item['id']}});</url>
                                        </attrs>
                                    </action>
                                    <action label="查看" >
                                        <attrs>
                                            <url>{{ u('PropertyFee/detail',['id'=>$list_item['id']]) }}</url>
                                        </attrs>
                                    </action>
                                    @if($list_item['status'] == 0)
                                        <action type="destroy" css="red"></action>
                                    @endif
                                </actions>
                            </columns>
                        </table>
                    </yz:list>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
    <div id="y-previewbox-color" class="none" >
        <div id="showhtml">

        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/jQuery.print.js') }}?{{ TPL_VERSION }}"></script>
    <script type="text/javascript">
        jQuery(function($){
            $("#buildId").change(function() {
                var buildId = $(this).val();
                var u_id = new Array();
                $.post("{{ u('PropertyFee/searchroom') }}",{"buildId":buildId},function(result){
                    var html = '<option value="" >请选择房间号</option>';
                    var data = result.data.list;
                    var roomId = $("#roomId").data('id');
                    $.each(data, function(index,e){
                        if (u_id.indexOf(data[index].id) == -1){
                            if(roomId == e.id){
                                html += " <option class='uid" + e.id + "' value=" + e.id + " selected >" + e.roomNum + "</option>";
                            } else {
                                html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.roomNum + "</option>";
                            }
                        }
                    });
                    $('#roomId').html(html);
                },'json');
            }).trigger('change');
            $.takeList = function(){
                var input = $("input[name=key]");
                var propertyFeeId = new Array();
                var k = 0;
                for (var i = 0; i < input.length; i++) {
                    if($(input[i]).parent().hasClass('checked')){
                        propertyFeeId[k] = $(input[i]).val();
                        k++;
                    }
                }
                if(propertyFeeId.length <= 0){
                    $.ShowAlert('请选择要支付的账单');
                    return;
                }
                $.post("{{ u('PropertyFee/checkRoomFee') }}",{"id":propertyFeeId},function(result){
                    if(result.status){
                        window.location.href="{{ u('PropertyFee/lists') }}?propertyFeeId="+propertyFeeId;
                    } else {
                        $.ShowAlert(result.msg);
                    }
                },'json');
            };

        });
        function showUrl(id) {
            //$.ShowAlert("<span class='red'>打印功能</span><br><span>‘阿呆’正在努力开发中···</span>");
            $.post("{{u('PropertyFee/printer')}}",{id:id},function(res){
                $("#showhtml").html(res);
                jQuery('#showhtml').print();
            });
        };
    </script>

@stop
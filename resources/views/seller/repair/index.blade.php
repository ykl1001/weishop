@extends('seller._layouts.base')
@section('css')
    <style>
        p{word-wrap:break-word; word-break:normal;}
        .tds tr{background-color: #fff;}
        .y-cancelreason{margin:0 15px;}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
    </style>
@stop
@section('content')
    <php>
        $status = [
        0 => '待处理',
        1 => '进行中',
        2 => '已完成',
        ];
        $type = [
        0 => '业主',
        1 => '租客',
        2 => '业主家属',
        ];

    </php>
    <div>
        <div class="m-zjgltbg">
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">报修管理</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser">
                    @yizan_begin
                    <yz:list>
                        <search url="{{ $searchUrl }}">
                            <row>
                                <item name="name" label="业主名称"></item>
                                <item name="build" label="楼栋号"></item>
                                <item name="roomNum" label="房间号"></item>
                                <btn type="search" css="btn-gray"></btn>
                            </row>
                        </search>
                        <tabs>
                            <navs>
                                <nav label="待处理">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'0','nav'=>'1']) }}</url>
                                        <css>@if( $nav == 1) on @endif</css>
                                    </attrs>
                                </nav>
                                <nav label="进行中">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'1','nav'=>'2']) }}</url>
                                        <css>@if( $nav == 2) on @endif</css>
                                    </attrs>
                                </nav>
                                <nav label="已完成">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'2','nav'=>'3']) }}</url>
                                        <css>@if( $nav == 3) on @endif</css>
                                    </attrs>
                                </nav>
                            </navs>
                        </tabs>
                        <table>
                            <columns>
                                <column code="id" label="编号" width="50"></column>
                                <column code="type" label="类型" width="50">
                                    <p>{{ $list_item['types']['name'] }}</p>
                                </column>
                                <column code="build" label="楼栋号" width="100">
                                    <p>{{ $list_item['build']['name'] }}</p>
                                </column>
                                <column code="roomNum" label="房间号" >
                                    <p>{{ $list_item['room']['roomNum'] }}</p>
                                </column>
                                <column code="owner" label="姓名" >
                                    <p>{{$list_item['puser']['name'] }}</p>
                                </column>
                                <column code="owner" label="认证身份" >
                                    <p>{{$type[$list_item['puser']['type']] }}</p>
                                </column>
                                <column code="mobile" label="电话" >
                                    <p>{{ $list_item['puser']['mobile'] }}</p>
                                </column>
                                <column code="status" label="状态" >
                                    <p>
                                        {{ $status[$list_item['status']] }}
                                    </p>
                                </column>

                                <column code="sellerStaffId" label="维修人员" >
                                    @if($list_item['status'] == 0)
                                        <div style="cursor:pointer;" data-type="{{$list_item['type']}}"  data-id ="{{$list_item['id']}}" class="isReceivabilitySeller">指派</div>
                                    @else
                                        {{ $list_item['staff']['name'] }}  {{ $list_item['staff']['mobile'] }}
                                    @endif

                                </column>
                                <column code="createTime" label="提交日期" >
                                    <p>{{ yzday($list_item['createTime']) }}</p>
                                </column>
                                <actions width="80">
                                    <action label="查看详情" >
                                        <attrs>
                                            <url>{{ u('Repair/edit',['id'=> $list_item['id']]) }}</url>
                                        </attrs>
                                    </action>
                                </actions>
                            </columns>
                        </table>
                    </yz:list>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script type="text/tpl" id="pais">
<div style="width:100%;text-align:center;padding:10px;" id="staff-pais">
    正在加载中,请稍后......
</div>
</script>


    <script type="text/javascript">
        $(function(){

            $(".isReceivabilitySeller").click(function(){
                var id = $(this).data('id');
                var dialog = $.zydialogs.open($("#pais").html(), {
                    boxid:'SET_GROUP_WEEBOX',
                    width:300,
                    title:'指派人员',
                    showClose:true,
                    showButton:true,
                    showOk:true,
                    showCancel:true,
                    okBtnName: '确认指派',
                    cancelBtnName: '取消返回',
                    contentType:'content',
                    onOk: function(){
                        if(staffId == ""){
                            $.ShowAlert("没有选择指定的人员");
                            return false;
                        }

                        $.post("{{ u('repair/designate') }}",{'staffId':staffId,'id':id,'status':1},function(res){
                            $.ShowAlert('指派成功');
                            window.location.reload();

                        },'json');
                    },
                    onCancel:function(){
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                });

                var type = $(this).data('type');
                var html = '';
                $.get('{{u('repair/getRepair')}}?type='+type,function(res){
                    $('#staff-pais').html();
                    html +=  ' <ul class="x-rylst">';
                    $.each(res.data, function(k,v){
                        html +=  '<li data-id="'+ v.id+'">'+v.name+'<i></i></li>';
                    });
                    html +=  '<div class="clearfix"></div>';
                    html +=  ' </ul>';
                    $('#staff-pais').html(html);
                });

            });
        });

        var staffId = "";
        $(document).on("click",".x-rylst li",function(){
            if($(this).hasClass("on")){
                $(this).removeClass("on");
                staffId = "";
            }else{
                $(".x-rylst li").each(function(){
                    $(this).removeClass("on");
                });
                $(this).addClass("on");
                staffId = $(this).data("id");
            }
        });
    </script>

@stop

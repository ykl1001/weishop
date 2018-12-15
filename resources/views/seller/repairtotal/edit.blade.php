@extends('seller._layouts.base')
@section('css')
    <style>
        p{word-wrap:break-word; word-break:normal;}
        .tds tr{background-color: #fff;}
        .y-cancelreason{margin:0 15px;}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
        .zydialog_head{width:600px;}
    </style>
@stop
@section('content')
<div>
    <div class="m-zjgltbg">                 
        <div class="p10">
            <div class="g-fwgl">
                <p class="f-bhtt f14 clearfix">
                    <span class="ml15 fl">报修详情</span>
                </p>
            </div>
            <div class="m-tab m-smfw-ser pt20">
                <div class="m-porbar clearfix">
                    <?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?> 
                    <img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt=" " class="mt20 pt10 clearfix" width="750">
                    <ul class="m-barlst clearfix tc mt20 pt10" style="width:770px;">
                    @foreach($data['statusNameDate'] as $key => $value)
                        @if($data['statusFlowImage'] == 'statusflow_9' && $key == 0)
                            <?php $color = '#000000'; ?>
                        @elseif($data['statusFlowImage'] == 'statusflow_10' && $key == 1)
                            <?php $color = '#000000'; ?>
                        @elseif($data['statusFlowImage'] == 'statusflow_11' && $key == 2)
                            <?php $color = '#000000'; ?>
                        @else
                            @if($value['date']==0)
                                <?php $color = '#ccc'; ?>
                            @else
                                <?php $color = '#000000'; ?>
                            @endif
                        @endif
                        <li style="width:{{$width}};*width:{{$_width}};color:{{$color}}">
                            <p class="tc">{{$value['name']}}</p>
                            <p class="tc">{{ $value['date'] > 0 ? yztime($value['date']) : '' }}</p>
                        </li>
                    @endforeach
                    </ul>
                </div>
                <?php $images = $data['images'] ? explode(',', $data['images']) : ''; ?>
                @yizan_begin
                    <yz:form nobtn="1">
                        <yz:fitem name="types" label="报修类型" type="text">
                            {{ $data['types']['name'] }}
                        </yz:fitem>
                        <yz:fitem name="build" label="楼栋号" type="text">
                            {{ $data['build']['name'] }}
                        </yz:fitem>
                        <yz:fitem name="roomNum" label="房间号" type="text">
                            {{ $data['room']['roomNum'] }}
                        </yz:fitem>

                        <yz:fitem name="apiTime" label="维修时间" type="text">
                            {{ yztime($data['apiTime']) }}
                        </yz:fitem>
                        @if($data['status'] > 0)
                            <yz:fitem name="sellerStaffId" label="维修人员" type="text">
                                {{ yztime($data['staff']['name']) }}
                            </yz:fitem>
                            <yz:fitem name="sellerPhone" label="维修人员电话" type="text">
                                {{ $data['staff']['mobile'] }}
                            </yz:fitem>

                         @endif
                        <yz:fitem name="owner" label="业主" type="text">
                            {{ $data['room']['owner'] }}
                        </yz:fitem>
                        <yz:fitem name="mobile" label="联系电话" type="text">
                            {{ $data['room']['mobile'] }}
                        </yz:fitem>
                        <yz:fitem label="问题描述" >
                            <p style="word-break:break-word;">{{ $data['content'] }}</p>
                            @if($data['images'])
                            @foreach($images as $image)
                            <a href="{{$image}}" target="_blank"><img src="{{$image}}" width="60%" height="50%"></a>
                            <br/>
                            @endforeach
                            @endif
                        </yz:fitem>
                        <div class="u-antt tc" style=" background: #f9f9f9;">
                            @if($data['status'] == 0)
                            <a href="javascript:;" data-type="{{ $data['type'] }}" data-id="{{ $data['id'] }}"  class="mt15 ml15 on m-sjglbcbtn" id="dispose">处理</a>
                            @elseif($data['status'] == 1)
                            <a href="javascript:;" data-status="2" class="mt15 ml15 on m-sjglbcbtn dispose">完成</a>
                            @endif
                            <a href="{{ u('Repair/index') }}" class="mt15 ml15 m-quxiaobtn">返回</a>
                        </div>
                    </yz:form>
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

        $("#dispose").click(function(){
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
    <script type="text/javascript">

$(function() {
        $('.dispose').click(function() {
            var status = $(this).data('status');
            var id = "{{ $data['id'] }}";
            var staffId = "{{$data['sellerStaffId']}}";
            $.post("{{ u('Repair/designate')  }}",{'staffId':staffId,'id':id,'status':status},function(result){
                if(result.code == 0){ 
                    window.location.reload();
                }else{
                    $.ShowAlert(result.msg);
                }
            },'json');
        })
    })
</script>

@stop
@extends('admin._layouts.base')
@section('css')
<style type="text/css">
    .w195{width: 195px;}
    .w163{width: 163px;}
    .ml25{margin-left: 25px;}
    .mt8{margin-top: 8px;}
    .p-addbtn{padding: 5px 10px;cursor: pointer;}
    .p-sellerlist{width: 600px;margin-top: 5px;cursor: pointer;border: solid #ccc;border-width: 1px 1px 0;}
    .p-sellerlist li:hover{background-color: #eee;}
    .p-sellerlist li{border-bottom: 1px solid #ccc;margin: 0;padding: 5px 10px;}

    .p-sellerlist i.fa{float: right;font-size: 16px;}
    .p-sellerlist i.fa:hover{color: red;}

    button.zfbtn{cursor: pointer;}
</style>
@stop

@section('right_content')
	@yizan_begin
        @if($edit)
    		<yz:form id="yz_form" nobtn="1">
                <yz:fitem name="startTime" label="开始时间" type="date"></yz:fitem>
                <yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>
                <yz:fitem label="活动内容">
                    <p>首单满<input type="text" name="fullMoney" class="u-ipttext w163" placeholder="请输入金额" value="{{$data['fullMoney']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元立减&nbsp;<input type="text" name="cutMoney" class="u-ipttext w163" placeholder="请输入金额" value="{{$data['cutMoney']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元</p>
                </yz:fitem>
                <yz:fitem label="适用范围">
                    <yz:radio name="useSeller" options="0,1" texts="所有商家,指定商家" default="0" checked="$data['useSeller']"></yz:radio>
                    <div id="useSellerList" @if($data['useSeller'] != 1) style="display:none" @endif>
                        @if(count($sellerLists) > 0)
                            <ul class="p-sellerlist">
                                @foreach($sellerLists as $key => $value)
                                    <li>
                                        商家名称：<input type="text" value="{{$value}}" class="u-ipttext addseller" data-sellerId='{{$key}}' disabled="true">
                                        <input type="hidden" value="{{$key}}" name="ids[]">
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </yz:fitem>
                <div class="u-fitem clearfix">
                    <span class="f-tt">
                        &nbsp;
                    </span>
                    <div class="f-boxr">
                          <button type="button" class="u-addspbtn2 zfbtn">作&nbsp;&nbsp;废</button>
                    </div>
                </div>
            </yz:form>
        @else
            <yz:form id="yz_form" action="save_new_activity">
                <yz:fitem name="startTime" label="开始时间" type="date"></yz:fitem>
                <yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>
                <yz:fitem label="活动内容">
                    <p>首单满<input type="text" name="fullMoney" class="u-ipttext w163" placeholder="请输入金额" value="{{$data['fullMoney']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元立减&nbsp;<input type="text" name="cutMoney" class="u-ipttext w163" placeholder="请输入金额" value="{{$data['cutMoney']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元</p>
                </yz:fitem>
                <yz:fitem label="适用范围">
                    <yz:radio name="useSeller" options="0,1" texts="所有商家,指定商家" default="0" checked="$data['useSeller']"></yz:radio>
                    <input type="button" value="+添加商家" class="p-addbtn" id="useSellerBtn" @if($data['useSeller'] != 1) style="display:none" @endif>
                    <div id="useSellerList" @if($data['useSeller'] != 1) style="display:none" @endif>
                        @if(count($sellerLists) > 0)
                            <ul class="p-sellerlist">
                                @foreach($sellerLists as $key => $value)
                                    <li>
                                        商家名称：<input type="text" value="{{$value}}" class="u-ipttext addseller" data-sellerId='{{$key}}' disabled="true">
                                        <i class="fa fa-times mt8" aria-hidden="true"></i>
                                        <input type="hidden" value="{{$key}}" name="ids[]">
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </yz:fitem>
            </yz:form>
        @endif
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(function(){
        
        $("input[name='useSeller']").change(function(){
            $.useSeller($(this).val());
        });

        //所有商家,指定商家    
        $.useSeller = function(useSeller){
            if(useSeller == 0)
            {
                $("#useSellerList,#useSellerBtn").hide();
            }
            else
            {
                $("#useSellerList,#useSellerBtn").show();
            }
        }

        //添加指定商家时，保存现有数据
        $("input.p-addbtn").click(function(){
            var saveData = new Object();
            var sellerIds = [];

            $.each($("#useSellerList ul li"), function(k, v){
                sellerIds[k] = $(this).find('input.addseller').attr('data-sellerId');
            });

            saveData.form = $("#yz_form").serializeArray();
            saveData.sellerIds = sellerIds;

            $.post("{{ u('Activity/save_full_data') }}", saveData, function(res){
                window.location.href = "{{ u('Activity/addSeller', ['type' => 4]) }}";
            });
        });

        //删除指定商家
        $(".p-sellerlist li i.fa").click(function(){
            var s = $(this);
            var id = s.siblings("input.addseller").attr('data-sellerId');

            //异步请求
            $.post("{{ u('Activity/deleteSellerIds') }}", {'id':id}, function(res){
                //动画移除
                if(res == 1){
                    s.parents("li").fadeOut(700,function(){
                        $(this).remove();
                    });
                }
            });

        });

        //作废
        $("button.zfbtn").click(function(){
            var status = "{{$data['timeStatus']}}";
            var id = "{{$data['id']}}";

            if(status == 1)
            {
                //进行中，结束
                var statusStr = "活动正在进行中，您确定要作废当前活动？";
            }
            else
            {
                //未开始，已结束，删除
                var statusStr = "您确定要删除活动？";
            }
            
            if(confirm(statusStr))
            {
                $.post("{{ u('Activity/cancellation') }}", {'id':id},function(res){
                    $.ShowAlert(res.msg);

                    if(res.code == 0)
                    {
                        setTimeout(function(){
                            if(status == 1)
                            {
                                window.location.reload();
                            }
                            else
                            {
                                window.location.href = "{{ u('Activity/index') }}";
                            }
                        },2000);
                        
                    }
                })
            }
        });

    })
</script>
@stop
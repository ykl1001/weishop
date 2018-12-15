@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <dl class="m-ddl">
            <dt>平台配送设置</dt>
            <dd class="clearfix">
                <yz:fitem name="systemSendStaffFee" label="配送服务费" val="{{$data['system_send_staff_fee']['val']}}" append="1">
                    <span class="ml5">{{$data['system_send_staff_fee']['tooltip']}}</span>
                </yz:fitem>
                <yz:fitem name="systemSendFee" label="配送抽佣" val="{{$data['system_send_fee']['val']}}" append="1">
                    <span class="ml5">{{$data['system_send_fee']['tooltip']}}</span>
                    <p class="gray">*平台向服务人员收取</p>
                </yz:fitem>
                <yz:fitem name="systemStaffChangeHour" label="状态切换" val="{{$data['system_staff_change_hour']['val']}}" append="1">
                    <span class="ml5">{{$data['system_staff_change_hour']['tooltip']}}</span>
                    <p class="gray">*控制配送人员随意切换上下班状态的时间(设置范围：0~23)</p>
                </yz:fitem>
            </dd>
        </dl>
    </yz:form>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
    $(function(){
        //收费比例
        // $("#systemSendCollectPercent,#systemStaffCollectPercent").keyup(function(){
        //     if(this.value==this.value2)
        //         return;
        //     if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)
        //         this.value=(this.value2)?this.value2:'';
        //     if(this.value>100 || this.value<0)
        //         this.value='';
        //     else
        //         this.value2=this.value;
        // });
        //最低收费标准
        $("#systemSendFee,#systemSendStaffFee").blur(function(){
            if(isNaN(this.value) || this.value=='')
                this.value = 0.00;
            $(this).val(parseFloat($(this).val()).toFixed(2));
        });
        //切换时间
        $("#systemStaffChangeHour").keyup(function(){
            this.value=this.value.replace(/\D/g,'');
            if(this.value < 0 || this.value > 23 )
                this.value = '';
        });
        
    })  
</script>
@stop
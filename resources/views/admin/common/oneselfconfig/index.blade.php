@extends('admin._layouts.base')
<php>
    $options = [1];//1,2
    $texts = ['上门服务'];//,'到店服务'
</php>
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 165px;}
    .x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
    .p-avoidFee{width: 87px;}
</style>
@stop
@section('right_content')
	@yizan_begin
	    <yz:form id="yz_form" action="save">
            <dl class="m-ddl">
                <dt>基本信息</dt>
                <dd>
                    <yz:fitem name="name" label="自营门店"></yz:fitem>
                    <yz:fitem name="logo" label="LOGO" type="image" append="1">
                        <div><small class='cred pl10 gray'>建议尺寸：750px*750px，支持JPG/PNG格式</small></div>
                    </yz:fitem>
                    <yz:fitem name="serviceTel" label="服务号码"></yz:fitem>
                </dd>
            </dl>
            <dl class="m-ddl">
                <dt>基本配置</dt>
                <dd>
                    <!-- <yz:fitem name="businessScope" label="经营范围">
                        <yz:checkbox name="businessScope."  options="$citys" textfield="name" valuefield="id" checked="$data['businessScope']"></yz:checkbox>
                    </yz:fitem> -->
                    <yz:fitem label="经营范围" pcss="send-user-type send-user-group hidden">
                        <div class="input-group">
                            <table border="0">
                                 <tbody>
                                    <tr>
                                        <td rowspan="2">
                                            <select id="user_1" name="user_1" class="form-control" multiple="multiple" style="min-width:200px; height:260px;">
                                                @foreach($citys as $key => $value)
                                                    @if(in_array($value['id'], $data['businessScope']))
                                                        <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="60" align="center" rowspan="2">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('user_2', 'user_1', 1);">
                                                <span class="fa fa-2x fa-angle-double-left"> </span>
                                            </button>
                                            <br><br>
                                            <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('user_2', 'user_1');">
                                                <span class="fa fa-2x fa-angle-left"> </span>
                                            </button>
                                            <br><br>
                                            <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('user_1', 'user_2');">
                                                <span class="fa fa-2x fa-angle-right"> </span>
                                            </button>
                                            <br><br>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('user_1', 'user_2', 1);">
                                                <span class="fa fa-2x fa-angle-double-right"> </span>
                                            </button>
                                            <input type="hidden" name="cityLists" id="cityLists">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                           <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; height:260px;"> 
                                                @foreach($citys as $key => $value)
                                                    @if(!in_array($value['id'], $data['businessScope']))
                                                        <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="blank3"></div>
                        </div> 
                    </yz:fitem> 
					<yz:fitem label="配送方式">
						<yz:radio name="sendWay" options="1" texts="员工配送" checked="$data['sendWay']"></yz:radio>
					</yz:fitem>
                    <yz:fitem label="起送费">
                        <input type="text" name="serviceFee" class="u-ipttext" value="{{ $data['serviceFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
                    </yz:fitem>
                    <yz:fitem label="配送费">
                        <input type="text" name="deliveryFee" class="u-ipttext" value="{{ $data['deliveryFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
                        <p class="mt10">
                            <yz:checkbox name="isAvoidFee" options="1" texts="设置满免" checked="$data['isAvoidFee']"></yz:checkbox>
                            满<input type="text" name="avoidFee" class="u-ipttext ml5 mr5 p-avoidFee p-disabled" value="{{ $data['avoidFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">免配送费
                        </p>
                    </yz:fitem>
                    <yz:fitem name="serviceMode" label="服务方式">
                        <php> $val =  $data['serviceMode'] ? $data['serviceMode'] : 1; </php>
                        <yz:radio name="serviceMode" options="$options" texts="$texts" checked="$val"></yz:radio>
                    </yz:fitem>
                    <!-- <yz:fitem label="可预约天数">
                        <input type="text" name="reserveDays" class="u-ipttext" value="{{ $data['reserveDays'] or 1}}">
                        <span>天</span>
                        <span style="color:#ccc" class="ml10">预约天数不包含当天，最大可预约30天</span>
                    </yz:fitem> -->
                    <div id="-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">
                             可预约天数:
                        </span>
                        <div class="f-boxr">
                              <input type="text" name="reserveDays" class="u-ipttext" value="{{ $data['reserveDays'] or 1}}" onKeyUp="amounts(this)" onBlur="overFormats(this)" maxlength="3">
                            <span>天</span>
                            <span style="color:#ccc" class="ml10">预约天数不包含当天，最大可预约30天</span>
                        </div>
                    </div>
                    <!-- <yz:fitem label="配送时间周期">
                        <input type="text" name="sendLoop" class="u-ipttext" value="{{ $data['sendLoop'] or 30}}">
                        <span>分钟</span>
                    </yz:fitem> -->
                    <div id="-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">
                             配送时间周期:
                        </span>
                        <div class="f-boxr">
                              <input type="text" name="sendLoop" class="u-ipttext" value="{{ $data['sendLoop'] or 30}}" onKeyUp="amounts(this)" onBlur="overFormats(this)" maxlength="7">
                            <span>分钟</span>
                        </div>
                    </div>
                </dd>
            </dl>
            <dl class="m-ddl">
                <dt>营业设置</dt>
                <dd class="clearfix" style="padding:15px;">
                    @include('admin.common.oneselfconfig.showtime')
                    @include('admin.common.oneselfconfig.sztime')
                </dd>
            </dl>
		</yz:form>
	@yizan_end
@stop
@section('js')
    <script type="text/javascript">
    $("#yz_form").submit(function(){
        var ids = new Array(); 
        $("#user_1 option").each(function(){
            ids.push(this.value);
        })
        $("#cityLists").val(ids);
    })
    $.optionMove = function(from, to, isAll){
        var from = $("#" + from);
        var to = $("#" + to);
        var list;
        if(isAll){
            list = $('option', from);
        }else{
            list = $('option:selected', from);
        }
        list.each(function(){
            if($('option[value="' + this.value + '"]', to).length > 0){
                $(this).remove();
            } else {
                $('option', to).attr('selected',false);
                to.append(this);
            }
        });
    }
    function amounts(th){
        var regStrs = [
            ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
            ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
            ['\\.(\\d?)+', '$1'], //禁止录入两个以上的点
            ['^(\\d+\\.\\d{0}).+', '$1'] //禁止录入小数点后两位以上
        ];
        for(i=0; i<regStrs.length; i++){
            var reg = new RegExp(regStrs[i][0]);
            th.value = th.value.replace(reg, regStrs[i][1]);
        }
    }

    function overFormats(th){
        var v = th.value;
        if(v === ''){
            v = '0';
        }else if(v === '0.'){
            v = '0';
        }else if(/^0+\d+\.?\d*.*$/.test(v)){
            v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
            v = inp.getRightPriceFormat(v).val;
        }else if(/^0\.\d$/.test(v)){
             v = v + '0';
        }else if(!/^\d+\.\d{2}$/.test(v)){
            if(/^\d+\.\d{2}.+/.test(v)){
                v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
            }else if(/^\d+\.$/.test(v)){
                v = v.substring(0, v.length-1);
            }else if(/^\d+\.\d$/.test(v)){
                v = v + '0';
            }else if(/^[^\d]+\d+\.?\d*$/.test(v)){
                v = v.replace(/^[^\d]+(\d+\.?\d*)$/, '$1');
            }else if(/\d+/.test(v)){
                v = v.replace(/^[^\d]*(\d+\.?\d*).*$/, '$1');
                ty = false;
            }else if(/^0+\d+\.?\d*$/.test(v)){
                v = v.replace(/^0+(\d+\.?\d*)$/, '$1');
                ty = false;
            }else{
                v = '0';
            }
        }
        th.value = v;
    }
    /**
     * 实时动态强制更改用户录入
     * arg1 inputObject
     **/
    function amount(th){
        var regStrs = [
            ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
            ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
            ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
            ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
        ];
        for(i=0; i<regStrs.length; i++){
            var reg = new RegExp(regStrs[i][0]);
            th.value = th.value.replace(reg, regStrs[i][1]);
        }
    }
    /**
    * 录入完成后，输入模式失去焦点后对录入进行判断并强制更改，并对小数点进行0补全
    * arg1 inputObject
    * 这个函数写得很傻，是我很早以前写的了，没有进行优化，但功能十分齐全，你尝试着使用
    * 其实有一种可以更快速的JavaScript内置函数可以提取杂乱数据中的数字：
    * parseFloat('10');
    **/
    function overFormat(th){
        var v = th.value;
            if(v === ''){
                v = '0.00';
            }else if(v === '0'){
                v = '0.00';
            }else if(v === '0.'){
                v = '0.00';
            }else if(/^0+\d+\.?\d*.*$/.test(v)){
                v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
                v = inp.getRightPriceFormat(v).val;
            }else if(/^0\.\d$/.test(v)){
                 v = v + '0';
            }else if(!/^\d+\.\d{2}$/.test(v)){
                if(/^\d+\.\d{2}.+/.test(v)){
                    v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
                }else if(/^\d+$/.test(v)){
                    v = v + '.00';
                }else if(/^\d+\.$/.test(v)){
                    v = v + '00';
                }else if(/^\d+\.\d$/.test(v)){
                    v = v + '0';
                }else if(/^[^\d]+\d+\.?\d*$/.test(v)){
                    v = v.replace(/^[^\d]+(\d+\.?\d*)$/, '$1');
                }else if(/\d+/.test(v)){
                    v = v.replace(/^[^\d]*(\d+\.?\d*).*$/, '$1');
                    ty = false;
                }else if(/^0+\d+\.?\d*$/.test(v)){
                    v = v.replace(/^0+(\d+\.?\d*)$/, '$1');
                    ty = false;
                }else{
                    v = '0.00';
                }
            }
        th.value = v;
    }
    </script>
@stop

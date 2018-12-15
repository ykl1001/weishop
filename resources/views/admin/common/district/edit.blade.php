@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="小区名称"></yz:fitem>
            <php>
                if($data['third']){
                    $proxy = $data['third'];
                } else if($data['second']){
                    $proxy = $data['second'];
                } else {
                    $proxy = $data['first'];
                }
            </php>
            <div id="proxy" class="u-fitem clearfix">
	            <span class="f-tt">
	                 代理账户:
	            </span>
                <div class="f-boxr">
                    <select id="pid_sle" class="sle" name="proxyId" >
                        <option value="0">请选择</option>
                        @if($proxy)
                            <option value="{{$proxy['id']}}"  selected  >{{$proxy['name']}}</option>
                        @endif
                    </select>
                    <input id="search_key" style="margin-left: 10px;width: 140px;" class="u-ipttext" type="input" placeholder="请输入要搜索的代理账户" />
                    <button type="button" id="proxy_search" class="btn "><i class="fa fa-search"></i> 搜索</button>
                </div>
            </div>
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" new="1" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
			</yz:fitem> 
			<yz:fitem name="address" label="详细地址">
				<yz:map name="address" pointVal="$data['mapPointStr']" addressVal="$data['address']" ></yz:map>
			</yz:fitem>
			<yz:fitem name="houseNum" label="户数"></yz:fitem> 
			<yz:fitem name="areaNum" label="面积" tip="平方米"></yz:fitem>   
			<php>
				$houseData = Lang::get('api.house_type');
				$houseTypes = [];
				foreach($houseData as $key => $value){
					$arr = [];
					$arr['id'] = $key;
					$arr['name'] = $value;
					$houseTypes[] = $arr; 
				}
			</php>
			<yz:fitem label="房产类型">
				<yz:select name="houseType" options="$houseTypes" textfield="name" valuefield="id" selected="$data['houseType']"></yz:select>
			</yz:fitem>
			<yz:fitem name="departTel" label="联系电话"></yz:fitem> 
			<yz:fitem name="departMail" label="电子邮箱"></yz:fitem> 
			<yz:fitem name="departStreet" label="街道/村/门牌号"></yz:fitem>  
			<yz:fitem name="departCommon" label="说明"></yz:fitem> 
		</yz:form> 
	@yizan_end
	<script type="text/javascript">
        $(function(){
            $("input[name='deduct']").attr("maxlength","6");
            $("#proxy_search").click(function(){
                var obj = new Object();
                obj.level = $("#level").val();
                obj.name = $("#search_key").val();
                $.post("{{ u('Proxy/index') }}",obj,function(res){
                    var optionsStr = "";
                    if(res.length > 0){
                        optionsStr = "<option value='0'>请选择</option>";
                        for(var i=0; i<res.length;i++){
                            optionsStr += "<option value='"+res[i].id+"' >"+res[i].name+"</option>";
                        }
                    } else {
                        optionsStr += "<option value='0'>未找到代理信息</option>";
                    }
                    $("#pid_sle").html(optionsStr).trigger("change");
                },'json');
            });
        })
	</script>
@stop
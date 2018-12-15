@extends('wap.community._layouts.base')

@section('show_top')
    <style>
        .bar .icon{line-height: 2.2rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left"  external href="{{ u('Property/index')}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$data['district']['name']}}小区-门禁</h1>
    </header>
@stop
@section('content')
    <div class="content" id='uksort'>        
        <!-- 业主信息 -->
        <div class="list-block media-list x-property bfh0 mb0">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-media">
                            <img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{  asset('wap/community/client/images/wdtx-wzc.png') }} @endif" width="70" height="70">
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title c-gray f14">业主：<span class="c-white">{{$data['name']}}</span></div>
                            </div>
                            <div class="item-subtitle c-gray f14">单元：<span class="c-white">{{$data['build']['name']}}#{{$data['room']['roomNum']}}</span>
                                
                            </div>
                            <div class="item-text c-gray ha f14">电话：<span class="c-white">{{$data['mobile']}}</span></div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <!-- 报修列表 -->
        <div class="list-block media-list bfh0">
            <ul class="nobg">
            @foreach($qryAllKeys as $k=>$item)                  
                    <li>
                        <div class="item-content p0">
                            <div class="item-media"></div>
                            <div class="item-inner ml0 pl10">
                                <div class="fl">
                                    <div class="item-title-row mt5">
                                        <div class="item-title mb5">{{$item['name']}}</div>
                                    </div>
                                    <div class="item-subtitle c-gray f12"></div>
                                    <div class="item-subtitle c-gray f12 mb5">有效期：{{ Time::toDate(Time::toTime($item['msg']['expiretime']),'Y-m-d') }}</div>
                                </div>
                                <span class="content-block x-opendoor_to"  @if($item['is_bind_deivce'] == 1) onclick="getTicket('{{$item['name']}}','{{$item['msg']['device_id']}}','{{$item['msg']['ksid']}}','{{$item['msg']['ktype']}}','{{$item['msg']['mtype']}}')" @else onclick="getTickets('{{$item['msg']['device_id']}}','{{$item['msg']['ksid']}}','{{$item['msg']['ktype']}}','{{$item['msg']['mtype']}}')" @endif>
                                    <span class="x-opendoor tc f12 fr toopendoor mt5">{{$item['is_bind_deivce'] == 1 ? "开门" : "绑定"}}</span>
                                </span>
                            </div>
                        </div>
                    </li>
            @endforeach
            </ul>
        </div> 
        <script type="text/javascript" src='{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}'></script>
        <script type="text/javascript">
            var originalId = "{{$weixin['originalId']}}";
            ;(function(window, undefined){
                //微信分享配置文件
                wx.config({
                    beta: true, // 开启内测接口调用，注入wx.invoke方法,非常重要!!必须有这个
                    debug: false,//开启调试接口，alert运行结果
                    appId: "{{$weixin['appId']}}", // 公众号的唯一标识
                    timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
                    nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
                    signature: "{{$weixin['signature']}}",// 签名
                    jsApiList: [                //需要调用的接口，都得在这里面写一遍
                        "openWXDeviceLib",//初始化设备库（只支持蓝牙设备）
                        "closeWXDeviceLib",//关闭设备库（只支持蓝牙设备）
                        "getWXDeviceInfos",//获取设备信息（获取当前用户已绑定的蓝牙设备列表）
                        "sendDataToWXDevice",//发送数据给设备
                        "startScanWXDevice",//扫描设备（获取周围所有的设备列表，无论绑定还是未被绑定的设备都会扫描到）
                        "stopScanWXDevice",//停止扫描设备
                        "connectWXDevice",//连接设备
                        "disconnectWXDevice",//断开设备连接
                        "getWXDeviceTicket",//获取操作凭证

                        //下面是监听事件：
                        "onWXDeviceBindStateChange",//微信客户端设备绑定状态被改变时触发此事件
                        "onWXDeviceStateChange",//监听连接状态，可以监听连接中、连接上、连接断开
                        "onReceiveDataFromWXDevice",//接收到来自设备的数据时触发
                        "onScanWXDeviceResult",//扫描到某个设备时触发
                        "onWXDeviceBluetoothStateChange",//手机蓝牙打开或关闭时触发
                    ]
                });
                wx.ready(function() {
                    //初始化设备库 需填写参数 公众号的原始ID
                    wx.invoke('openWXDeviceLib', {'brandUserName':originalId}, function(res){
                       /* if(res.err_msg != "openWXDeviceLib:ok"){
                            $.toast('初始化设备失败,无法进行开门!');
                            $(".x-opendoor_to").attr("onclick","$.toast('开门已被禁止，请检查配置!');");
                        }*/
                    });
                    // //开始扫描
                    wx.invoke("startScanWXDevice",{"btVersion":"ble"},function(res){});
                      //设备连接状态改变
                    wx.error(function(res){
                        alert("wx.error错误："+JSON.stringify(res));
                        //如果初始化出错了会调用此方法，没什么特别要注意的
                    });
                });
            })(window);
            //获取ticket，可用于绑定和解绑设备
            function getTicket(name,deviceId,ksid,ktype,mtype){
                 $.modal({
                          title:  name+"房间提示",
                          text: '你将打开"'+name+'"房门',
                          buttons: [
                            {
                              text: '取消',
                            },{
                              text: '开门',
                              onClick: function() {
                                    WeixinJSBridge.invoke('openWXDeviceLib',{'brandUserName':originalId}, function(res) {
                                    if(res.bluetoothState=='off'){
                                        $.toast('蓝牙是关闭状态，请打开手机蓝牙!');
                                    }else{
                                        $.showPreloader();
                                        var data = {deviceId:deviceId,type:'1'};
                                        $.post("{{ u('Unlock/isbindDeivce') }}", {'deviceId':data.deviceId}, function (res) {
                                            if(res.status == 1){
                                                $.post("{{ u('Unlock/openDoor') }}", {'ticket': res.ticket,'ksid': ksid,'ktype': ktype,'mtype': mtype,'deviceId':data.deviceId}, function (res) {
                                                    if(res.code == 0){
                                                        sendData4OpenDoor(data.deviceId,res.msg);
                                                    }else if(res.code == "ERROR_W_022"){
                                                        $.hidePreloader();
                                                        $.toast(res.msg);                                   
                                                    }else {
                                                        $.hidePreloader();
                                                        $.toast("获取钥匙失败！");                                 
                                                    }
                                                }, "json");     
                                            }else{
                                                wx.invoke('getWXDeviceTicket', data, function(res) {                
                                                    if(res.err_msg=='getWXDeviceTicket:ok'){
                                                        $.post("{{ u('Unlock/bindDeivce') }}", {'ticket': res.ticket,'ksid': ksid,'ktype': ktype,'mtype': mtype,'deviceId':data.deviceId,'isopen':1}, function (res) {
                                                                $.hidePreloader();
                                                                sendData4OpenDoor(data.deviceId,res.data.msg);
                                                        }, "json");                  
                                                    }else{
                                                        $.hidePreloader();
                                                        $.toast("获取设备信息失败！");
                                                    }
                                                });     
                                            }
                                        }, "json");                             
                                    }
                                });
                              }
                            }
                          ]
                    });
            }
            //获取ticket，可用于绑定和解绑设备
            function getTickets(deviceId,ksid,ktype,mtype){ 
                 $.showPreloader();
                 WeixinJSBridge.invoke('openWXDeviceLib',{'brandUserName':originalId}, function(res) {
                    if(res.bluetoothState=='off'){
                        $.hidePreloader();
                        $.toast('蓝牙是关闭状态，请打开手机蓝牙!');
                    }else{
                        var data = {deviceId:deviceId,type:'1'};
                        wx.invoke('getWXDeviceTicket', data, function(res) {                
                            if(res.err_msg=='getWXDeviceTicket:ok'){
                                $.post("{{ u('Unlock/bindDeivce') }}", {'ticket': res.ticket,'ksid': ksid,'ktype': ktype,'mtype': mtype,'deviceId':data.deviceId,'isopen':0}, function (res) {
                                        location.reload();
                                }, "json");                  
                            }else{
                                $.hidePreloader();
                                $.toast("绑定失败");
                            }
                        });                             
                    }
                });
            }
            //发送开门数据
            function sendData4OpenDoor(device_id,_base64Data){
                $.hidePreloader();
                var _data = {deviceId:device_id,base64Data:_base64Data};
                wx.invoke('sendDataToWXDevice', _data, function(res) {
                    if(res.err_msg == "sendDataToWXDevice:ok"){
                        $.toast("开门成功");
                        $.post("{{ u('Unlock/openDoorLog') }}", {'errorCode':res.err_msg,'districtId': "{{$data['districtId']}}",'doorId': "{{$data['id']}}",'buildId': "{{$data['buildId']}}",'roomId':"{{$data['roomId']}}"}, function (res) {}, "json"); 
                    }else{
                        $.toast("开门失败");
                    }
                });
            }

            //可以获取用户已绑定的设备以及设备的连接状态等信息。
            function getDevice(device_id,baseData){

                wx.invoke('getWXDeviceInfos',{'brandUserName':originalId}, function(res) {
                        $.toast("getWXDeviceInfos:"+JSON.stringify(res));
                     
                     if(res.err_msg=='getWXDeviceInfos:ok'){
                        sendData4OpenDoor(device_id,baseData);
                    }else{
                        $.toast("连接状态失败");
                    }
                });
            }
        </script>
    </div>
@stop
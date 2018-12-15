<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
	<meta charset="utf-8" />
	<meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1, user-scalable=no"  />
	<meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name='apple-touch-fullscreen' content='yes'>
	<meta name="full-screen" content="yes">
	<title>硬件JSAPI测试</title>
</head>
<body>
	

	<!-- weixin, 重要 -->
	<script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>
	<script src="jquery.min.js"></script>
	
	<!--可以通过getDevice()判断用户是否已经绑定设备，如未绑定的，先进行绑定操作，再发送开门数据。-->
	<script>
	;(function(window, undefined){
		wx.config({
			beta:true,//****重要
			debug:true,//开始测试阶段建议用使用true，正式使用用false
		    appId: '{{ $config['appId'] }}', // 必填，公众号的标识
		    timestamp: '{{ $config['timestamp'] }}', // 必填，生成签名的时间戳
		    nonceStr: '{{ $config['noncestr'] }}', // 必填，生成签名的随机串
		    signature: '{{ $config['signature'] }}',// 必填，签名，见附录1  http://mp.weixin.qq.com/wiki/11/74ad127cc054f6b80759c40f77ec03db.html
			jsApiList : [ 'openWXDeviceLib', 'closeWXDeviceLib',
						'getWXDeviceInfos', 'startScanWXDevice', 'stopScanWXDevice',
						'connectWXDevice', 'disconnectWXDevice', 'sendDataToWXDevice', 'getWXDeviceTicket','onReceiveDataFromWXDevice','onMenuShareAppMessage']
			// 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});
		wx.ready(function() {
			openWXDeviceLib();//硬件功能需要先调用openWXDeviceLib
		});		
	})(window);
	
	function openWXLib(){
		WeixinJSBridge.invoke('openWXDeviceLib',{}, function(res) {
			//根据openWXDeviceLib的结果res做业务判断
			if(res.bluetoothState=='off'){
				alert('蓝牙是关闭状态，请打开手机蓝牙!');
				return;
			}else{
				
			}
		});
	}
	//获取ticket，可用于绑定和解绑设备
	function getTicket(){
		var data = {deviceId:'xxxxx',type:'1'};
		wx.invoke('getWXDeviceTicket', data, function(res) {
			if(res.err_msg=='getWXDeviceTicket:ok'){
				
			}else{
				
			}
		});
	}
	//发送开门数据
	function sendData4OpenDoor(device_id,_base64Data){
		var _data = {deviceId:device_id,base64Data:_base64Data};
		wx.invoke('sendDataToWXDevice', _data, function(res) {
			
		});
	}
	
	//可以获取用户已绑定的设备以及设备的连接状态等信息。
	function getDevice(){
		
		 wx.invoke('getWXDeviceInfos',{}, function(res) {
			
		 });
	}
	
	</script>
</body>
</html>
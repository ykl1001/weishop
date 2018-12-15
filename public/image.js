(function($) {
    "use strict";
	if (!$.FanweImage) {
		$.FanweImage = function (target, params) {
			var fi = this;
			fi.$ = $;
			fi.target = target;
			fi.params = params;

			fi.params.showWidth = fi.$('body').width();
			if (typeof fi.params.showHeight == 'undefined') {
				fi.params.showHeight = parseInt(fi.params.showWidth / fi.params.width * fi.params.height);
			}
			
			fi.naturalWidth = 0;
			fi.naturalHeight = 0;
			fi.hammertime = null;
			fi.img = null;
			fi.isUpload = false;
			fi.data = null;

			fi.dataUriToBlob = function(dataURI) {
				var byteString;
				if (dataURI.split(',')[0].indexOf('base64') >= 0)
					byteString = atob(dataURI.split(',')[1]);
				else
					byteString = unescape(dataURI.split(',')[1]);
				
				var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
				
				var ia = new Uint8Array(byteString.length);
				for (var i = 0; i < byteString.length; i++) {
					ia[i] = byteString.charCodeAt(i);
				}

				return new Blob([ia], {type:mimeString});
			}
			
			fi.getEvenSize = function(num) {
				num = parseInt(num);
				
				if (isNaN(num)) {
					return 0;
				}
				
				if (num % 2 != 0) {
					num--;
				}
				return num;
			}

			fi.compressImg = function(img, data){
				data.r = data.r % 360;
				var cvs = document.createElement('canvas');
				var ctx = cvs.getContext("2d");
				cvs.width = fi.naturalWidth;
				cvs.height = fi.naturalHeight;
				var imgWidth  = img.width;
				var imgHeight = img.height;
				
				var zoom = fi.params.width / fi.params.showWidth;
				var left = fi.getEvenSize(img.style.left),
					top  = fi.getEvenSize(img.style.top);
				
				if (data.r != 0) {
					switch (data.r) {
						case 90:
							cvs.width	= fi.naturalHeight;
							cvs.height	= fi.naturalWidth;
							imgWidth	= img.height;
							imgHeight	= img.width;
							left        = fi.getEvenSize(left - (fi.params.showWidth - img.width) / 2 + (fi.params.showWidth - imgWidth) / 2);
							top         = fi.getEvenSize(top - (fi.params.showHeight - img.height) / 2 + (fi.params.showHeight - imgHeight) / 2);
							ctx.translate(cvs.width, 0);
						break;
						
						case 180:
							ctx.translate(cvs.width, cvs.height);
						break;
						
						case 270:
							cvs.width   = fi.naturalHeight;
							cvs.height  = fi.naturalWidth;
							imgWidth    = img.height;
							imgHeight   = img.width;
							left        = fi.getEvenSize(left - (fi.params.showWidth - img.width) / 2 + (fi.params.showWidth - imgWidth) / 2);
							top         = fi.getEvenSize(top - (fi.params.showHeight - img.height) / 2 + (fi.params.showHeight - imgHeight) / 2);
							ctx.translate(0, cvs.height);
						break;
					}
					ctx.rotate(data.r * Math.PI / 180);
				} else {
					ctx.translate(0, 0);
				}
				ctx.drawImage(img, 0, 0);
				ctx.save();
				
				var cutCvs = document.createElement('canvas');
				var cutCtx = cutCvs.getContext("2d");
				cutCvs.width  = fi.params.width;
				cutCvs.height = fi.params.height;
				
				left = fi.getEvenSize(left * zoom);
				top  = fi.getEvenSize(top * zoom);
				
				cutCtx.translate(left, top);
				cutCtx.drawImage(cvs, 0, 0, cvs.width, cvs.height, 0, 0, fi.getEvenSize(imgWidth * zoom), fi.getEvenSize(imgHeight * zoom));
				
				var newImageData = cutCvs.toDataURL('image/png');
				cvs = null;
				cutCvs = null;
				
				return fi.dataUriToBlob(newImageData);
			}

			fi.resetInput = function() {
				if (fi.fileInput) {
					var parent = $(fi.fileInput).parent();
					var html = parent.html();
					parent.html(html);
				}
			}

			fi.destroy = function () {
				fi.isUpload = false;
				if (fi.hammertime != null) {
					fi.hammertime.destroy();
				}
			}
			
			fi.init = function() {
				$.PopupFanweImage();
				$(document).off('change','.popup-image #selectCutImageInput');
				$(document).on('change','.popup-image #selectCutImageInput', function () {
					$.showIndicator();
					fi.fileInput = this;
					fi.cut();
				});
				
				if(window.App){
					window.CutCallBack = function(base64Img) {
						fi.$.hideIndicator();
						fi.data = base64Img;
						fi.load();
					}
					
					$(document).off('click','.popup-image #selectAppCutImage');
					$(document).on('click','.popup-image #selectAppCutImage', function () {
						//$.showIndicator();
						App.CutPhoto('{"w":'+ 1000 +',"h":'+1000+'}');
					});
				}
			}
			
			fi.show = function(width, height) {
				fi.$.hideIndicator();
				var photoBox = fi.$(".popup-image .cut-photo");
				var btnsBox = fi.$(".popup-image .cut-btns");
				var handlersBox = fi.$(".popup-image .cut-handlers");
				
				photoBox.removeClass('none');
				handlersBox.removeClass('none');
				btnsBox.addClass('none');
				
				var handlersBoxHeight = handlersBox.height() / 2;
				var box = photoBox.find('.cut-photo-box');
				box.width(fi.params.showWidth).height(fi.params.showHeight).css({"margin-left": (-fi.params.showWidth / 2) + 'px', "margin-top": (-fi.params.showHeight / 2 - handlersBoxHeight) + 'px'});
				
				fi.img = photoBox.find('img').get(0);
				fi.img.src = fi.data;
				fi.img.width = width;
				fi.img.height = height;
				fi.img.style.left = fi.getEvenSize((fi.params.showWidth - width) / 2) + "px";
				fi.img.style.top  = fi.getEvenSize((fi.params.showHeight - height) / 2) + "px";
				fi.img.style.transform = "";
				fi.data = null;

				var resetOptions = { x: 0, y: 0, w: 0, h: 0, s:1, a:0, r:0};
				var resetInit = function() {
					var left = fi.getEvenSize(fi.img.style.left),
						top  = fi.getEvenSize(fi.img.style.top);

					resetOptions.x = left;
					resetOptions.y = top;
					resetOptions.s = 1;
					resetOptions.w = fi.img.width;
					resetOptions.h = fi.img.height;
				}
				resetInit();
				
				handlersBox.off('click', '.rotate');
				handlersBox.on('click', '.rotate', function(){
					resetOptions.r += 90;
					photoBox.find('img').animate({rotate:resetOptions.r + 'deg'}, 200, 'ease-out');
				});
				
				handlersBox.off('click', '.save');
				handlersBox.on('click', '.save', function(){
					if (fi.isUpload) {
						return;
					}
					$.showIndicator();
					fi.isUpload = true;

					fi.$.post(SITE_URL + '/Resource/getformargs?type=mobile', function (result) {
						var fd = new FormData();
						for (var k in result.args) {
							fd.append(k, result.args[k]);
						}
						fd.append(result.save_path.name, result.save_path.path);
						fd.append('file', fi.compressImg(fi.img, resetOptions), 'fanwe.png');

						var xhr = new XMLHttpRequest();
						xhr.addEventListener("load", function(event) {
							$.hideIndicator();
							if (event.target.status == 200 || event.target.status == 201) {
								if (typeof fi.params.callback != 'function') {
									eval(fi.params.callback + '("' + result.image_url + '", fi.target)');
								} else {
									fi.params.callback.call(fi, result.image_url, fi.target);
								}
								fi.$.closeModal();
								fi.$.toast("上传图片成功");
								fi.resetInput();
								fi.destroy();
							} else {
								fi.$.toast("上传图片失败");
							}
						}, false);

						xhr.addEventListener("error", function(event) {
							$.hideIndicator();
							fi.$.toast("上传图片失败");
						}, false);
						xhr.open("POST", result.action);
						xhr.send(fd);
					}, "json");
				});
				
				handlersBox.off('click', '.refresh');
				handlersBox.on('click', '.refresh', function(){
					photoBox.addClass('none');
					handlersBox.addClass('none');
					btnsBox.removeClass('none');
					fi.resetInput();
					fi.destroy();
				});

				fi.hammertime = new Hammer(box.get(0), {domEvents:true});
				fi.hammertime.add(new Hammer.Pinch());
				
				box.on('mousewheel', function(e){
					if (e.deltaY > 0) {
						resetOptions.s += 0.1;
					} else {
						resetOptions.s -= 0.1; 
					}
					fi.img.style.left = fi.getEvenSize(resetOptions.x * resetOptions.s) + "px";
					fi.img.style.top = fi.getEvenSize(resetOptions.y * resetOptions.s) + "px";
					fi.img.width  = fi.getEvenSize(resetOptions.w * resetOptions.s);
					fi.img.height = fi.getEvenSize(resetOptions.h * resetOptions.s);
				});

				fi.hammertime.on("panstart", function (event){
					resetInit();
					event.preventDefault();
				});

				fi.hammertime.on("panmove", function (event){
					fi.img.style.left = fi.getEvenSize(resetOptions.x + event.deltaX) + "px";
					fi.img.style.top  = fi.getEvenSize(resetOptions.y + event.deltaY) + "px";
					event.preventDefault();
				});

				fi.hammertime.on("pinchstart", function (event){
					resetInit();
				});

				fi.hammertime.on("pinchmove", function (event){
					fi.img.style.left = fi.getEvenSize(resetOptions.x * event.scale) + "px";
					fi.img.style.top  = fi.getEvenSize(resetOptions.y * event.scale) + "px";
					fi.img.width  = fi.getEvenSize(resetOptions.w * event.scale);
					fi.img.height = fi.getEvenSize(resetOptions.h * event.scale);
				});
			}
			
			fi.load = function() {
				var loadimg = new Image();
				loadimg.onload = function () {
					var zoom    = 1;
					var width   = fi.naturalWidth = loadimg.naturalWidth;
					var height  = fi.naturalHeight = loadimg.naturalHeight;
					var maxSize = 1020;

					if (loadimg.naturalWidth > maxSize || loadimg.naturalHeight > maxSize) {
						if (loadimg.naturalWidth / maxSize > loadimg.naturalHeight / maxSize) {
							zoom = maxSize / loadimg.naturalWidth;
						} else {
							zoom = maxSize / loadimg.naturalHeight;
						}

						width = fi.getEvenSize(loadimg.naturalWidth * zoom);
						height = fi.getEvenSize(loadimg.naturalHeight * zoom);
						
						fi.naturalWidth = width;
						fi.naturalHeight = height;
						
						var cvs = document.createElement('canvas');
						var ctx = cvs.getContext("2d");
						cvs.width = width;
						cvs.height = height;
						ctx.translate(0, 0);
						ctx.drawImage(loadimg, 0, 0, loadimg.naturalWidth, loadimg.naturalHeight, 0, 0, width, height);
						loadimg = null;
						fi.data = cvs.toDataURL('image/png');
					}

					if (width != fi.params.showWidth || height != fi.params.showHeight) {
						if (fi.params.showWidth / width < fi.params.showHeight / height) {
							zoom = fi.params.showHeight / height;
						} else {
							zoom = fi.params.showWidth / width;
						}
						width  = fi.getEvenSize(width * zoom);
						height = fi.getEvenSize(height * zoom);
					}
					fi.show(width, height);
				}
				loadimg.src = fi.data;
			}
			
			fi.cut = function() {
				if (fi.fileInput.files.length == 0) {
					fi.$.hideIndicator();
					return;
				}
				
				var reader = new FileReader();
				reader.onload = function (e) {
					fi.data = e.target.result;
					e = null;
					reader = null;
					fi.load();
				}
				reader.readAsDataURL(fi.fileInput.files[0]);
			}
			fi.init();
		};
		
		$.PopupFanweImage = function(params) {
			var html = '<div class="popup popup-image">' + 
						'	<div class="content-block">' +
						'		<header class="bar bar-nav">' +
						'			<a href="#" class="icon iconfont pull-left cut-close">&#xe69a;</a>' +
						'			<h1 class="title">选择图片</h1>' +
						'		</header>' +
						'		<div class="content cut-content">' +
						'			<div class="cut-btns">' +
						'				<div class="content-block">' +
						(!window.App ? '	<label for="selectCutImageInput"><span class="button button-big button-fill">选择图片</span></label>' : '') +
						(window.App ? '		<p><span id="selectAppCutImage" class="button button-big button-fill">选择图片</span></p>' : '') +
						'					<span><input id="selectCutImageInput" type="file" accept="image/*" class="none"/></span>' +
						'				</div>' +
						'			</div>' +
						'			<div class="cut-photo none">' +
						'				<div class="cut-photo-box">' +
						'					<div></div>' +
						'					<img />' +
						'				</div>' +
						'			</div>' +
						'			<nav class="bar bar-tab cut-handlers none">' +
						'				<a class="tab-item rotate" href="#">' +
						'					<i class="icon iconfont">&#xe699;</i>' +
						'					<span class="tab-label">旋转</span>' +
						'				</a>' +
						'				<a class="tab-item save" href="#">' +
						'					<i class="icon iconfont">&#xe697;</i>' +
						'					<span class="tab-label">保存</span>' +
						'				</a>' +
						'				<a class="tab-item refresh" href="#">' +
						'					<i class="icon iconfont">&#xe698;</i>' +
						'					<span class="tab-label">重选</span>' +
						'				</a>' +
						'			</nav>' +
						'		</div>' +
						'	</div>' +
						'</div>';
			$.popup(html, true);
			
			$(document).off('click','.popup-image .cut-close');
			$(document).on('click','.popup-image .cut-close', function () {
				$.closeModal('.popup-image');
			});
		}
		
		$.fn.fanweImage = function (params) {
			return new $.FanweImage(this.get(0), params);
		};
	}
})(Zepto);
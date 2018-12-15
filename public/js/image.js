(function($) {
    "use strict";

    var FanweImage = function (fileInput, params) {


		
        var fi = this;
        fi.fileInput = fileInput;
        fi.params = params;

        fi.params.showWidth = $("body").width();
        if (typeof fi.params.showHeight == 'undefined') {
            fi.params.showHeight = Math.min($("body").height(),parseInt(fi.params.showWidth / fi.params.width * fi.params.height));
        }
		
        fi.$ = $;
        fi.naturalWidth = 0;
        fi.naturalHeight = 0;
        fi.cutphotoTop = 0;
        fi.cutphotoLeft = 0;
        fi.img = null;
        fi.hammertime = null;
        fi.isUpload = false;
        fi.data = null;
		fi.appimg = (typeof(params.appimg)!= 'undefined' && params.appimg.length)?params.appimg:false;
		
		
        //图片信息读取
        fi.exifReader = function(file) {
            var TiffTags = {
                0x0100: "ImageWidth",
                0x0101: "ImageHeight",
                0x8769: "ExifIFDPointer",
                0x8825: "GPSInfoIFDPointer",
                0xA005: "InteroperabilityIFDPointer",
                0x0102: "BitsPerSample",
                0x0103: "Compression",
                0x0106: "PhotometricInterpretation",
                0x0112: "Orientation",
                0x0115: "SamplesPerPixel",
                0x011C: "PlanarConfiguration",
                0x0212: "YCbCrSubSampling",
                0x0213: "YCbCrPositioning",
                0x011A: "XResolution",
                0x011B: "YResolution",
                0x0128: "ResolutionUnit",
                0x0111: "StripOffsets",
                0x0116: "RowsPerStrip",
                0x0117: "StripByteCounts",
                0x0201: "JPEGInterchangeFormat",
                0x0202: "JPEGInterchangeFormatLength",
                0x012D: "TransferFunction",
                0x013E: "WhitePoint",
                0x013F: "PrimaryChromaticities",
                0x0211: "YCbCrCoefficients",
                0x0214: "ReferenceBlackWhite",
                0x0132: "DateTime",
                0x010E: "ImageDescription",
                0x010F: "Make",
                0x0110: "Model",
                0x0131: "Software",
                0x013B: "Artist",
                0x8298: "Copyright"
            };

            function getStringFromDB(buffer, start, length) {
                var outstr = "";
                for (var n = start; n < start + length; n++) {
                    outstr += String.fromCharCode(buffer.getUint8(n));
                }
                return outstr;
            }

            function readEXIFData(file, start) {
                if (getStringFromDB(file, start, 4) != "Exif") {
                    return null;
                }

                var bigEnd,
                    tags, tag,
                    exifData, gpsData,
                    tiffOffset = start + 6;

                // test for TIFF validity and endianness
                if (file.getUint16(tiffOffset) == 0x4949) {
                    bigEnd = false;
                } else if (file.getUint16(tiffOffset) == 0x4D4D) {
                    bigEnd = true;
                } else {
                    return null;
                }

                if (file.getUint16(tiffOffset + 2, !bigEnd) != 0x002A) {
                    return null;
                }

                var firstIFDOffset = file.getUint32(tiffOffset + 4, !bigEnd);

                if (firstIFDOffset < 0x00000008) {
                    return null;
                }

                return readTags(file, tiffOffset, tiffOffset + firstIFDOffset, TiffTags, bigEnd);
            }

            function readTags(file, tiffStart, dirStart, strings, bigEnd) {
                var entries = file.getUint16(dirStart, !bigEnd),
                    tags = {},
                    entryOffset, tag,
                    i;

                for (i = 0; i < entries; i++) {
                    entryOffset = dirStart + i * 12 + 2;
                    tag = strings[file.getUint16(entryOffset, !bigEnd)];
                    tags[tag] = readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd);
                }
                return tags;
            }

            function readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd) {
                var type = file.getUint16(entryOffset + 2, !bigEnd),
                    numValues = file.getUint32(entryOffset + 4, !bigEnd),
                    valueOffset = file.getUint32(entryOffset + 8, !bigEnd) + tiffStart,
                    offset,
                    vals, val, n,
                    numerator, denominator;

                switch (type) {
                    case 1: // byte, 8-bit unsigned int
                    case 7: // undefined, 8-bit byte, value depending on field
                        if (numValues == 1) {
                            return file.getUint8(entryOffset + 8, !bigEnd);
                        } else {
                            offset = numValues > 4 ? valueOffset : (entryOffset + 8);
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                vals[n] = file.getUint8(offset + n);
                            }
                            return vals;
                        }

                    case 2: // ascii, 8-bit byte
                        offset = numValues > 4 ? valueOffset : (entryOffset + 8);
                        return getStringFromDB(file, offset, numValues - 1);

                    case 3: // short, 16 bit int
                        if (numValues == 1) {
                            return file.getUint16(entryOffset + 8, !bigEnd);
                        } else {
                            offset = numValues > 2 ? valueOffset : (entryOffset + 8);
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                vals[n] = file.getUint16(offset + 2 * n, !bigEnd);
                            }
                            return vals;
                        }

                    case 4: // long, 32 bit int
                        if (numValues == 1) {
                            return file.getUint32(entryOffset + 8, !bigEnd);
                        } else {
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                vals[n] = file.getUint32(valueOffset + 4 * n, !bigEnd);
                            }
                            return vals;
                        }

                    case 5:    // rational = two long values, first is numerator, second is denominator
                        if (numValues == 1) {
                            numerator = file.getUint32(valueOffset, !bigEnd);
                            denominator = file.getUint32(valueOffset + 4, !bigEnd);
                            val = new Number(numerator / denominator);
                            val.numerator = numerator;
                            val.denominator = denominator;
                            return val;
                        } else {
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                numerator = file.getUint32(valueOffset + 8 * n, !bigEnd);
                                denominator = file.getUint32(valueOffset + 4 + 8 * n, !bigEnd);
                                vals[n] = new Number(numerator / denominator);
                                vals[n].numerator = numerator;
                                vals[n].denominator = denominator;
                            }
                            return vals;
                        }

                    case 9: // slong, 32 bit signed int
                        if (numValues == 1) {
                            return file.getInt32(entryOffset + 8, !bigEnd);
                        } else {
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                vals[n] = file.getInt32(valueOffset + 4 * n, !bigEnd);
                            }
                            return vals;
                        }

                    case 10: // signed rational, two slongs, first is numerator, second is denominator
                        if (numValues == 1) {
                            return file.getInt32(valueOffset, !bigEnd) / file.getInt32(valueOffset + 4, !bigEnd);
                        } else {
                            vals = [];
                            for (n = 0; n < numValues; n++) {
                                vals[n] = file.getInt32(valueOffset + 8 * n, !bigEnd) / file.getInt32(valueOffset + 4 + 8 * n, !bigEnd);
                            }
                            return vals;
                        }
                }
            }

            var dataView = new DataView(file);

            if ((dataView.getUint8(0) != 0xFF) ||
                (dataView.getUint8(1) != 0xD8)) {
                return;
            }

            var offset = 2,
                length = file.byteLength,
                marker;

            while (offset < length) {
                if (dataView.getUint8(offset) != 0xFF) {
                    return null;
                }

                marker = dataView.getUint8(offset + 1);

                if (marker == 225) {
                    return readEXIFData(dataView, offset + 4, dataView.getUint16(offset + 2) - 2);
                } else {
                    offset += 2 + dataView.getUint16(offset + 2);
                }
            }
        }

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

        fi.compressImg = function(img){
			
            var cvs = document.createElement('canvas');
            var ctx = cvs.getContext("2d");
            cvs.width = fi.params.width;
            cvs.height = fi.params.height;

            var left = parseFloat(img.style.left),
                top = parseFloat(img.style.top);

            if (isNaN(left)) left = 0;
            if (isNaN(top)) top = 0;

            var zoom = fi.params.width / fi.params.showWidth;

            ctx.translate(left * zoom, top * zoom);
			
			ctx.drawImage(img, 0, 0, fi.naturalWidth, fi.naturalHeight, 0, 0, img.width * zoom, img.height * zoom);

            var newImageData = cvs.toDataURL('image/png');
            return fi.dataUriToBlob(newImageData);
        }

        fi.resetInput = function() {
            var html = '<input id="' + fi.fileInput.id + '" type="file" accept="image/*" style="display:none" />';
            $(fi.fileInput).after(html);
            $(fi.fileInput).remove();
        }

        fi.touchMoveFun = function (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        fi.destroy = function () {
            if (fi.hammertime != null) {
                fi.hammertime.destroy();
            }
            //fi.$(document).off("touchmove", fi.touchMoveFun);
        }
		//保存按钮
		fi.uploadimg = function(){
			if (fi.isUpload) {
				return;
			}
			//$.showIndicator();
			$.showPreloader("图片上传中...");
			fi.isUpload = true;

			fi.$.post(SITE_URL + '/Resource/getformargs?type=mobile', function (result) {
				var fd = new FormData();
				for (var k in result.args) {
					fd.append(k, result.args[k]);
				}
				
				fd.append(result.save_path.name, result.save_path.path);
				fd.append('file', fi.compressImg(fi.img), 'fanwe.png');
				
				

				var xhr = new XMLHttpRequest();

				xhr.addEventListener("load", function(event) {
					//$.hideIndicator();
					$.hidePreloader();
					if (event.target.status == 201) {
						fi.$.closeModal();
						fi.$.toast("上传图片成功");
						fi.resetInput();
						fi.destroy();
						fi.params.callback.call(fi, result.image_url);
					} else {
						fi.$.toast("上传图片失败");
					}
				}, false);
				xhr.addEventListener("error", function(event) {
					//$.hideIndicator();
					$.hidePreloader();
					fi.$.toast("上传图片失败");
				}, false);
				xhr.open("POST", result.action);
				xhr.send(fd);
			}, "json");
		}
		
        fi.init = function() {
            if (fi.fileInput.files.length == 0 && !fi.appimg) return;
			//弹出层
 			$(".popup-cutimg").remove();
			var popup_img  = '<div class="popup popup-cutimg fanwecutphoto"><div class="modal-title" style="margin:0;"><a onclick="FANWE.JS_BACK_HANDLER = NULL;" class="button button-link button-nav pull-left open-popup button-fill button-danger close-popup" style="line-height: inherit;">返回</a>裁剪图片<a class="button button-link button-nav pull-right open-popup button-fill button-danger button-save" style="line-height: inherit;">保存</a></div><div class="modal-text cutdesk" style="margin:0;"><div class="cutbox-wrap"><div class="cutphoto" style="width:' + fi.params.showWidth + 'px;height:' + fi.params.showHeight + 'px;text-align: initial;"><div class="line" style="width:' + (fi.params.showWidth-4) + 'px;height:' + (fi.params.showHeight-4) + 'px;"><p style="text-align:center; line-height:' + fi.params.showHeight + 'px;">加载图片中...</p></div></div> </div></div></div>';
            $("body").append(popup_img);
			
			$(".button-save").bind("click",fi.uploadimg);
			
			
            var divBox = $(".popup-cutimg .cutphoto");
            

            var initImage = function(width, height) {
                divBox.children(".line").html('');
				var imgoffsetX = Math.max(0,($("body").width() - fi.params.showWidth)/2);
				var imgoffsetY = Math.max(0,($("body").height() - fi.params.showHeight)/2);
                fi.img = null;
                fi.img = document.createElement("img");
                fi.img.className = 'imgcut';
                fi.img.src = fi.appimg?fi.appimg:fi.data;
                fi.img.style.width = $("body").width() + "px";
                fi.img.style.position = "absolute";
				fi.img.style.left = (0 - imgoffsetX) + "px";
				//fi.img.style.top = (0 - imgoffsetY) + "px";
                divBox.children(".line").append(fi.img);
				//背景图
                fi.imgback = null;
                fi.imgback = document.createElement("img");
                fi.imgback.className = 'imgback';
                fi.imgback.src = fi.appimg?fi.appimg:fi.data;
                fi.imgback.style.width = $("body").width() + "px";
                fi.imgback.style.position = "absolute";
				fi.imgback.style.left = "0px";
				fi.imgback.style.top = "0px";
                divBox.after(fi.imgback);
				

					
				
				
                fi.data = null;

                var resetOptions = { x: 0, y: 0, w: 0, h: 0, s:1, a:0};
                var resetInit = function() {
                    var left = parseFloat(fi.imgback.style.left),
                        top = parseFloat(fi.imgback.style.top);

                    if (isNaN(left)) left = 0;
                    if (isNaN(top)) top = 0;

                    resetOptions.x = left;
                    resetOptions.y = top;
                    resetOptions.w = fi.imgback.width;
                    resetOptions.h = fi.imgback.height;
                }
				var cutbox = document.getElementsByClassName("cutphoto")[0];
				cutbox.style.position = "absolute";
				cutbox.style.zIndex = 99999;
				cutbox.style.top = imgoffsetY + "px";
				cutbox.style.left = imgoffsetX + "px";
				fi.cutphotoLeft = imgoffsetX;
				
                fi.hammertime = new Hammer(fi.$(".cutphoto").get(0), {domEvents:true});
                fi.hammertime.add(new Hammer.Pinch());

                fi.hammertime.on("panstart", function (event){
                    resetInit();
                    event.preventDefault();
                });

                fi.hammertime.on("panmove", function (event){
                    fi.img.style.left = (resetOptions.x + (0 - (fi.cutphotoLeft - event.deltaX))) - 2 + "px";
                    fi.img.style.top = (resetOptions.y + (0 - (fi.cutphotoTop - event.deltaY))) - 2 + "px";
					
                    fi.imgback.style.left = (resetOptions.x + event.deltaX) + "px";
                    fi.imgback.style.top = (resetOptions.y + event.deltaY) + "px";
                    event.preventDefault();
                });

                fi.hammertime.on("pinchstart", function (event){
					$(".imgcut").css("width","auto");
					$(".imgback").css("width","auto");
                    resetInit();
                });

                fi.hammertime.on("pinchmove", function (event){
                    fi.img.style.left = (resetOptions.x * event.scale) - fi.cutphotoLeft + "px";
                    fi.img.style.top = (resetOptions.y * event.scale) - fi.cutphotoTop + "px";
                    fi.img.width = resetOptions.w * event.scale;
                    fi.img.height = resetOptions.h * event.scale;
					
                    fi.imgback.style.left = (resetOptions.x * event.scale) + "px";
                    fi.imgback.style.top = (resetOptions.y * event.scale) + "px";
                    fi.imgback.width = resetOptions.w * event.scale;
                    fi.imgback.height = resetOptions.h * event.scale;
                });

				//-------img----
                fi.hammertimeImg = new Hammer(fi.imgback, {domEvents:true});
                fi.hammertimeImg.add(new Hammer.Pinch());

                fi.hammertimeImg.on("panstart", function (event){
                    resetInit();
                    event.preventDefault();
                });

                fi.hammertimeImg.on("panmove", function (event){
                    fi.img.style.left = (resetOptions.x + (0 - (fi.cutphotoLeft - event.deltaX))) - 2 + "px";
                    fi.img.style.top = (resetOptions.y + (0 - (fi.cutphotoTop - event.deltaY))) - 2 + "px";
					
                    fi.imgback.style.left = (resetOptions.x + event.deltaX) + "px";
                    fi.imgback.style.top = (resetOptions.y + event.deltaY) + "px";
                    event.preventDefault();
                });

                fi.hammertimeImg.on("pinchstart", function (event){
					$(".imgcut").css("width","auto");
					$(".imgback").css("width","auto");
                    resetInit();
                });

                fi.hammertimeImg.on("pinchmove", function (event){
                    fi.img.style.left = (resetOptions.x * event.scale) - fi.cutphotoLeft + "px";
                    fi.img.style.top = (resetOptions.y * event.scale) - fi.cutphotoTop + "px";
                    fi.img.width = resetOptions.w * event.scale;
                    fi.img.height = resetOptions.h * event.scale;
					
                    fi.imgback.style.left = (resetOptions.x * event.scale) + "px";
                    fi.imgback.style.top = (resetOptions.y * event.scale) + "px";
                    fi.imgback.width = resetOptions.w * event.scale;
                    fi.imgback.height = resetOptions.h * event.scale;
                });
                //fi.$(document).on("touchmove", fi.touchMoveFun);
				$.hideIndicator();
				var popup = $.popup(".popup-cutimg");
				var cutphotoTop = fi.cutphotoTop = Math.max(0,($("body").height() - fi.params.showHeight - $(".modal-title").height()) / 2);
				var imgcutTop = Math.max((0-cutphotoTop),((fi.params.showHeight - ($("body").width()/fi.naturalWidth ) * fi.naturalHeight) / 2 - 2));
				$(".imgcut").css("top", imgcutTop+ "px");
				$(".imgback").css("top",Math.max(0,($("body").height() - ($("body").width()/fi.naturalWidth ) * fi.naturalHeight - $(".modal-title").height()) / 2) + "px");
				$(".cutphoto").css("top",cutphotoTop + "px");
                FANWE.JS_BACK_HANDLER = function() {
                    $.closeModal();
                    return true;
                }
            }
			
			
			var loadImg = function(tags){
					var loadimg = new Image();
					var loadimgTmp = new Image();
					loadimg.onload = function () {
						fi.data = null;
						var zoom = 1;
						var width = fi.naturalWidth = loadimg.naturalWidth;
						var height = fi.naturalHeight = loadimg.naturalHeight;
						var maxSize = 1000;

						if (loadimg.naturalWidth > maxSize || loadimg.naturalHeight > maxSize) {
							if (loadimg.naturalWidth / maxSize > loadimg.naturalHeight / maxSize) {
								zoom = maxSize / loadimg.naturalWidth;
							} else {
								zoom = maxSize / loadimg.naturalHeight;
							}

							width = parseInt(loadimg.naturalWidth * zoom);
							height = parseInt(loadimg.naturalHeight * zoom);
							
						}
						
						if (width % 2 != 0) {
							width--;
						}

						if (height % 2 != 0) {
							height--;
						}

						fi.naturalWidth = width;
						fi.naturalHeight = height;

						var cvs = document.createElement('canvas');
						var ctx = cvs.getContext("2d");

						switch (tags.Orientation) {
							case 5:
							case 6:
							case 7:
							case 8:
								fi.naturalWidth = cvs.width = height;
								fi.naturalHeight = cvs.height = width;
								break;
							default:
								cvs.width = width;
								cvs.height = height;
						}

						switch (tags.Orientation) {
							case 2:
								// horizontal flip
								ctx.translate(width, 0);
								ctx.scale(-1, 1);
								break;
							case 3:
								// 180 rotate left
								ctx.translate(width, height);
								ctx.rotate(Math.PI);
								break;
							case 4:
								// vertical flip
								ctx.translate(0, height);
								ctx.scale(1, -1);
								break;
							case 5:
								// vertical flip + 90 rotate right
								ctx.rotate(0.5 * Math.PI);
								ctx.scale(1, -1);
								break;
							case 6:
								// 90 rotate right
								ctx.rotate(0.5 * Math.PI);
								ctx.translate(0, -height);
								break;
							case 7:
								// horizontal flip + 90 rotate right
								ctx.rotate(0.5 * Math.PI);
								ctx.translate(width, -height);
								ctx.scale(-1, 1);
								break;
							case 8:
								// 90 rotate left
								ctx.rotate(-0.5 * Math.PI);
								ctx.translate(-width, 0);
								break;
							default:
								break;
						}

						ctx.drawImage(loadimg, 0, 0, loadimg.naturalWidth, loadimg.naturalHeight, 0, 0, width, height);
						loadimg = null;
						if(!fi.appimg)
							fi.data = cvs.toDataURL('image/png');
						else 
							fi.appimg = cvs.toDataURL('image/png');

						width = cvs.width;
						height = cvs.height;

						if (width != fi.params.showWidth || height != fi.params.showHeight) {
							if (fi.params.showWidth / width < fi.params.showHeight / height) {
								zoom = fi.params.showHeight / height;
							} else {
								zoom = fi.params.showWidth / width;
							}
							width  = width * zoom;
							height = height * zoom;
						}
						initImage(width, height);
					}
					
					loadimg.src = fi.appimg?fi.appimg:fi.data;
			}
			
			if(!fi.appimg){
				var reader = new FileReader();
				reader.onload = function (e) {
					fi.data = e.target.result;
					e = null;
					reader = null;
					var tags = fi.exifReader(fi.data);
					tags = tags ? tags : { Orientation: 1 };

					var reader1 = new FileReader();
					reader1.onload = function (e1) {
						fi.data = e1.target.result;
						e1 = null;
						reader1 = null;
						loadImg(tags);
						
					}
					reader1.readAsDataURL(fi.fileInput.files[0]);
				}
				reader.readAsArrayBuffer(fi.fileInput.files[0]);
 			}else{
				var tags = { Orientation: 1 };;
				loadImg(tags);
			} 


        }

        fi.init();
    };

    $.FanweImage = FanweImage;

    $.fn.fanweImage = function (params) {
        return new $.FanweImage(this.get(0), params);
    };
	

})(Zepto);

window.CutCallBackBefore = function (result){
	if(result == 1){
		$.showIndicator();
	}else{
		$.toast("获取图片失败！");
	}
}



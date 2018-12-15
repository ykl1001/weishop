<script charset="utf-8" src="//map.qq.com/api/js?v=2.exp&key={{ Config::get('app.qq_map.key') }}&libraries=geometry,convertor"></script>
<script type="text/javascript" src="//3gimg.qq.com/lightmap/components/geolocation/geolocation.min.js"></script>
<script>
    $.gpsPosition = function (resultFun) {
        var isTranslatePoint = false;
        var gpsLatLng = null;
        var qqcGeocoder = new qq.maps.Geocoder({complete: function (result) {
                var nowNearPoi = null;
                var nearPoi;
                for(var nearPoiKey in result.detail.nearPois){
                    nearPoi = result.detail.nearPois[nearPoiKey];
                    if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                        nowNearPoi = nearPoi;
                    }
                }

                var address  = result.detail.address;
                if (nowNearPoi) {
                    if(nowNearPoi.address != "" && nowNearPoi.address.indexOf(nowNearPoi.name) > -1){
                        nowNearPoi.address = "";
                    }
                    address = nowNearPoi.address + nowNearPoi.name;
                } else {
                    if (result.detail.addressComponents.streetNumber != '' && address.indexOf(result.detail.addressComponents.streetNumber) > -1) {
                        address = address.replace(result.detail.addressComponents.streetNumber, '');
                    }
                    if(result.detail.addressComponents.street != result.detail.addressComponents.streetNumber){
                        address += result.detail.addressComponents.street + result.detail.addressComponents.streetNumber;
                    }else{
                        address += result.detail.addressComponents.street;
                    }
                }

                var reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                address = address.replace(reg, '');

                var mapPointStr = gpsLatLng.lat + "," + gpsLatLng.lng;
                resultFun.call(this, gpsLatLng, result.detail.addressComponents.city, address, mapPointStr, result.detail.addressComponents.district);
            }
        });

        if($.device.osVersion == undefined){
            $.device.osVersion = '0.0';
        }
        
        if (!window.App && !$.device.android && $.compareVersion($.device.osVersion, '9')  > 0) {
            var geolocation = new qq.maps.Geolocation("{{ Config::get('app.qq_map.key') }}", "myapp");
            geolocation.getLocation(function(result){
                gpsLatLng = new qq.maps.LatLng(result.lat, result.lng);
                qqcGeocoder.getAddress(gpsLatLng);
            }, function (error){
                citylocation.searchLocalCity();
            });
            return;
        }

        var translatePoint = function (position){
            if (isTranslatePoint) {
                return;
            }
            isTranslatePoint = true;
            var currentLat = position.coords.latitude;
            var currentLon = position.coords.longitude;
            qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function (res) {
                latlng = res[0];
                gpsLatLng = latlng;
                qqcGeocoder.getAddress(gpsLatLng);
            });
        }

        var citylocation = new qq.maps.CityService({
            complete: function (result) {
                if (isTranslatePoint) {
                    return;
                }
                isTranslatePoint = true;

                gpsLatLng = result.detail.latLng;
                qqcGeocoder.getAddress(result.detail.latLng);
            }
        });

        if (navigator.geolocation) {
            if (window.App) {
                geolocation = new qq.maps.Geolocation("{{ Config::get('app.qq_map.key') }}", "myapp");
                geolocation.getLocation(function(result){
                    gpsLatLng = new qq.maps.LatLng(result.lat, result.lng);
                    qqcGeocoder.getAddress(gpsLatLng);
                }, function (error){
                    citylocation.searchLocalCity();
                });
                return;
            }

            navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                citylocation.searchLocalCity();
            }, {
                enableHighAccuracy: true,
                maximumAge: 10,
                timeout: 3000
            });
        } else {
            citylocation.searchLocalCity();
        }
    }
</script>
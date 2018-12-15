@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title f16">APP下载</h1>
    </header>
@stop

@section('content')
    <div class="content">
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            var type = "{{$args['type']}}";
           if(type == 1){
               var text = '<p class="f14 tl">名称: staff.ipa</p><p class="f14 tl">大小: {{$appsize}}</p>';
           }else{
               var text = '<p class="f14 tl">名称: androidstaffapp.apk</p><p class="f14 tl">大小: {{$appsize}}</p>';
           }
            $.modal({
                title:  '下载提示',
                text:text,

                buttons: [
                    {
                        text: '返回',
                        onClick: function() {
                            var app = "{{$args['app']}}";
                            if(app == 1){
                                window.location.href = "{{$appId}}://o2o.app/openwith?act=back&url=xxxx";
                            }else{
                                $.href('{{u('Seller/settled')}}');
                            }
                          //  window.location.href = "{{$appId}}://o2o.app/openwith?act=back&url=xxxx";
                        }
                    },
                    {
                        text: '确定',
                        onClick: function() {
                            if(type == 1){
                                window.location.href = "{!! $config['staff_app_down_url'] !!}";
                            }else{
                                window.location.href = "{!! $config['staff_android_app_down_url'] !!}";
                            }
                        }
                    }
                ]
            })
        })
    </script>
@stop

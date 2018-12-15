<!DOCTYPE html>
<!--[if IE 6]><html lang="zh-CN" class="ie6 ie9- ie8-"><![endif]-->
<!--[if IE 7]><html lang="zh-CN" class="ie7 ie9- ie8-"><![endif]-->
<!--[if IE 8]><html lang="zh-CN" class="ie8 ie9-"><![endif]-->
<!--[if IE 9]><html lang="zh-CN" class="ie9"><![endif]-->
<!--[if (gt IE 8)|!(IE)]><!-->
<html lang="zh-CN">
<!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <title>{{ $site_config['site_name'] }}商家管理平台</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}?{{ TPL_VERSION }}">
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome-ie7.css') }}"><![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('static/kindeditor/themes/default/default.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/hover.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/jqueryui/ui.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/zydialog.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/qtip/jquery.qtip.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/uniform/css/uniform.default.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/jqueryui/datepicker.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/yzht.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sm-ht.css') }}?{{ TPL_VERSION }}">

    <script src="{{ asset('js/html5.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/jquery.1.9.1.js') }}?{{ TPL_VERSION }}"></script>   
    <script src="{{ asset('js/yz.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/htbase.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/zydialog.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/jquery.bgiframe.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/json.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/datalist.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/dot.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/jqueryui/ui.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/jqueryui/datepicker.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/uniform/jquery.uniform.min.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/qtip/jquery.qtip.min.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/kindeditor/kindeditor-min.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/kindeditor/lang/zh_CN.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/highcharts/js/highcharts.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('static/highcharts/js/modules/exporting.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('js/sm-ht.js') }}?{{ TPL_VERSION }}"></script>  
    <script>
        //当前控件器
        var CURR_CONTROLLER = "{{ CONTROLLER_NAME }}";
        //当前操作
        var CURR_ACTION     = "{{ ACTION_NAME }}";
        //网站链接
        var SITE_URL        = "{{ url('/') }}";
        //图片域名
        var IMAGE_URL       = "{{ Config::get('app.image_url') }}";
    </script>
    @yield('css')
</head>
<body class="p5">
<?php $controller = CONTROLLER_NAME == 'index' ? 'Index' : CONTROLLER_NAME; $$controller = "on1"; ?>
    <div class="all">
        <!-- state -->
        <nav class="g-pzhd">
            <div class="w1000 ma clearfix">
                <div class="m-lfct fl">
                    <a href="{{ u('Index/index') }}" class="s-logo"><img src="{{ $site_config['admin_logo'] }}"  alt=""></a>
                </div>
                <ul class="u-nav fl">
                    @foreach ($seller_auth as $key => $top_nav)
                        <li @if(isset($top_nav['selected']) && !empty($key))class="on"@endif><a href="{{ url($top_nav['url']) }}"><i class="fa fa-{{ $top_nav['icon'] }}"></i>{{ $top_nav['name'] }}</a></li>
                    @endforeach
                </ul>
                <div class="fr f16 m-zhsztt">
                    <a href="javascript:;">
                        <!-- @if(OPERATION_VERSION != 'personal' && $seller['type'] > 1) -->
                            {{ trim( str_limit($seller['name'], $limit = 6, $end = '...') ) }}
                        <!-- @else -->
                            {{ trim( str_limit($seller['name'], $limit = 10, $end = '...') ) }}
                        <!-- @endif -->
                        <i class="fa fa-sort-desc ml5"></i>
                    </a>
                    <div class="u-fwxlct none">
                        <i class="dwsj"></i>
                        @if($login_seller['type'] == 3)
                        <a href="{{ u('SystemConfig/index') }}">账户设置</a>
                        @else
                        <a href="{{ u('Seller/index') }}">账户设置</a>
                        @endif
                        <a href="{{ u('Public/logout') }}">安全退出</a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- 内容 -->
        <div class="w1000 ma mt10 g-bsbgct">
            <div style="width:600px;height:600px;margin:100px auto;">@yield('content')</div>
        <div class="cl"></div>
        </div>
    </div>
</body>

@yield('js')
<script type="text/tpl" id="imageListItemTpl">
<li class="image-box">
    <a class="m-tpyllst-img image-item" href="@{{=it.image}}" target="_blank"><img src="@{{=it.image}}" alt=""></a>
    <a class="m-tpyllst-btn image-update-btn" href="javascript:;">
        <i class="fa fa-plus"></i> 选择图片
    </a>
    <a href="javascript:;" class="image-delete fa fa-times"></a>
    <input type="hidden" name="@{{=it.inputName}}" value="@{{=it.image}}"/>
</li>
</script>
<script type="text/javascript">
    function heightadjust(){
    var b=Math.max($(".u-lfslider").height(),$(window).height(),$(".u-frct").height());
    $(".u-lfslider").height(b);
};
jQuery(function($){
    setTimeout('heightadjust()',1500);
    $(".date").datepicker();
    $(".datetime").datetimepicker({
        controlType:"select"
    });
    $(".dateyear").datepicker({
        changeYear:true,
        changeMonth:true,
        defaultDate:"-25y"
    });

    $("input[type='checkbox'],input[type='radio']").uniform();

    $('*[title]').tooltip();

    $(".image-list").sortable({items:'li.image-box',cancel:'.image-add-box'});

    KindEditor.ready(function(K){
        var imgeditor = K.editor({
            themeType:"simple",
            allowFileManager:false
        });
        $(document).on('click', '.img-update-btn', function(e){
            imgeditor.loadPlugin('yzimage', function () {
                imgeditor.plugin.imageDialog({
                    clickFn: function (url, title, width, height, border, align) {
                        var rel = $(e.target).data('rel');
                        $('#'+rel).val(url);
                        $('#img-preview-'+rel).attr('href',url).attr('target','_blank');
                        $('#img-preview-'+rel+' img').attr('src',url).show();
                        imgeditor.hideDialog();
                        var callback = $(e.target).data('callback');
                        if(typeof callback != 'undefined'){
                            callback = callback + '(e.target,url);';
                            eval(callback);
                        }
                    }
                });
            });
        }).on('click', '.image-list .image-add-btn', function(e){
            imgeditor.loadPlugin('yzimage', function () {
                imgeditor.plugin.imageDialog({
                    clickFn: function (url, title, width, height, border, align) {
                        var html = $("#imageListItemTpl").html();
                        var obj = new Object();
                        obj.inputName = $(e.target).parents('.image-list').data('input-name');
                        obj.image = url;
                        $(e.target).parent().before($.Template(html, obj));
                        imgeditor.hideDialog();
                    }
                });
            });
        }).on('click', '.image-list .image-update-btn', function(e){
            imgeditor.loadPlugin('yzimage', function () {
                imgeditor.plugin.imageDialog({
                    clickFn: function (url, title, width, height, border, align) {
                        var item = $(e.target).parent();
                        item.find('.image-item').attr('href',url);
                        item.find('.image-item img').attr('src',url);
                        item.find('input').val(url);
                        imgeditor.hideDialog();
                    }
                });
            });
        }).on('click', '.image-list .image-delete', function(e){
            $(this).parent().remove();
        })

        var mediaeditor = K.editor({
            themeType:"simple",
            allowFileManager:true
        });
        K('.media-update-btn').click(function (e) {
            mediaeditor.loadPlugin('zymedia', function () {
                mediaeditor.plugin.mediaDialog({
                    clickFn: function (url, title, width, height, border, align) {
                        var rel = $(e.target).data('rel');
                        $('#'+rel).val(url);
                        mediaeditor.hideDialog();
                    }
                });
            });
        });
    });

    $(document).on('click','.disabled',function(event){
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        return false;
    });

    $(".ajax-from").submit(function(){
        return false;
    })

    $(".validate-form").validate({
        onfocusout:false,
        onkeyup:false,
        onclick:false,
        focusInvalid:false,
        showErrors: function(errorMap, errorList) {
            $('.error-tip').qtip('destroy', true); 
            $('.error-tip').removeClass('error-tip');
            if (errorList.length > 0) {
                var obj = $(errorList[0].element);
                $.tip(obj, errorList[0].message);
            }
        }
    });

    $(".ajax-form").submit(function(){
        var form = this;
        if ($(this).hasClass('sumit-loading')) {
            return false;
        }
        $('.error-tip').qtip('destroy', true);
        KindEditor.sync('.yzeditor');
        for(var i = 0; i < YZ.AJAX_FROM_SYNC.length; i++) {
            YZ.AJAX_FROM_SYNC[i].call(this);
        }
        $(this).addClass('sumit-loading');
        $.post(this.action, $(this).serialize(), function(result){
            $(form).removeClass('sumit-loading');
            if(result.status){
                if(typeof(YZ.AJAX_FROM_CALLBACK) === "undefined"){
                    $.zydialogs.open('<p style="padding:30px;">'+result.msg+'</p>', {
                        boxid:'AJAX_FROM_WEEBOX',
                        width:300,
                        title:'操作提示',
                        timeout:2,
                        onClose:function(){
                            if(result.url){
                                result.url += result.url.indexOf('?') == -1 ? '?' : '&';
                                location.href = result.url + "t=" + (new Date()).getTime();
                            }else{
                                location.reload(true);
                            }
                        }
                    });
                } else {
                    YZ.AJAX_FROM_CALLBACK.call(this, form, result);
                }
            } else {
                var istip = true;
                if(result.field){
                    var field = $("*[name='" + result.field + "']", form);
                    if(field.length > 0){
                        if(field.data('tip-rel')){
                            field = $(field.data('tip-rel'));
                        }
                        var tabPane = field.parents('.tab-pane');
                        if(!tabPane.hasClass('tab-pane-active')){
                            var rel = tabPane.attr('rel');
                            $(".tab-nav li").removeClass("tab-pane-active");
                            $(".tab-nav li[rel='"+rel+"']").addClass("tab-pane-active");
                            $(".tab-pane",form).removeClass("tab-pane-active");
                            $(".tab-pane[rel='"+rel+"']",form).addClass("tab-pane-active");
                        }
                        $.tip(field,result.msg);
                        istip = false;
                    }
                } 
                if (istip) {
                    $.ShowAlert(result.msg);
                }
            }
        },'json');
        return false;
    })
})
</script>
</html>
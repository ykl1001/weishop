@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>

    @if($data['status'] == 1)
        <nav class="bar bar-tab">
            <a id="submit" class="button button-light fr w_percentage_25 f14 f_999 mr10">维修完成</a>
        </nav>
    @endif
@stop

@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop

@section('content')
    @include('staff.default._layouts.refresh')
    <div class="content-block-title f12 m10">报修信息</div>
    <div class="delivery-content y-wxxqbox m10 pl10 pr10">
        <div class="flex delivery-name-phone">
            <div class="flex-2 br-1 lh30">
                <span>{{$data['puser']['name']}}</span>
                <span class="ml0575 f-light-color">{{$data['puser']['mobile']}}</span>
            </div>
            <span class="flex-1 text-align-center"><a onclick="javascript:tel('{{$data['puser']['mobile']}}')"><i class="iconfont f20">&#xe64e;</i></a></span>
        </div>
        <div class="delivery-location fine-bor-top"><h3>{{$data['build']['name']}} {{$data['room']['roomNum']}}</h3></div>
        <div class="delivery-location fine-bor-top">报修时间：{{$data['createTime']}}</div>
    </div>

    @if($data['status'] == 2 && $data['isRate'] == 1)
        <div class="content-block-title f12 m10">维修评价</div>
        <div class="y-xwxdbox">
            <div class="f_l f12">评分:</div>
            <div class="y-wxxqscorebox">
                <div class="y-scorestar">
                    <i class="icon iconfont">&#xe644;</i>
                    <i class="icon iconfont">&#xe644;</i>
                    <i class="icon iconfont">&#xe644;</i>
                    <i class="icon iconfont">&#xe644;</i>
                    <i class="icon iconfont">&#xe644;</i>
                    <div class="y-scorestar2" style="width: {{($data['rate']['star']/5)*100}}%;">
                        <i class="icon iconfont f_red">&#xe645;</i>
                        <i class="icon iconfont f_red">&#xe645;</i>
                        <i class="icon iconfont f_red">&#xe645;</i>
                        <i class="icon iconfont f_red">&#xe645;</i>
                        <i class="icon iconfont f_red">&#xe645;</i>
                    </div>
                </div>
                <div class="f12 y-ell3">{{$data['rate']['content']}}</div>
            </div>
        </div>
    @endif

    <div class="content-block-title f12 m10">故障详情</div>
    <div class="y-xwxdbox">
        <p class="bold f14 y-ell">可维修时间：{{$data['apiTime']}}</p>
        <p class="f12 mt5">故障类型：{{$data['repairType']}}</p>
        <p class="f12 mt5">故障详情：{{$data['content']}}</p>
        <p class="f12 mt5 mb10">故障图片</p>
        <p>
            @if($data['images'])
                @foreach($data['images'] as $item)
                    <img src="{{$item}}" class="w100">
                @endforeach
            @endif
        </p>
    </div>
@stop

@section('show_nav')@stop

@section($js)
    <script>
        function tel(mobile){
            window.location.href = "tel:"+mobile;
        }
        var id = "{{ Input::get('id') }}";

        $(document).off("touchend","#submit");
        var is_post = 0;
        $(document).on("touchend","#submit",function(){
            if(is_post == 1){
                return false;
            }
            var data = {
                id: id,
                status: 2
            };
            $.post("{{ u('Repair/complte') }}", data, function(res){
                if(res.code == 0) {
                    $.alert(res.msg, function(){
                        $.href("{!! u('Repair/detail',['id'=>Input::get('id')]) !!}");
                    });
                }else{
                    is_post = 0;
                    $.alert(res.msg);
                }
            },"json");
            return false;
        })

    </script>
@stop
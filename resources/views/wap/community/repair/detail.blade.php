@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="javascript:$.href('{{u('Repair/index',['districtId'=>$args['districtId']])}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">报修记录</h1>
    </header>

    @if($data['status'] == 2 && $data['isRate'] == 0)
        <nav class="bar bar-tab y-ddxqbtnh">
            <div class="y-ddxqbtn2">
                <a onclick="$.href('{{u('Repair/rate',['id'=>$data['id'],'districtId'=>$args['districtId']])}}')" class="ui-btn fr">去评价</a>
            </div>
        </nav>
    @endif
@stop

@section('content')
    <div class="content mb20 pb10" id=''>
        <div class="c-bgfff p10">
            <span class="f12 c-gray2">{{ $data['createTime'] }}</span>
            <span class="f12 c-red fr">{{ $data['statusStr'] }}</span>
        </div>
        @if($data['status'] == 2 && $data['isRate'] == 1)
            <div class="content-block-title f12 m10 clearfix">
                <span>维修评价</span>
                <div class="y-scorestar fr">
                    <i class="icon iconfont vat">&#xe678;</i>
                    <i class="icon iconfont vat">&#xe678;</i>
                    <i class="icon iconfont vat">&#xe678;</i>
                    <i class="icon iconfont vat">&#xe678;</i>
                    <i class="icon iconfont vat">&#xe678;</i>
                    <div class="y-scorestar2" style="width: {{($data['rate']['star']/5)*100}}%;">
                        <i class="icon iconfont c-red vat">&#xe677;</i>
                        <i class="icon iconfont c-red vat">&#xe677;</i>
                        <i class="icon iconfont c-red vat">&#xe677;</i>
                        <i class="icon iconfont c-red vat">&#xe677;</i>
                        <i class="icon iconfont c-red vat">&#xe677;</i>
                    </div>
                </div>
            </div>
            <div class="y-bxxqbox">
                <p class="f12 mt10 mb10 y-ell3">{{$data['rate']['content']}}</p>
                <p class="f12 c-gray2">{{Time::toDate($data['rate']['createTime'])}}</p>
            </div>
        @endif
        <div class="content-block-title f12 m10">维修信息</div>
        <div class="y-bxxqbox">
            @if($data['status'] != 0)
                <p class="f12 mt10">维修人员：{{$data['staff']['name']}}<span class="ml10">{{$data['staff']['mobile']}}</span><a href="tel:{{$data['staff']['mobile']}}" class="icon iconfont c-red fr pl10 vat f20">&#xe609;</a></p>
            @endif
            <p class="f12 mt10">故障类型：{{$data['repairType']}}</p>
            <p class="f12 mt10 mb10">故障描述：{{$data['content']}}</p>
            <p>
                @if($data['images'])
                    @foreach($data['images'] as $item)
                        <img src="{{$item}}" class="w100">
                    @endforeach
                @endif
            </p>
        </div>
      </div>
    </div>
@stop

@section($js)
@stop
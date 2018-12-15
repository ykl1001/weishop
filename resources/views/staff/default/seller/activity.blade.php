@extends('staff.default._layouts.base')
@section('css')
<style type="text/css">
    .actions-modal-button.actions-modal-button-bold{font-size: 16px;color: #313233;}
</style>
@stop
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/index') }}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a class="button button-link button-nav pull-right y-yxzxnew">
            新增
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom @stop
@section('show_nav')@stop
@section('content')
    <div class="list-block media-list y-ulbnobor y-sylist">
        <ul>
            @foreach($list as $key => $value)
                <li class="mb10">
                    <a href="#" class="item-link item-content">
                        <div class="item-media">
                            @if($value['type'] == 4)
                                <img src="{{ asset('seller/images/xin.png') }}" width="28">
                            @elseif($value['type'] == 5)
                                <img src="{{ asset('seller/images/jian.png') }}" width="28">
                            @elseif($value['type'] == 6)
                                <img src="{{ asset('seller/images/tei.png') }}" width="28">
                            @endif
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row mb5">
                                <div class="item-title f_333 f15">@if($value['type'] == 6){{$value['sale']}}折@endif{{$value['name']}}</div>
                                <div class="item-after f_333 f13">
                                    @if($value['timeStatus'] == 1)
                                        <span style="color:green">进行中</span>
                                    @elseif($value['timeStatus'] == 0)
                                        <span style="color:red">未开始</span>
                                    @elseif($value['timeStatus'] == -1)
                                        <span style="color:gray">已过期</span>
                                    @endif
                                </div>
                            </div>
                            <div class="item-subtitle f12 f_999">{{ Time::toDate($value['startTime'], 'Y/m/d') }}-{{ Time::toDate($value['endTime'], 'Y/m/d') }}</div>
                        </div>
                    </a>
                    <div class="y-yxzxbtn f12">
                        <a href="#" onclick="JumpURL('{{ u('seller/activityInfo', ['id'=> $value['id']]) }}','#seller_activityInfo_view',2)">查看详情</a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@stop
@section($js)
<script type="text/javascript">
$(document).off('click','.y-yxzxnew');
$(document).on('click','.y-yxzxnew', function () {
    var buttons1 = [
    {
      text: "<a href='#' data-url=\"{{ u('seller/activityAddFull') }}\" data-id=\"seller_activityAddFull_view\">满减活动</a>",
      bold: true
    },
    {
      text: "<a href='#' data-url=\"{{ u('seller/activityAddSpecial') }}\" data-id=\"seller_activityAddSpecial_view\">特价商品</a>",
      bold: true
    }
    ];
    var buttons2 = [
    {
      text: '取消',
      bg: 'danger'
    }
    ];
    var groups = [buttons1, buttons2];
    $.actions(groups);
});

$(document).off('click','.actions-modal-button-bold');
$(document).on('click','.actions-modal-button-bold', function () {
    var url = $(this).find('a').attr('data-url');
    var id = $(this).find('a').attr('data-id');
    JumpURL(url, '#'+id, 2);
});

</script>
@stop
@section('preloader')
@stop
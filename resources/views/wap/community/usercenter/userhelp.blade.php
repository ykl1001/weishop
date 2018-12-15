@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
		<a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else{{u('UserCenter/index')}}@endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$title}}</h1>
    </header>
@stop
@section('content')
    @if(!$isFx)
        @include('wap.community._layouts.bottom')
        <div class="content c-bgfff" id=''>
            <div class="y-about f14">
                <p>{!! $userhelp !!}</p>
            </div>
        </div>
    @elseif($isFx == 1)
        <style>
            .x-probox {
                width: 100%;
                position: absolute;
                left: 0;
                bottom: 0;
                padding-bottom: 0px;
            }
            .y-about {
               padding: 0 !important;
            }
            .y-about {
                background-color: #fff;
            }
			img{				
                width: 100%;
			}
        </style>
        <div class="content" id='' style="margin-bottom: 45px">
           <div>
               <div class="c-bgfff">
                   <div class="list-block mt10 ml20  pt10 pb10">
                       开通分销费用：<span class="x-noticeico">{{$userc['protocolFee'] or 'protocolFee'}}元</span>
                   </div>
               </div>
               <div class="y-about f14 pb10 tl">
                   <div style=" padding: .5rem .5rem 0;">
                       <p>{!! $userc['privilegeDetails'] !!}</p>
                   </div>
                   <div style="background-color:#f2f2f2" class="tc pt10 mb10">
                       点击开通,即表示您已阅读并同意<a style="color:#00BEC6" href="{{u('UserCenter/userhelp',['isFx' => 2])}}">《分销资质购买协议》</a>
                   </div>
               </div>
           </div>
        </div>
        <div class="x-probox">
            <div class="x-pbtn c-white" id="cz_btn"><button class="join f16 c-bg w100  pt10 pb10" onclick="$.href('{{u('UserCenter/recharge',['isFx'=>1])}}')">开通</button></div>
        </div>
    @elseif($isFx == 2)
        <div class="content c-bgfff" id=''>
            <div class="y-about f14">
                <p>{!! $userc['purchaseAgreement'] !!}</p>
            </div>
        </div>
    @endif
@stop

@section($js)
    <script type="text/javascript">
        BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";
    </script>
@stop


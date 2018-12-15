@extends('wap.community._layouts.base_menu')
@section('content')
    <div id="page-swiper" class="page">
        <div class="content" id=''>
            <div class="swiper-container y-swiper" data-space-between='0'  style="background:#fff;">
                <div class="swiper-wrapper">
                    @for($i = 0; $i < (ceil(count($menu) / 8)); $i++)
                        <div class="swiper-slide">
                            <ul class="y-nav clearfix">
                                @foreach(array_slice($menu,($i * 8),8) as  $v)
                                    <?php 
                                        if (!preg_match("/^(http|https):/", $v['url'])){
                                            $v['url'] = 'http://'.$v['url'];
                                        } 
                                    ?>
                                    <li><a href="{{ $v['url'] }}" class="db" external><img src="{{ $v['menuIcon'] }}"><p class="f13">{{ $v['name'] }}</p></a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endfor
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
@stop
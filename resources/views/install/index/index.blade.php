﻿@extends('install._layouts.base')
@section('right_content')
    <div class="x-shouc">
        <div class="f_l w600">
            <p class="x-shouc1 mb15">中文版授权协议 适用于中文用户</p>
            <p>感谢您选择方维科技产品。希望我们的努力能为您提供一个高效快速、强大的站点解决方案，和强大的社区解决
                方案。方维科技网址为 http://www.fanwe.com，产品官方讨论区网址为 http://www.fanwe.com</p>
            <p>用户须知：本协议是您与方维科技公司之间关于您使用方维科技公司提供的各种软件产品及服务的法律协议。无论您人或
                组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制方维科技责任的免责
                条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及/或方维随时其的修改，您
                应不使用或主动取消方维科技公司提供的方维产品。否则，您的任何对方维产品中的相关服务的注册、登陆、下载、查看
                等使用行为将被视为您对本服务条款全部的完全接受，包括接受方维对服务条款随时所做的任何修改。</p>
            <p>本服务条款一旦发生变更, 方维将在网页上公布修改内容。修改后的服务条款一旦在网站管理后台上公布即有替
                原来的服务条款。您可随时登陆方维官方论坛查阅最新版服务条款。如果您选择接受本条款，即表示您同意接则不能
                获得使用本服务的权利。您若有违反本条款规则不能获得使用本服务的权利。您若有违反本条款规则不能获得使用本</p>
            <h3>方维科技公司简介</h3>
            <p>福建方维信息科技有限公司创立于2008年，是一家以互联网领域创新型软件产品研发及配套服务为主，标准化管理、快速发展的高新技术企业。公司下设重庆分公司，拥有员工近百人，拥有一批互联网领域10余年从业经验的专业团队。</p>
            <p>方维为用户提供专业的互联网创新模式产品的商业咨询策划、设计开发及投融资居间服务，让用户享受到全方位的互联网创新产品及服务解决方案，是国内领先的互联网应用产品及解决方案提供商。</p>
            <p>我们追求卓越的产品品质以及完美的用户体验，提倡"以顾客体验为根本导向"。将目标客户群体的用户体验科学的通过人机交互自然完美、友好的呈现出来。旨在为客户创作具有革新性用户体验、简单方便、安全的、实用的产品及解决方案。</p>
        </div>
        <div class="f_l ">
            <h3>敏锐的行业前瞻性</h3>
            <p>方维设立战略研究室，与互联网各领域内从业者与专家保持密切沟通，形成方维独特的市场敏锐和前瞻性<br>先于全国市场，首家推出“方维众筹”、“方维P2P”、“方维O2O”等创新产品，并取得骄人的市场业绩！</p>
            <h3>雄厚的自主产品研发实力</h3>
            <p>有一支从业软件开发10余年、反应迅速、讲求团队合作的专业技术团队，奠定开发实力的高起点<br>多年互联网行业经验，拥有20余款互联网与移动互联网相关产品的自主知识产权！</p>
            <h3>全方位的产业链服务</h3>
            <p>战略先行：在企业创立或转型前期，梳理商业模式、规划技术平台、进行落地执行指导<br>技术支撑：根据战略中规划的技术平台，依托我们的研发实力，开发切合您需求与发展的软件产品<br>资本引入：协助您进行融资，实现企业快速发展、建立行业壁垒，让您走在行业竞争者前列！</p>
            <h3>强大的专业团队  </h3>
            <p>拥有一支由软件技术开发、商业营销策划、投融资等各行业资深专家及从业人员组成的专业团队<br>我们洞察市场、深谙技术、引入资本运作，旨在为不同需求、不同发展阶段的客户解决最实际的问题！</p>
        </div>
    </div>
    <p class="mt20 tc">
        <a href="javascript:;" class="btn btn2 mr15">不同意</a>
        <a href="javascript:;" class="btn btn3">同意</a>
    </p>
@stop
@section('js')
    <script>
        jQuery(function($){
            $(".btn3").click(function(){
                $.post("{{u('Index/bengin')}}", {agent:'m'}, function(result){
                    if(result.status){
                        location.href = "{{ u('index/check') }}";
                    }
                },'json');
            });
            $(".btn2").click(function(){
                $.ShowAlert("不同意，将会无法安装程序");
            });
        });
    </script>
@stop
/**
 * Created by Administrator on 2015/7/3.
 */
$(function(){
    var headerH = $(".d-header").height();
    var imgH = $('.d-showbg .show').height();
    var loadH = $('.d-showbg .loading').height();
    var loadW = $('.d-showbg .loading').width();
    //��Ƭ��λ
    $("#showbg .show").css({
        position:'absolute',
        top: ($(window).height() - headerH - imgH)/2
    });
    $("#showbg .loading").css({
        position:'absolute',
        top: ($(window).height() - headerH - loadH)/2,
        left: ($(window).width() - loadW)/2
    });
    $(".d-showbg .d-focus").css({
        position:'absolute',
        top: ($(window).height() - headerH - imgH)/2
    });
    //ɨ���λ
    $(".d-smbox").css({
        position:'absolute',
        left: ($(window).width() - $('.d-smbox').outerWidth())/2,
        top: ($(window).height() - headerH - $('.d-smbox').outerHeight())/2
    });
    //ɨ��ɹ���ʾ��λ
    $(".d-smtips").css({
        position:'absolute',
        left: ($('.d-smbox').outerWidth() - $('.d-smtips').outerWidth())/2,
        top: ($('.d-smbox').outerHeight() - $('.d-smtips').outerHeight())/2
    });
    // ���������ʾ
    $("#m-tkny").css({
        position:'absolute',
        left: ($(window).width() - $('#m-tkny').outerWidth())/2,
        top: ($(window).height() - $('#m-tkny').outerHeight())/2 + $(document).scrollTop()
    });
    $("#m-tkny1").css({
        position:'absolute',
        left: ($(window).width() - $('#m-tkny1').outerWidth())/2,
        top: ($(window).height() - $('#m-tkny1').outerHeight())/2 + $(document).scrollTop()
    });
    $("#m-tkny2").css({
        position:'absolute',
        left: ($(window).width() - $('#m-tkny2').outerWidth())/2,
        top: ($(window).height() - $('#m-tkny2').outerHeight())/2 + $(document).scrollTop()
    });
    $("#m-tkny3").css({
        position:'absolute',
        left: ($(window).width() - $('#m-tkny3').outerWidth())/2,
        top: ($(window).height() - $('#m-tkny3').outerHeight())/2 + $(document).scrollTop()
    });

    $(document).on("touchend",".d-sure",function(){
        $(this).parents(".m-tkbg").hide();
    });

    //ѡ������б���
    $(document).on("touchend",".d-leavelist .unchoose",function(){
        $(this).hide().siblings(".choose").show();
    })
    $(document).on("touchend",".d-leavelist .choose",function(){
        $(this).hide().siblings(".unchoose").show();
    })
    //ȫѡ�б���
    $(document).on("touchend",".d-header .allchoose",function(){
        $(".d-leavelist").find(".choose").show();
        $(".d-leavelist").find(".unchoose").hide();
        $(".d-operate").slideDown();
    })
    $(document).on("touchend",".d-header .unchoose",function(){
        $(".d-leavelist").find(".choose").show();
        $(".d-leavelist").find(".unchoose").hide();
        $(".d-operate").slideDown();
    })
    $(document).on("touchend",".d-header .choose",function(){
        $(".d-leavelist").find(".choose").hide();
        $(".d-leavelist").find(".unchoose").show();
        $(".d-operate").slideDown();
    })
    // �޸�ͷ��
    $(".x-wdtxi").touchend(function(){
        $(".photo_frame").fadeIn();
    });
    //�������һ����Ԫ�������ױ߿�
    $(".d-pjlist li").last().css("border","none");
    //�����ұ����ÿ��
    $(".d-ztpj").css("width", $(".d-wdpj").width() - 70);
    $(".pjwenzi").css("width", $(".d-wdpj").width() - 70);
    //��������ɾ����¼
    //$(".d-qjjl").on("swipeleft",function(){
    //    $(this).find("a").addClass("on");
    //    $(this).find("a").animate({right:'0'});
    //    $(this).find(".scroll").animate({right:'90px'});
    //    event.stopPropagation();
    //});
    //$(".d-qjjl").on("swiperight",function(){
    //    if($(this).find("a").hasClass("on")){
    //        $(this).find("a").removeClass("on");
    //        $(this).find("a").animate({right:'-90px'});
    //        $(this).find(".scroll").animate({right:'0'});
    //    }
    //    event.stopPropagation();
    //});
    //��ȡɾ����¼��ť�ĸ���߶ȷ��ظ���ť�߶Ȳ������и�
    for(var i=0;i<$(".d-qjjl").length;i++){
        $(".d-qjjl").eq(i).find(".d-delete").css("line-height",$(".d-qjjl").eq(i).height()+30+"px");
    }
    //�ճ̷�ҳ
    $(".d-fenyebar .d-fenyelist").css({
        position:'relative',
        left: ($('.d-fenyebar').width()-320)/2
    });

    var a = $(".d-fenyelist ul li").index($(".on"));
    $(".d-fenyelist ul").css({
        position:'relative',
        left: -(a * 54),
        width: ($(".d-fenyelist ul li").length) * 54
    });
    $(document).on("touchend",".d-fenyebar .next",function(){
        var left = $(".d-fenyelist ul").position().left + $(".d-fenyelist ul").width();
        if(left !=270){
            $(".d-fenyelist ul").animate({left: $(".d-fenyelist ul").position().left - 54});
        }
    });
    $(document).on("touchend",".d-fenyebar .prev",function(){
        if($(".d-fenyelist ul").position().left !=0) {
            $(".d-fenyelist ul").animate({left: $(".d-fenyelist ul").position().left + 54});
        }
    });
    //����޸�����ʱ��������ʧ
    $(".x-wdzhr").is(":focus").text("");
});
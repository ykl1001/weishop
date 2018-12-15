<style>
    /* 可自行设计实现captcha的位置大小 */
    #popup-mobile {position: relative;margin: 50px 0;text-align: center;}
    #popup-mobile .inp {border: 1px solid gray;padding: 0 10px;width: 200px;height: 30px;font-size: 18px;}
    #popup-mobile .btn {border: 1px solid gray;width: 100px;height: 30px;font-size: 18px;cursor: pointer;}
    #popup-mobile #embed-captcha {width: 300px;margin: 0 auto;}
    #popup-mobile .show {display: block;}
    #popup-mobile .hide {display: none;}
    #popup-mobile #notice {color: red;}
    /* 以下遮罩层为demo.用户可自行设计实现 */
    #popup-mobile #mask {display: none;position: fixed;text-align: center;left: 0;top: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;z-index: 100}
    #popup-mobile #popup-captcha-mobile {position: fixed;display: none;left: 50%;top: 50%;/*transform: translate(-50%, -50%);-webkit-transform: translate(-50%, -50%);*/z-index: 9999;margin: -125px 0 0 -130px;}
</style>
<script src="{{ asset('wap/js/gt.js') }}"></script>

<div id="popup-mobile">
    <!-- 1.引入该文件 -->
    <!-- 2.#getmask调用 -->
    <!-- 3.fun() 验证成功回调函数 -->
    <!-- <input class="btn" id="getmask" type="submit" data-backfunction="fun()" value="获取验证码"> -->
    
    <div id="mask"></div>
    <div id="popup-captcha-mobile"></div>
</div>

<script>
    $("#mask").click(function () {
        $("#mask, #popup-captcha-mobile").hide();
    });
    $("#getmask").click(function () {
		mobile = $("#cellphone").val();
        if(mobile != ""){
            var reg = /^1[\d+]{10}$/;
            if(!reg.test(mobile)){
                $.toast('请输入正确的手机号码'); 
                return false;
            }
        }else{
            $.toast("手机号码不能为空");
            return false;
        }
        $("#mask, #popup-captcha-mobile").show();
    });
    var handlerPopupMobile = function (captchaObj) {
        // 将验证码加到id为captcha的元素里
        captchaObj.appendTo("#popup-captcha-mobile");
        //拖动验证成功后两秒(可自行设置时间)自动发生跳转等行为
        captchaObj.onSuccess(function () {
            var validate = captchaObj.getValidate();
            $.ajax({
                url: "{{ u('GtVerifyLoginServlet/second') }}", // 进行二次验证
                type: "post",
                dataType: "json",
                data: {
                    // 二次验证所需的三个值
                    type: "mobile",
                    geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode
                },
                success: function (data) {
                    if (data && (data.status === "success")) {
                        $("#mask, #popup-captcha-mobile").hide();
                        $("#getmask").addClass("last-time");
                        eval($("#getmask").data('backfunction'));
                    } else {
                        $.alert("验证失败");
                    }
                }
            });
        });
    };

    $.get("{{ u('GtStartCaptchaServlet/once') }}", {'type':'mobile', 't':(new Date()).getTime()}, function(data){
        initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                offline: !data.success
            }, handlerPopupMobile);
    });

</script>
<script>
    window.alert = function(msg,type)
    {
    var alertFram = document.createElement("alert");
        alertFram.id="alertFram";
        alertFram.style.position = "absolute";
        alertFram.style.left = "50%";
        alertFram.style.top = "30%";
        alertFram.style.marginLeft = "-349px"; 
        alertFram.style.width = "698px";
        alertFram.style.height = "249px";
        alertFram.style.background = "#fff";
        alertFram.style.textAlign = "center";
        alertFram.style.lineHeight = "53px";
        alertFram.style.zIndex = "991212"; 
        strHtml =  '<div class="g-tkbg" style="left:0px">';
        strHtml +=  '<div class="g-serct">';
        strHtml +=     '<p class="f-tt" style="text-align:left">';
        if(type == 1 || type == 2){            
            strHtml +=           '<span class="ml15"> 恭喜！</span>';
        }else{
            strHtml +=           '<span class="ml15"> 错误提示！</span>';
        }
        strHtml +=     '</p>';
        strHtml +=     '<p class="tc mt20 mb20">';
        if(type == 1 || type == 2){             
            strHtml +=         '<img src="{{ asset("images/ico/xlico.png") }}" alt="">';
        }else{
            strHtml +=         '<img src="{{ asset("images/ico/iconfont-ku.png") }}" alt="">';
        }
        strHtml +=     '</p>';
        strHtml +=     '<p class="lh25 f18 tc">';
        strHtml +=     msg;
        strHtml +=     '</p>';
        strHtml +=     ' <p class="mt20 pb20 tc">';
        if(type == 2){            
            strHtml += '<a href="javascript:;" onclick=\"doStype()\" class="btn f-back mb20">设置服务类型</a>';
        }else{
            strHtml += '<a href="javascript:;"  class="doOk btn f-back mb20">确定</a>';
        }
        strHtml +=     '</p>' ;//onclick=\"doOk()\"
        strHtml += "</div>\n";
        strHtml += "</div>\n";
        alertFram.innerHTML = strHtml;
        document.body.appendChild(alertFram); 
        //var ad = setInterval("doAlpha()",5);
        $(document).on('click', '.doOk', function(){
            alertFram.style.display = "none";
        }); 
        alertFram.focus();
        document.body.onselectstart = function(){return false;};
    }
</script>
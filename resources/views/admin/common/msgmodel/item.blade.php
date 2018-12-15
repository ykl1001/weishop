@if($data)
    <fieldset class="fieldset-1">
        <legend class="checked_all">
            <label><span>{{$type[$data['type']]}}</span></label>
        </legend>
        <div class="actions">
            <div class="blank15"></div>
            <fieldset class="my_fieldset fieldset-2">
                <legend  class="checked_module">
                    <label>
                        {{$data['name']}}
                    </label>
                </legend>
                <div class="blank15"></div>
                <div class="actions fieldset-3">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>标题</span>：<input name="title" class="u-ipttext" value="{{$data['title']}}"/>
                    <div class="blank15"></div>
                    <div class="f-boxr">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>内容</span>：<textarea name="content" id="buyer_share_content" class="u-ttarea" style="width: 85%">{{$data['content']}}</textarea>
                    </div>
                    @if($data['tip'])
                        <div class="blank15"></div>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>提示： {{$data['tip']}}</span>
                        <div class="blank15"></div>
                        <div class="red">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>注意： 以上变量请勿随意修改。</span>
                        </div>
                    @endif
                    <div id="" class="u-fitem clearfix " style="margin: 15px 0 0 23px;">
		            <span class="f-tt">
		                 是否发送短信通知：
		            </span>
                        <div class="f-boxr">
                            <label>
                                <div class=""><span><input type="radio" class="uniform " name="is_send_msg" value="1" @if($data['isSendMsg'] == 1) checked @endif /></span><span>是</span></div>

                            </label>
                            <span>&nbsp;&nbsp;</span>
                            <label>
                                <div class=""><span><input type="radio" class="uniform " name="is_send_msg" value="0" @if($data['isSendMsg'] != 1) checked @endif /></span><span>否</span></div>

                            </label>
                            <span>&nbsp;&nbsp;</span>
                        </div>
                    </div>
                    <div class="blank15"></div>
                </div>
                <div class="blank15"></div>
            </fieldset>
            <input type="hidden" name="id" value="{{$data['id']}}">
            <input type="hidden" name="name" value="{{$data['name']}}">
            <input type="hidden" name="code" value="{{$data['code']}}">
            <div class="blank15"></div>
        </div>
    </fieldset>
    <div class="blank15"></div>
@endif
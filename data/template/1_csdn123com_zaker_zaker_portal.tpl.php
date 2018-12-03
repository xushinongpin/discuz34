<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$csdn123com_zaker_return = <<<EOF


<div style="background:#ddd;margin-top:16px;margin-bottom:16px;padding:16px;">
  <input type="hidden" class="px" id="hzwzaker_catid" value="{$_GET['catid']}" />
  <table cellpadding="8" cellspacing="4">
    <tr>
      <td align="right">zaker新闻地址</td>
      <td><input type="text" class="px" style="width: 30em" id="hzwzaker_fromurl" /></td>
    </tr>
<tbody 
EOF;
 if($csdn123_showmore!=1) { 
$csdn123com_zaker_return .= <<<EOF
 style="display:none" 
EOF;
 } 
$csdn123com_zaker_return .= <<<EOF
>
    <tr>
      <td align="right">采集回复数</td>
      <td><input type="text" class="px" id="hzwzaker_replaynum" value="20" /></td>
    </tr>
    <tr>
      <td align="right">请输入发帖人(楼主)的UID</td>
      <td><input type="text" class="px" id="hzwzaker_first_uid" value="{$first_uid}" style="width: 30em" /></td>
    </tr>
    <tr>
      <td align="right">请输入回帖用户的UID</td>
      <td><input type="text" class="px" id="hzwzaker_reply_uid" value="{$reply_uid}" style="width: 30em" /></td>
    </tr>
    <tr>
      <td align="right">是否伪原创（批量替换）</td>
      <td><input name="hzwzaker_weiyanchang" type="radio" class="radio" value="1" id="weiyanchang_yes">
        <label for="weiyanchang_yes">是</label>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input name="hzwzaker_weiyanchang" type="radio" class="radio" value="0" id="weiyanchang_no" checked>
        <label for="weiyanchang_no">否</label></td>
    </tr>
    <tr>
      <td align="right">回帖的总时长(单位秒)</td>
      <td><input type="text" class="px" id="hzwzaker_intval_time" value="3600" /></td>
    </tr>
    <tr>
      <td align="right">中文编码类型</td>
      <td><input name="hzwzaker_simplified" type="radio" class="radio" value="0" id="simplified_original" checked>
        <label for="simplified_original">不转换</label>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input name="hzwzaker_simplified" type="radio" class="radio" value="2" id="simplified_yes">
        <label for="simplified_yes">简体</label>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input name="hzwzaker_simplified" type="radio" class="radio" value="1" id="simplified_no">
        <label for="simplified_no">繁体</label></td>
    </tr>
    <tr>
      <td align="right">浏览量</td>
      <td><input type="text" class="px" id="hzwzaker_views" value="{$views}" /></td>
    </tr>
</tbody>
    <tr>
      <td colspan="2"><div style="margin-left:100px;margin-top:16px;">
          <input type="button" class="pn pnc" id="hzwzaker_submit" value="　确定采集　" style="padding: 0 10px;line-height: 21px;" />
        </div></td>
    </tr>
  </table>
</div>
<script src="{$_G['siteurl']}source/plugin/csdn123com_zaker/res/jquery.min.js" type="text/javascript"></script> 
<script type="text/javascript">
var csdn123_jQ = jQuery.noConflict(true); (function($, window, undefined) {
    $("#hzwzaker_submit").click(function() {
        var hzwzaker_fromurl = $("#hzwzaker_fromurl").val();
        var hzwzaker_replaynum = $("#hzwzaker_replaynum").val();       
        var hzwzaker_catid=$("#catid").val();
        var hzwzaker_first_uid = $("#hzwzaker_first_uid").val();
        var hzwzaker_reply_uid = $("#hzwzaker_reply_uid").val();
        var hzwzaker_weiyanchang = $("input[name='hzwzaker_weiyanchang']:checked").val();
        var hzwzaker_intval_time = $("#hzwzaker_intval_time").val();
        var hzwzaker_simplified = $("input[name='hzwzaker_simplified']:checked").val();
        var hzwzaker_views = $("#hzwzaker_views").val();
        var hzwzaker_url = "hzwzaker_fromurl=" + encodeURIComponent(hzwzaker_fromurl);
        hzwzaker_url = hzwzaker_url + "&hzwzaker_replaynum=" + hzwzaker_replaynum;
hzwzaker_url = hzwzaker_url + "&hzwzaker_catid=" + hzwzaker_catid;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_first_uid=" + hzwzaker_first_uid;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_reply_uid=" + hzwzaker_reply_uid;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_weiyanchang=" + hzwzaker_weiyanchang;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_intval_time=" + hzwzaker_intval_time;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_simplified=" + hzwzaker_simplified;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_views=" + hzwzaker_views;
        hzwzaker_url = hzwzaker_url + "&hzwzaker_forum_portal=portal";
        hzwzaker_url = hzwzaker_url + "&t=" + (new Date()).getTime();
        if (hzwzaker_fromurl == "") {
            $("#hzwzaker_fromurl").focus();
            return false
        } else {
            $(this).val("执行中，稍等…");
            $.get("{$_G['siteurl']}plugin.php?id=csdn123com_zaker:front_control&" + hzwzaker_url,
            function(data) {
                if (data.length > 10 && data.length < 800) {
                    window.location.href = data
                } else if (data == "no1") {
                    alert("!content_url_error!")
                } else {
                    alert("采集失败！！请确保输入正确和插件设置正确。")
                }
                $("#hzwzaker_submit").val("　确定采集　")
            })
        }
        return false
    })
})(csdn123_jQ, window);
</script> 


EOF;
?>
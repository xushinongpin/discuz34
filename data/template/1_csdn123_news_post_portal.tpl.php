<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$csdn123_news_return = <<<EOF


<div style="margin:4px auto;border:1px solid #CCC;padding:10px;line-height:24px;background:#EEE">
  <div> 关键词：
    <input type="text" id="csdn123keyword" class="px" placeholder="输入您想要采集的内容关键词"  size="60" />
    &nbsp;&nbsp;&nbsp;&nbsp;
    <button type="button"  id="csdn123_query" class="pn vm" style="vertical-align:top;"><em>采集内容</em></button>
    <button type="button"  class="pn vm" style="vertical-align:top;" onclick="alert('如果想要采集最新的内容，请去论坛后台使用“关键词采集”的功能，前台采集的内容会比较旧一点。')"><em>采集最新内容</em></button>
  </div>
  <div style="margin-top:16px;display:
EOF;
 if($csdn123_showmore==0) { 
$csdn123_news_return .= <<<EOF
none
EOF;
 } else { 
$csdn123_news_return .= <<<EOF
block
EOF;
 } 
$csdn123_news_return .= <<<EOF
" id="csdn123_moreGongNeng"> 请选择：
    <select id="csdn123_searchresult">
      <option value="no">----请在上面输入关键词，这儿显示采集的结果----</option>
    </select>
    <span id="csdn123_loading"></span>
    <button type="button"  id="csdn123_someNews" class="pn vm" style="vertical-align:top;display:none"><em>换一批</em></button>
    <button type="button"  id="csdn123_newsPre" class="pn vm" style="vertical-align:top;"><em>上一条</em></button>
    <button type="button"  id="csdn123_newsNext" class="pn vm" style="vertical-align:top;"><em>下一条</em></button>
  </div>
  <div style="clear:both;margin-top:16px;display:
EOF;
 if($csdn123_showmore==0) { 
$csdn123_news_return .= <<<EOF
none
EOF;
 } else { 
$csdn123_news_return .= <<<EOF
block
EOF;
 } 
$csdn123_news_return .= <<<EOF
" id="csdn123_moreGongNeng2"> 其　它：
    <button type="button"  id="csdn123_reset" class="pn vm" style="vertical-align:top;"><em>初始内容</em></button>
    <button type="button"  id="csdn123_likearticle" class="pn vm" style="vertical-align:top;"><em>相似内容</em></button>
    <button type="button"  id="csdn123_tongyici" class="pn vm" style="vertical-align:top;"><em>伪原创</em></button>
    <button type="button"  id="csdn123_localimgae" class="pn vm" style="vertical-align:top;"><em>图片本地化</em></button>
    <button type="button"  id="csdn123_fromurl" class="pn vm" style="vertical-align:top;"><em>来源地址</em></button>
    <button type="button"  id="csdn123_textformat" class="pn vm" style="vertical-align:top;"><em>内容排版</em></button>
    <button type="button"  id="csdn123_jian" class="pn vm" style="vertical-align:top;"><em>简体</em></button>
    <button type="button"  id="csdn123_fan" class="pn vm" style="vertical-align:top;"><em>繁体</em></button>
    <br /><br />常用采集关键词：
<a href="javascript:csdn123_keyword('家居装饰')">家居装饰</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('美女模特')">美女模特</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('娱乐搞笑')">娱乐搞笑</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('励志名言')">励志名言</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('知识文化')">知识文化</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('键康养生')">键康养生</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('菜谱美食')">菜谱美食</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('服饰美容')">服饰美容</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('情感天地')">情感天地</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('旅游休闲')">旅游休闲</a>&nbsp;|&nbsp;
<a href="javascript:csdn123_keyword('影视评论')">影视评论</a>
<br>
    历史采集关键词：<span id="csdn123_tishi_historykeyword"></span> </div>
</div>
<br />
<script src="{$_G['siteurl']}source/plugin/csdn123_news/res/jquery.min.js" type="text/javascript"></script> 
<script src="{$_G['siteurl']}source/plugin/csdn123_news/res/jquery.cookie.js" type="text/javascript"></script> 
<script type="text/javascript">

var _csdn123_siteurl = encodeURIComponent("{$_G['siteurl']}");

var _csdn123_s1teurl = encodeURIComponent(SITEURL);

var csdn123_remoteUrl="";

var csdn123_jQ = jQuery.noConflict(true);

var csdn123_showimg="{$_G['siteurl']}source/plugin/csdn123_news/display_picture.php";

(function ( $, window, undefined ) {

$("#csdn123_query").click(function(){



var csdn123keywordQuery = $("#csdn123keyword").val();

csdn123_getcookies(csdn123keywordQuery);

if(csdn123keywordQuery=="")

{

alert("输入您想要采集的内容关键词");

$("#csdn123keyword").focus();

return;

}

if(csdn123keywordQuery.length<2)

{

alert("您输入的关键词太短了，请输入至少二个字符以上的关键词！");

$("#csdn123keyword").focus();

return;

}

csdn123keywordQuery=encodeURIComponent(csdn123keywordQuery);

$("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

var csdn123_ajax_url="{$_G['siteurl']}plugin.php?id=csdn123_news:csdn123_fun&cms=dz&search_query="+ csdn123keywordQuery +"&csdn123callback=?";

$.getJSON(csdn123_ajax_url, function(data) {

if(data.total>0){



$("#csdn123_searchresult").html("");

csdn123_getRemoteUrlContent(data.items[0].url);

var csdn123_i=0;		

for(csdn123_i=0;csdn123_i<data.items.length;csdn123_i++)

{

$("<option value='" + data.items[csdn123_i].url + "' csdn123fromurl='" + data.items[csdn123_i].fromurl + "'>" + data.items[csdn123_i].title + "</option>").appendTo("#csdn123_searchresult");

}

}  else {

alert("抱歉，未采集到内容！！您可以在论坛后台使用众大云采集来获取更多的内容。");

$("#csdn123keyword").focus();

}

$("#csdn123_loading").html("");

$("#csdn123_someNews").show();

});	

});



$("#csdn123_newsPre,#csdn123_newsNext").click(function(){



var csdn123_sel_index=$("#csdn123_searchresult option:selected").index();

if($(this).text()=="上一条")

{

csdn123_sel_index--;

} else {

csdn123_sel_index++;

}

if(csdn123_sel_index<=0)

{

csdn123_sel_index=0;

}

if(csdn123_sel_index>=$("#csdn123_searchresult option").length)

{

csdn123_sel_index--;

}

var csdn123_preObj=$("#csdn123_searchresult option").eq(csdn123_sel_index);

csdn123_preObj.attr('selected','selected');

csdn123_getRemoteUrlContent(csdn123_preObj.val());



});



$("#csdn123_someNews").click(function(){

$("#csdn123_query").click();

});



$("#csdn123_tongyici").click(function(){



$("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

var csdn123_contentHtmlCode=$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html();

if(csdn123_contentHtmlCode.length<20)

{

alert("编辑器中没有内容或者内容很少，不能调用伪原创的功能。");

$("#csdn123_loading").html('');

return;

}

$.get("{$_G['siteurl']}plugin.php?id=csdn123_news:csdn123_fun&originality=yes",function(data){

if(data.indexOf('=')==-1)
{
alert("您没有添加伪原创词库，请去插件后台添加！");
$("#csdn123_loading").html('');
return false;
}

var csdn123_tempTongyiciArr;

data=data.split(",");				

for(var csdn123_i=0;csdn123_i<data.length;csdn123_i++)

{

csdn123_tempTongyiciArr=data[csdn123_i].split("=");

csdn123_contentHtmlCode=csdn123_contentHtmlCode.replace(csdn123_tempTongyiciArr[0],csdn123_tempTongyiciArr[1]);

}

csdn123_contentHtmlCode=csdn123_contentHtmlCode.replace(/_hzw_/ig,"");

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html(csdn123_contentHtmlCode);

$("#csdn123_loading").html('');



})



});



$("#csdn123_likearticle").click(function(){



$("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

var csdn123_likearticleData=$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").text();

csdn123_likearticleData=csdn123_likearticleData + $("#title").val();

csdn123_likearticleData=csdn123_likearticleData.replace(/[^\u4e00-\u9fa5]/g,'');

if(csdn123_likearticleData.length<5)

{

$("#csdn123_loading").html('');

return;

}

csdn123_likearticleData=encodeURIComponent(csdn123_likearticleData);

$.post("{$_G['siteurl']}plugin.php?id=csdn123_news:csdn123_fun","likearticleData=" + csdn123_likearticleData,function(data){



$("#csdn123keyword").val(data);

$("#csdn123_query").click();

$("#csdn123_loading").html('');



},"json");



});


$("#csdn123_searchresult").change(function(){



csdn123_CurrentRemoteUrl=$(this).children('option:selected').val();

if(csdn123_CurrentRemoteUrl.indexOf("http")!=-1)

{

csdn123_getRemoteUrlContent(csdn123_CurrentRemoteUrl);

}





});



$("#csdn123_localimgae").click(function(){



$("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

if($("#csdn123_tishi_historykeyword").html()=="" && confirm("图片本地化会下载远程图片，在下载图片的过程中可能会导致网页很卡，是否要下载远程的图片到本地存储？？")==false)

{

$("#csdn123_loading").html('');

return false;

}

var csdn123_htmlcode=$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html();

var csdn123_imgPatt = /<img[^<>]*src\s*=\s*(['"]?)([^'"<>]*)\\1(?=\s|\/|>)/img

var csdn123_imgRegStr=csdn123_htmlcode.match(csdn123_imgPatt);

if(!csdn123_imgRegStr)

{

$("#csdn123_loading").html('');

return false;

}

for(var csdn123_i=0;csdn123_i<csdn123_imgRegStr.length;csdn123_i++)

{

var csdn123_imgValue=csdn123_imgRegStr[csdn123_i];

var csdn123_imgValue_arr=csdn123_imgValue.match(/<img[^>]*src\s*=\s*(['"]?)([^'">]*)\\1/i);

csdn123_imgValue=csdn123_imgValue_arr[2];

$.ajax({async:false,cache:false,data:"id=csdn123_news:csdn123_fun&csdn123_localimg=yes&csdn123_localimgUrl="+encodeURIComponent(csdn123_imgValue),type:"GET",url:"{$_G['siteurl']}plugin.php",success:function(data){



csdn123_htmlcode=csdn123_htmlcode.replace(csdn123_imgValue,data);



}})





}

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html(csdn123_htmlcode);

$("#uchome-ifrHtmlEditor").contents().find("#icoDown").click();

$("#csdn123_loading").html('');



});

$("#csdn123_fromurl").click(function(){



var csdn123fromurl=$("#csdn123_searchresult option:selected").attr("csdn123fromurl");

if(csdn123fromurl && csdn123fromurl.indexOf("http")!=-1)

{

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").append("<br><br>来源地址：" + csdn123fromurl);

$("input[name=fromurl]").val(csdn123fromurl);

alert("文章来源地址已经添加到文章的最下面！");

}



});

$("#csdn123_textformat").click(function(){



$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").find("p").before("<span>&nbsp;&nbsp;&nbsp;&nbsp;<span>");

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").find("p").after("<br>");

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").find("img").before("<br>");

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").find("img").after("<br>");



});

$("#csdn123_reset").click(function(){



if(csdn123_remoteUrl.indexOf("http")!=-1)

{

csdn123_getRemoteUrlContent(csdn123_remoteUrl);

}



});

$("#csdn123_jian,#csdn123_fan").click(function(){



$("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

var csdn123_contentHtmlCode=$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html();

var csdn123_jianTextData=$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").text();

var csdn123_title=$("#title").val();

var convertType="toGBK";		

csdn123_jianTextData=csdn123_jianTextData + csdn123_title;

csdn123_jianTextData=csdn123_jianTextData.replace(/[^\u4e00-\u9fa5]/g,'');

if(csdn123_jianTextData.length<3)

{

$("#csdn123_loading").html('');

return;

}

csdn123_jianTextData=encodeURIComponent(csdn123_jianTextData);		

if($(this).attr("id")=="csdn123_fan")

{

convertType="toBIG";

}

$.post("{$_G['siteurl']}plugin.php?id=csdn123_news:csdn123_fun","convertType=" + convertType + "&csdn123_jianTextData=" + csdn123_jianTextData,function(data){



var csdn123_tempTextDataArr;			

for(var csdn123_i=0;csdn123_i<data.length;csdn123_i++)

{

csdn123_tempTextDataArr=data[csdn123_i].split("=");

csdn123_regexp=csdn123_tempTextDataArr[0];

var csdn123_regexp=new RegExp(csdn123_regexp,"ig");

csdn123_contentHtmlCode=csdn123_contentHtmlCode.replace(csdn123_regexp,csdn123_tempTextDataArr[1]);

csdn123_title=csdn123_title.replace(csdn123_regexp,csdn123_tempTextDataArr[1]);

}

$("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html(csdn123_contentHtmlCode);

$("#title").val(csdn123_title);

$("#csdn123_loading").html('');



},"json")



});





})( csdn123_jQ, window);



function csdn123_keyword(str)

{

csdn123_jQ("#csdn123keyword").val(str);

csdn123_jQ("#csdn123_query").click();

}



function csdn123_getRemoteUrlContent(url)
{

csdn123_remoteUrl=url;

csdn123_jQ("#csdn123_loading").html('<img src="' + STATICURL + 'image/common/loading.gif" alt="loading" />');

csdn123_catchUrl="{$_G['siteurl']}plugin.php?id=csdn123_news:csdn123_fun&getremoteurl=yes&cms=dz&ip={$_SERVER['REMOTE_ADDR']}&siteur1=" + _csdn123_siteurl  + "&s1teur1=" + _csdn123_s1teurl + "&url="+ encodeURIComponent(url) +"&csdn123content=?";

csdn123_jQ.getJSON(csdn123_catchUrl,function(data){



if(data.status=="ok")

{
csdn123_jQ("input[name=fromurl]").val('');

csdn123_jQ("#title").val(data.title);				

var csdn123_data_content=data.content;

csdn123_data_content=csdn123_data_content.replace(/http:\/\/www.csdn123.net\/mydata\/showimg\.php/g,csdn123_showimg);

csdn123_data_content=csdn123_data_content.replace(/http:\/\/www.csdn123.net\/mydata\/zhihuimg\.php/g,csdn123_showimg);

csdn123_data_content=csdn123_data_content.replace(/http:\/\/www.csdn123.net\/mydata\/nicimg\.php/g,csdn123_showimg);

csdn123_data_content=csdn123_data_content.replace(/http:\/\/www.csdn123.net\/mydata\/showbaiduimg\.php/g,csdn123_showimg);

csdn123_data_content=csdn123_data_content.replace(/http:\/\/www.csdn123.net\/zd_version\/zd9\/display_picture\.php/g,csdn123_showimg);

csdn123_jQ("#uchome-ifrHtmlEditor").contents().find(".HtmlEditor").contents().find("body").html(csdn123_data_content);

csdn123_jQ("#csdn123_loading").html('');

csdn123_jQ("#csdn123_moreGongNeng,#csdn123_moreGongNeng2").show();

}



});



}



function csdn123_getcookies(csdn123keywordQuery)

{

if(csdn123keywordQuery.indexOf("http")!=-1 || csdn123keywordQuery.length>5)

{

return false;

}

var csdn123TempCookies=csdn123_jQ.cookie("csdn123");

if(csdn123TempCookies==undefined && csdn123keywordQuery=="")

{

return false;



}else if(csdn123keywordQuery!=""){



if(csdn123TempCookies && csdn123TempCookies.indexOf("|")>0)

{

csdn123TempCookies=csdn123TempCookies.replace(csdn123keywordQuery + "|","");

}

if(csdn123TempCookies==undefined)

{

csdn123TempCookies=csdn123keywordQuery + "|";

} else {

csdn123TempCookies=csdn123keywordQuery + "|" + csdn123TempCookies;

}

}

csdn123_jQ.cookie("csdn123",csdn123TempCookies);

var csdn123TempCookiesArr=csdn123TempCookies.split("|");

var csdn123_j=0;

var csdn123_cookieKeyword="";

for(csdn123_j=0;csdn123_j<csdn123TempCookiesArr.length;csdn123_j++)

{

if(csdn123TempCookiesArr[csdn123_j]!="" && csdn123TempCookiesArr[csdn123_j]!="undefined")

{

csdn123_cookieKeyword+="<a href=\"javascript:csdn123_keyword('" + csdn123TempCookiesArr[csdn123_j] + "')\">" + csdn123TempCookiesArr[csdn123_j] + "</a>&nbsp;|&nbsp;"

}

if(csdn123_j>16)

{

break;

}

}

csdn123_jQ("#csdn123_tishi_historykeyword").html(csdn123_cookieKeyword);

}

csdn123_getcookies("");

</script> 


EOF;
?>
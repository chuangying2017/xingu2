/* -----------H-ui前端框架-------------
* H-ui.admin.js v3.1
* http://www.h-ui.net/
* Created & Modified by guojunhui
* Date modified 2017.02.03
* Copyright 2013-2017 北京颖杰联创科技有限公司 All rights reserved.
* Licensed under MIT license.
* http://opensource.org/licenses/MIT
*/
var num=0,oUl=$("#min_title_list"),hide_nav=$("#Hui-tabNav");

/*获取顶部选项卡总长度*/
function tabNavallwidth(){
	var taballwidth=0,
		$tabNav = hide_nav.find(".acrossTab"),
		$tabNavWp = hide_nav.find(".Hui-tabNav-wp"),
		$tabNavitem = hide_nav.find(".acrossTab li"),
		$tabNavmore =hide_nav.find(".Hui-tabNav-more");
	if (!$tabNav[0]){return}
	$tabNavitem.each(function(index, element) {
        taballwidth += Number(parseFloat($(this).width()+60))
    });
	$tabNav.width(taballwidth+25);
	var w = $tabNavWp.width();
	if(taballwidth+25>w){
		$tabNavmore.show()}
	else{
		$tabNavmore.hide();
		$tabNav.css({left:0});
	}
}

/*左侧菜单响应式*/
function Huiasidedisplay(){
	if($(window).width()>=768){
		$(".Hui-aside").show();
	} 
}
/*获取皮肤cookie*/
function getskincookie(){
	var v = $.cookie("Huiskin");
	var hrefStr=$("#skin").attr("href");
	if(v==null||v==""){
		v="default";
	}
	if(hrefStr!=undefined){
		var hrefRes=hrefStr.substring(0,hrefStr.lastIndexOf('skin/'))+'skin/'+v+'/skin.css';
		$("#skin").attr("href",hrefRes);
	}
}
/*菜单导航*/
function Hui_admin_tab(obj){
	var bStop = false,
		bStopIndex = 0,
		href = $(obj).attr('data-href'),
		title = $(obj).attr("data-title"),
		topWindow = $(window.parent.document),
		show_navLi = topWindow.find("#min_title_list li"),
		iframe_box = topWindow.find("#iframe_box");
	//console.log(topWindow);
	if(!href||href==""){
		alert("data-href不存在，v2.5版本之前用_href属性，升级后请改为data-href属性");
		return false;
	}if(!title){
		alert("v2.5版本之后使用data-title属性");
		return false;
	}
	if(title==""){
		alert("data-title属性不能为空");
		return false;
	}
	show_navLi.each(function() {
		if($(this).find('span').attr("data-href")==href){
			bStop=true;
			bStopIndex=show_navLi.index($(this));
			return false;
		}
	});
	if(!bStop){
		creatIframe(href,title);
		min_titleList();
	}
	else{
		show_navLi.removeClass("active").eq(bStopIndex).addClass("active");			
		iframe_box.find(".show_iframe").hide().eq(bStopIndex).show().find("iframe").attr("src",href);
	}	
}
/*最新tab标题栏列表*/
function min_titleList(){
	var topWindow = $(window.parent.document),
		show_nav = topWindow.find("#min_title_list"),
		aLi = show_nav.find("li");
}
/*创建iframe*/
function creatIframe(href,titleName){
	var topWindow=$(window.parent.document),
		show_nav=topWindow.find('#min_title_list'),
		iframe_box=topWindow.find('#iframe_box'),
		iframeBox=iframe_box.find('.show_iframe'),
		$tabNav = topWindow.find(".acrossTab"),
		$tabNavWp = topWindow.find(".Hui-tabNav-wp"),
		$tabNavmore =topWindow.find(".Hui-tabNav-more");
	var taballwidth=0;
		
	show_nav.find('li').removeClass("active");	
	show_nav.append('<li class="active"><span data-href="'+href+'">'+titleName+'</span><i></i><em></em></li>');
	if('function'==typeof $('#min_title_list li').contextMenu){
		$("#min_title_list li").contextMenu('Huiadminmenu', {
			bindings: {
				'closethis': function(t) {
					var $t = $(t);				
					if($t.find("i")){
						$t.find("i").trigger("click");
					}
				},
				'closeall': function(t) {
					$("#min_title_list li i").trigger("click");
				},
			}
		});
	}else {
		
	}	
	var $tabNavitem = topWindow.find(".acrossTab li");
	if (!$tabNav[0]){return}
	$tabNavitem.each(function(index, element) {
        taballwidth+=Number(parseFloat($(this).width()+60))
    });
	$tabNav.width(taballwidth+25);
	var w = $tabNavWp.width();
	if(taballwidth+25>w){
		$tabNavmore.show()}
	else{
		$tabNavmore.hide();
		$tabNav.css({left:0})
	}	
	iframeBox.hide();
	iframe_box.append('<div class="show_iframe"><div class="loading"></div><iframe frameborder="0" src='+href+'></iframe></div>');
	var showBox=iframe_box.find('.show_iframe:visible');
	showBox.find('iframe').load(function(){
		showBox.find('.loading').hide();
	});
}
/*关闭iframe*/
function removeIframe(){
	var topWindow = $(window.parent.document),
		iframe = topWindow.find('#iframe_box .show_iframe'),
		tab = topWindow.find(".acrossTab li"),
		showTab = topWindow.find(".acrossTab li.active"),
		showBox=topWindow.find('.show_iframe:visible'),
		i = showTab.index();
	tab.eq(i-1).addClass("active");
	tab.eq(i).remove();
	iframe.eq(i-1).show();	
	iframe.eq(i).remove();
}
/*关闭所有iframe*/
function removeIframeAll(){
	var topWindow = $(window.parent.document),
		iframe = topWindow.find('#iframe_box .show_iframe'),
		tab = topWindow.find(".acrossTab li");
	for(var i=0;i<tab.length;i++){
		if(tab.eq(i).find("i").length>0){
			tab.eq(i).remove();
			iframe.eq(i).remove();
		}
	}
}
//展示页面
function showpage(title,url,w,h){
    layer_show(title,url,w,h);
}
//首先展示单个规则的数据
function admin_role_edit_s(title,url,id,w,h){
			url = '/admin/rbac/admin_role_edit_show';
			$.ajax({
				url:url,
				data:{id:id},
				type:'get',
			});
    layer_show(title,url,w,h);
}
/*弹出层*/
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
function layer_show(title,url,w,h){
	if (title == null || title == '') {
		title=false;
	};
	if (url == null || url == '') {
		url="404.html";
	};
	if (w == null || w == '') {
		w=800;
	};
	if (h == null || h == '') {
		h=($(window).height() - 50);
	};
	layer.open({
		type: 2,
		area: [w+'px', h +'px'],
		fix: false, //不固定
		maxmin: true,
		shade:0.4,
		title: title,
		content: url
	});
}
/*关闭弹出框口*/
function layer_close(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}
/*时间*/
function getHTMLDate(obj) {
    var d = new Date();
    var weekday = new Array(7);
    var _mm = "";
    var _dd = "";
    var _ww = "";
    weekday[0] = "星期日";
    weekday[1] = "星期一";
    weekday[2] = "星期二";
    weekday[3] = "星期三";
    weekday[4] = "星期四";
    weekday[5] = "星期五";
    weekday[6] = "星期六";
    _yy = d.getFullYear();
    _mm = d.getMonth() + 1;
    _dd = d.getDate();
    _ww = weekday[d.getDay()];
    obj.html(_yy + "年" + _mm + "月" + _dd + "日 " + _ww);
};

$(function(){
	getHTMLDate($("#top_time"));
	getskincookie();
	//layer.config({extend: 'extend/layer.ext.js'});
	Huiasidedisplay();
	var resizeID;
	$(window).resize(function(){
		clearTimeout(resizeID);
		resizeID = setTimeout(function(){
			Huiasidedisplay();
		},500);
	});
	
	$(".nav-toggle").click(function(){
		$(".Hui-aside").slideToggle();
	});
	$(".Hui-aside").on("click",".menu_dropdown dd li a",function(){
		if($(window).width()<768){
			$(".Hui-aside").slideToggle();
		}
	});
	/*左侧菜单*/
	$(".Hui-aside").Huifold({
		titCell:'.menu_dropdown dl dt',
		mainCell:'.menu_dropdown dl dd',
	});
	
	/*选项卡导航*/
	$(".Hui-aside").on("click",".menu_dropdown a",function(){
		Hui_admin_tab(this);
	});
	
	$(document).on("click","#min_title_list li",function(){
		var bStopIndex=$(this).index();
		var iframe_box=$("#iframe_box");
		$("#min_title_list li").removeClass("active").eq(bStopIndex).addClass("active");
		iframe_box.find(".show_iframe").hide().eq(bStopIndex).show();
	});
	$(document).on("click","#min_title_list li i",function(){
		var aCloseIndex=$(this).parents("li").index();
		$(this).parent().remove();
		$('#iframe_box').find('.show_iframe').eq(aCloseIndex).remove();	
		num==0?num=0:num--;
		tabNavallwidth();
	});
	$(document).on("dblclick","#min_title_list li",function(){
		var aCloseIndex=$(this).index();
		var iframe_box=$("#iframe_box");
		if(aCloseIndex>0){
			$(this).remove();
			$('#iframe_box').find('.show_iframe').eq(aCloseIndex).remove();	
			num==0?num=0:num--;
			$("#min_title_list li").removeClass("active").eq(aCloseIndex-1).addClass("active");
			iframe_box.find(".show_iframe").hide().eq(aCloseIndex-1).show();
			tabNavallwidth();
		}else{
			return false;
		}
	});
	tabNavallwidth();
	
	$('#js-tabNav-next').click(function(){
		num==oUl.find('li').length-1?num=oUl.find('li').length-1:num++;
		toNavPos();
	});
	$('#js-tabNav-prev').click(function(){
		num==0?num=0:num--;
		toNavPos();
	});
	
	function toNavPos(){
		oUl.stop().animate({'left':-num*100},100);
	}
	
	/*换肤*/
	$("#Hui-skin .dropDown-menu a").click(function(){
		var v = $(this).attr("data-val");
		$.cookie("Huiskin", v);
		var hrefStr=$("#skin").attr("href");
		var hrefRes=hrefStr.substring(0,hrefStr.lastIndexOf('skin/'))+'skin/'+v+'/skin.css';
		$(window.frames.document).contents().find("#skin").attr("href",hrefRes);
	});
});
//添加规则
function admin_role_add_save() {
    var text = '';
    $('input[name="power_id[]"]:checked').each(function () {
        text += "," + $(this).val();
    });

    var rolename = $('#roleName').val();
    var remarks = $('#remarks').val();

    $.ajax({
        type: "post",
        url: "/Admin/Rbac/admin_role_add",
        data: {powerid: text, remarks: remarks, rolename: rolename},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
//编辑规则
function admin_role_add_edit() {
    var text = '';
    $('input[name="power_id[]"]:checked').each(function () {
        text += "," + $(this).val();
    });

    var rolename = $('#roleName').val();
    var remarks = $('#remarks').val();
	var edit_id = $('input[name="edit_id"]').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/admin_role_edit",
        data: {powerid: text, remarks: remarks, rolename: rolename,id:edit_id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
//根据传过来的id和地址去删除
function admin_role_del(obj,id,url){
    layer.confirm('确认要删除吗？',function(index){
        $.ajax({
            type: 'POST',
            url:url,
            data:{id:id},
            dataType: 'json',
            success: function(data){
                if(data.status == 1){
                    layer.msg(data.msg,{icon:1,time:1000},function(){
                        $(obj).parents("tr").remove();
                    });
                }else{
                    layer.msg(data.msg,{icon:2,time:1000});
                }

            },
            error:function(data) {
                console.log(data.msg);
            },
        });
    });
}
//编辑权限节点
function power_edit_save(){
	var name = $('input[name="name"]').val();
	//这是修改的id
	var powerid = $('input[name="powerid"]').val();
	var controls = $('input[name="mobile"]').val();
	var pid = $('input[name="pid"]').val();
	var sort = $('input[name="sort"]').val();
	var level = $('input[name="level"]').val();
	var address = $('input[name="address"]').val();
	$.ajax({
		url:'/admin/rbac/power_edit_save',
		type:'post',
		data:{name:name,powerid:powerid,control:controls,pid:pid,sort:sort,level:level,address:address},
		dataType:'json',
		success:function (data) {
			if(data.status == 1){
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
			}else{
                layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
			}
        }
	});
}
//编辑前台节点
function edit_show(url){
    var name = $('input[name="name"]').val();
    var controls = $('input[name="mobile"]').val();
    var pid = $('input[name="pid"]').val();
    var path = $('input[name="path"]').val();
    var level = $('input[name="level"]').val();
    var style = $('input[name="style"]').val();
    $.ajax({
        url:url,
        type:'post',
        data:{name:name,control_action:controls,pid:pid,path:path,level:level,style:style},
        dataType:'json',
        success:function (data) {
            if(data.status == 1){
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
            }else{
                layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
            }
        }
    });
		/*$("form").ajaxSubmit({
			url:url,
			async:false,
			type:'post',
			success:function(data){
				if(data.status == 1){
					layer.msg(data.msg,{icon:1,time:1000},function(){
						var index = parent.layer.getFrameIndex(window.name);
							parent.location.reload();
							parent.layer.close(index);
					})
				}else{
					layer.msg(data.msg,{icon:2,time:2},function(){
						var index = parent.layer.getFrameIndex(window.name);
						parent.location.reload();
						parent.layer.close(index);
					})
				}
			}
		})*/
}
//添加节点
function power_add(){
	var pid = $('select[name="pid"]').val();
	var control = $('input[name="control_action"]').val();
	var name = $('input[name="name"]').val();
	$.ajax({
		url:'/admin/rbac/power_add',
		data:{pid:pid,control:control,name:name},
		type:'post',
		dataType:'json',
		success:function (data) {
			if(data.status == 1){
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
			}else{
                layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
			}
        }
	})
}
//添加前台节点
function ago_power(url){
    var pid = $('select[name="pid"]').val();
    var control = $('input[name="control_action"]').val();
    var name = $('input[name="name"]').val();
    $.ajax({
        url:url,
        data:{pid:pid,control_action:control,name:name,token:$('input[name="__token__"]').val()},
        type:'post',
        dataType:'json',
        success:function (data) {
            if(data.status == 1){
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
            }else{
                layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
            }
        }
    })
}
//节点批量删除
function admin_batch_del(url){
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="powerid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: url,
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });



    });
}
/*管理员-停用*/
function admin_stop(obj,id,url,url1){

        //此处请求后台程序，下方是成功后的前台处理……
        $.ajax({
            url:url,
            data:{id:id},
            type:'get',
            dataType:'json',
            success:function (data) {
                if(data.status == 1){
                    $(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title="启用" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已停用</span>');
                    $(obj).remove();
                    layer.msg(data.msg,{icon: 1,time:1000});
                }else{
                    layer.msg(data.msg,{icon:2,time:1000});
                }
            }
        });



}
/*管理员-启用*/
function admin_start(obj,id,url,url1){

        //此处请求后台程序，下方是成功后的前台处理……
		$.ajax({
            url:url,
            data:{id:id},
            type:'get',
            dataType:'json',
			success:function (data) {
				if(data.status == 1){
                    $(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title="停用" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                    $(obj).remove();
                    layer.msg(data.msg, {icon: 1,time:1000});
				}else{
                    layer.msg(data.msg, {icon: 2,time:1000});
				}
            }
		});



}
/*管理员-修改-展示*/
function change_password(title,url,id,w,h){
		$.ajax({
			url:url,
			data:{id:id},
			type:'post'
		});
    layer_show(title,url,w,h);
}
/*后台密码修改*/
function xiugai_password(){
	//alert(1);return false;
    var tt= $('#teacher-new-password').val();
    var dd = $('#teacher-new-password2').val();
    var id = $('input[name="uid"]').val();
    $.ajax({
        url:'/admin/rbac/houtaixiu',
        data:{id:id,password:tt,password_confirm:dd},
        type:'post',
        dataType:'json',
        success:function (data) {
            if(data.status == 1){
                layer.msg(data.msg, {icon: 1, time: 2000},function (){
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                })
            }else{
                    layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.location.reload();
                        parent.layer.close(index);
                    })
            }
        }
    });
}
//登录验证
function check_login(){
    var uname = $.trim($('.username').val());
    var pwd = $.trim($('.pwd').val());
    var code = $.trim($('.code').val());
    var token = $('input[name="__token__"]').val();
    if (!uname) {
        $('.username_tip').html('请输入你的账号！');
        return false;
    } else {
        $('.username_tip').html('');
    }
    if (!pwd || (pwd.length<6&&pwd.length>12)) {
        $('.password_tip').html('请输入6-12位的密码！');
        return false;
    } else {
        $('.password_tip').html('');
    }
    if (!code || (code.length<4)) {
        $('.code_tip').html('请输入4位数字的验证码！');
        window.location.reload();
    } else {
        $('.code_tip').html('');
    }
    if (uname && pwd && code) {
        $.ajax({
            type:"post",
            url:"/admin/login/login",
            data: {username:uname,password:pwd,code:code,token:token},
            dataType: 'json',
            async : false,//设置为同步操作就可以给全局变量赋值成功
            success:function(data){
                if(data.status == 1) {
                    $('.username_tip').html('');
                    $('.password_tip').html('');
                    $('.code_tip ').html('');
                    location.href = data.url;
                }
                else
                {
                    if(data.type ==1)
                    {
                        $('.code_tip').html(data.msg);
                    	window.location.reload();
                    }
                    else if (data.type ==2)
                    {
                        $('.code_tip').html(data.msg);
                        window.location.reload();
                    }
                    else {
                        $('.code_tip').html(data.msg);
                        window.location.reload();
                    }
                }
            }
        });
    }
}
/*资讯-编辑*/
function article_edit_save() {
    var id = $('.id').val();
    var art_title = $('.art_title').val();
    var art_source = $('.art_source').val();
    var art_author = $('.art_author').val();
    var art_type = $('.art_type').val();
    var editorvalue = UE.getEditor('editor').getContent();
    $.ajax({
        type: "post",
        url: "/Admin/Article/articleedit",
        data: {id: id, art_title: art_title, art_source: art_source, art_author: art_author, art_type: art_type, editorvalue: editorvalue},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });
}
//编辑添加文章
function article_add_save(){
	var art_type = $('.art_type').val();
	var art_source = $('.art_source').val();
	var art_title = $('.art_title').val();
	var art_author = $('.art_author').val();
	var editor = UE.getEditor('editor').getContent();
	$.ajax({
		url:'/admin/article/articleadd',
		data:{art_type:art_type,art_source:art_source,art_title:art_title,art_author:art_author,art_content:editor},
		type:'post',
		dataType:'json',
		async:false,
		success:function(data){
				if(data.status == 1){
					layer.msg(data.msg,{icon:1,time:2000},function(){
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.location.reload();
                        parent.layer.close(index);
					});
				}else{
						layer.msg(data.msg,{icon:2,time:2000},function(){
							var index = parent.layer.getFrameIndex(window.name);//先得到当前iframe层的索引
							parent.location.reload();
							parent.layer.close(index);
						});
				}
		}
	})
}
//编辑分类
function product_edit_class_save(id){
		$.ajax({
			url:'/admin/product/productclassedit',
			data:{id:id,type_name:$('input[name="type_name"]').val(),type_message:$('textarea[name="type_message"]').val()},
			dataType:'json',
			type:'post',
			success:function(data){
				if(data.status == 1){
                    layer.msg(data.msg,{icon:1,time:2000},function(){
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.location.reload();
                        parent.layer.close(index);
					});
				}else{
						layer.msg(data.msg,{icon:2,time:1000},function(){
							var index = parent.layer.getFrameIndex(window.name);
							parent.location.reload();
							parent.layer.close(index);
						});
				}
			}

		});
}
//添加文章分类
function product_add_artivle(){
    var type_name = $('input[name="type_name"]').val();
    var content = $('textarea[name="type_message"]').val();
    $.ajax({
        url:'/admin/product/product_add_article',
        data:{type_name:type_name,type_message:content},
        dataType:'json',
        type:'post',
        success:function(data) {
            if (data.status == 1) {
                layer.msg(data.msg,{icon:1,time:2000},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }else{
                layer.msg(data.msg,{icon:2,time:2000},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }
        }

    });
}

//添加分类
function product_add_class_save(){
		var class_name = $('#sel_Sub').val();
		var type_name = $('input[name="type_name"]').val();
		var content = $('textarea[name="content"]').val();
		$.ajax({
			url:'/admin/product/product_class_adds',
			data:{pid:class_name,title:type_name,content:content},
			dataType:'json',
			type:'post',
			success:function(data) {
                if (data.status == 1) {
					layer.msg(data.msg,{icon:1,time:2000},function(){
						var index = parent.layer.getFrameIndex(window.name);
						parent.location.reload();
						parent.layer.close(index);
					});
                }else{
						layer.msg(data.msg,{icon:2,time:2000},function(){
							var index = parent.layer.getFrameIndex(window.name);
							parent.location.reload();
							parent.layer.close(index);
						});
				}
            }

		});
}
//编辑分类
/*function product_wuxian_class_save(url){

	console.log(url);
    var class_name = $('#sel_Sub').val();
    var type_name = $('input[name="type_name"]').val();
    var content = $('textarea[name="content"]').val();
    var id = $('input[name="gid"]').val();
    var token = $('input[name="__token__"]').val();
    var pid_id =$('input[name="pid_id"]').val();
    $.ajax({
        url:url,
        data:{pid:class_name,title:type_name,content:content,id:id,token:token,pid_id:pid_id},
        dataType:'json',
        type:'post',
        success:function(data) {
            if (data.status == 1) {
                layer.msg(data.msg,{icon:1,time:2000},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }else{
                layer.msg(data.msg,{icon:2,time:2000},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }
        }

    });
}*/
//添加产品
function product_add_save(form,url){
	$(form).ajaxSubmit({
		url:url,
		type:'post',
		dataType:'json',
		async:false,
		success:function(data){
			console.log(data);
			if(data.status == 1){
				layer.msg(data.msg,{icon:1,time:1000},function(){
					var index = parent.layer.getFrameIndex(window.name);
						parent.location.reload();
						parent.layer.close(index);
				})
			}else{
				layer.msg(data.msg,{icon:2,time:2000});
			}
		}

	});
}
//添加会员
function user_add_save(form,url){
	$(form).ajaxSubmit({
			url:url,
			type:'post',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.status == 1){
						layer.msg(data.msg,{icon:1,time:1500},function(){
							var index = parent.layer.getFrameIndex(window.name);
								parent.location.reload();
								parent.layer.close(index);
						});
				}else{
							layer.msg(data.msg,{icon:2,time:2000});
				}

		}
	})
}
//修改密码
function user_password_save(id){
	var newpassword =$('.newpassword').val();
	var repassword = $('.repassword').val();
	var type_select = $('.type_select').val();
    $.ajax({
        type: "post",
        url: "/Admin/Member/userpasswordedit",
        data: {id: id, newpassword: newpassword,repassword:repassword,type_select:type_select},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });
}
/*用户-编辑-保存*/
function user_edit_save(id) {

    var name = $('.name').val();
    var mobile = $('.mobile').val();
    var id_card_number = $('select[name="type"]').val();

    $.ajax({
        type: "post",
        url: "/Admin/Member/useredit",
        data: {name: name, id: id, mobile: mobile, level: id_card_number},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}
//用户充值金额或者扣款
function recharge(url,id) {
    var type = $('.type').val();
    var income = $('.income').val();
    var message = $('.message').val();
    var type_select = $('.type_select').val();
    $.ajax({
        type: "post",
        url: url,
        data: {type: type, income: income, user_id:id, message: message,type_select:type_select},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}
//重置密码
function resetpassword(form,url){
		var potions = {
				url:url,
				async:false,
				type:'post',
				dataType:'json',
				success:function (data) {
						if(data.status == 1){
							layer.msg(data.msg,{icon:1,time:1500},function(){
								var index = parent.layer.getFrameIndex(window.name);
									parent.location.reload();
									parent.layer.close(index);
							});
						}else{
							layer.msg(data.msg,{icon:1,time:1500});
						}
                }
		};
		$("form").ajaxSubmit(potions);
		return false
}
//基本设置
function baseconfig()
{
    var onoff = $('input[name="onoff"]:checked').val();
    var chaoshi = $('input[name="chaoshi"]:checked').val();
    var webname = $('.webname').val();
    var weburl = $('.weburl').val();
    var title = $('.title').val();
    var keywords = $('.keywords').val();
    var description = $('.description').val();
    var copyright = $('.copyright').val();
    var icp = $('.icp').val();
    var cnzz = $('.cnzz').val();
    var ip = $('.ip').val();
    var num = $('.num').val();
    var email_status = $('input[name="email_status"]:checked').val();
    var smtpserver = $('.smtpserver').val();
    var smtpport = $('.smtpport').val();
    var smtpuser = $('.smtpuser').val();
    var smtppwd = $('.smtppwd').val();
    var interst = $('.interst').val();
    var smsusername = $('.smsusername').val();
    var smspassword = $('.smspassword').val();
    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/index",
        data: {smsusername: smsusername, smspassword: smspassword, onoff: onoff, chaoshi: chaoshi, webname: webname, weburl: weburl, title: title, keywords: keywords, description: description, copyright: copyright, icp: icp, cnzz: cnzz, ip: ip, num: num, email_status: email_status, smtpserver: smtpserver, smtpport: smtpport, smtpuser: smtpuser, smtppwd: smtppwd, interst: interst},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000});
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}
function settingsave(){

    var onoff = $('input[name="onoff"]:checked').val();
    var chaoshi = $('input[name="chaoshi"]:checked').val();
    var webname = $('.webname').val();
    var weburl = $('.weburl').val();
    var title = $('.title').val();
    var keywords = $('.keywords').val();
    var description = $('.description').val();
    var copyright = $('.copyright').val();
    var icp = $('.icp').val();
    var cnzz = $('.cnzz').val();
    var ip = $('.ip').val();
    var num = $('.num').val();
    var email_status = $('input[name="email_status"]:checked').val();
    var smtpserver = $('.smtpserver').val();
    var smtpport = $('.smtpport').val();
    var smtpuser = $('.smtpuser').val();
    var smtppwd = $('.smtppwd').val();
    var interst = $('.interst').val();
    var smsusername = $('.smsusername').val();
    var smspassword = $('.smspassword').val();
    var smsservice = $('.smsservice').val();
    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/index",
        data: {smsusername: smsusername,smsservice:smsservice, smspassword: smspassword, onoff: onoff, chaoshi: chaoshi, webname: webname, weburl: weburl, title: title, keywords: keywords, description: description, copyright: copyright, icp: icp, cnzz: cnzz, ip: ip, num: num, email_status: email_status, smtpserver: smtpserver, smtpport: smtpport, smtpuser: smtpuser, smtppwd: smtppwd, interst: interst},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功
        success: function (data) {
        		console.log(data);
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000});
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });
}
//添加银行
function bankadd(url){
			$.ajax({
				url:url,
				data:{bankcrad:$('input[name="bankname"]').val(),sort:$('input[name="sort"]').val()},
				type:'post',
				dataType:'json',
				async:false,
				success:function(data){
					if(data.status == 1){
						layer.msg(data.msg,{icon:1,time:1500},function(){
							var index = parent.layer.getFrameIndex(window.name);
								parent.location.reload();
								parent.layer.close(index);
						});
					}else{
						layer.msg(data.msg,{icon:1,time:1500},function(){
							var index = parent.layer.getFrameIndex(window.name);
								parent.location.reload();
								parent.layer.close(index);
						});
					}
				}
			})
}
//编辑银行
function bank_edit_save(id){
		$.ajax({
			url:'admin/webconfig/bank_edit',
			data:{id:id,bankcrad:$('input[name="bankname"]').val(),sort:$('input[name="sort"]').val()},
			async:false,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					layer.msg(data.msg,{icon:1,time:1000},function(){
						var index = parent.layer.getFrameIndex(window.name);
							parent.location.reload();
							parent.layer,close(index)
					});
				}else{
					layer.msg(data.msg,{icon:1,time:1000},function(){
						var index = parent.getFrameIndex(window.name);
						parent.location.reload();
						parent.layer.close(index);
					});
				}
			}
		});
}
//商品下架
function admin_xiajia(obj,id,url,url1){
		$.ajax({
			url:url,
			data:{id:id},
			type:'get',
			dataType:'json',
			async:false,
			success:function(data){
				if(data.status == 1){
					$(obj).parents("tr").find('.td-manage').prepend('<a style="text-decoration:none" onClick="admin_shangjia(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe631;</i>');
					$(obj).parents('tr').find('.td-status').html('<span  class="label">已下架</span>');
					$(obj).remove();
					layer.msg(data.msg,{icon:1,time:1500});
				}else {
					layer.msg(data.msg,{icon:2,time:2000});
				}
			}
		});
}
//商品上架
function admin_shangjia(obj,id,url,url1){
	$.ajax({
		url:url,
		data:{id:id},
		type:'get',
		dataType:'json',
		async:false,
		success:function(data){
				if(data.status == 1){
						$(obj).parents("tr").find('.td-manage').prepend('<a style="text-decoration:none" onClick="admin_xiajia(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe6e1;</i></i></a>');
						$(obj).parents("tr").find('.td-status').html('<span class="label label-success">已发布</span>');
						$(obj).remove();
						layer.msg(data.msg,{icon:1,time:1000});
				}else{
					layer.msg(data.msg,{icon:2,time:1500});
				}
		}
	});
}
//否主图
function fou_tu(obj,id,url,url1){
	$.ajax({
		url:url,
		type:'get',
		dataType:"json",
		data:{id:id},
		async:false,
		success:function(data){
			if(data.status == 1){
				$(obj).parents("tr").find('.td-manage').prepend(' <td class="f-14 td-manage"><a style="text-decoration:none" onClick="shi_tu(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find('.td-status').html('<span  class="label">否</span>');
				$(obj).remove();
				layer.msg(data.msg,{icon:1,time:1000},function(){
					window.location.reload();
				});
			}else{
				layer.msg(data.msg,{icon:2,time:1500});
			}
		}
	});
}
//是图
function shi_tu(obj,id,url,url1){
	$.ajax({
		url:url,
		data:{id:id},
		type:'get',
		dataType:'json',
		success:function(data){
			if(data.status == 1){
				$(obj).parents("tr").find('.td-manage').prepend('<td class="f-14 td-manage"><a style="text-decoration:none" onClick="fou_tu(this,\''+id+'\',\''+url1+'\',\''+url+'\')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe6e1;</i></i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success">是</span>');
				$(obj).remove();
				layer.msg(data.msg,{icon:1,time:1400},function(){
					window.location.reload();
				});
			}else{
				layer.msg(data.msg,{icon:2,time:1500});
			}
		}
	})
}



function leaving_tye(){//提交回复
    var editorVal =UE.getEditor('editor').getContent();
    var id = $('#id').val();
    $.ajax({
        url:'/admin/member/updeta',
        data:{id:id,reply:editorVal},
        type:'get',
        dataType:'json',
        success:function(data){
            if(data.status == 1){
                layer.msg(data.msg,{icon:1,time:1500},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }else{
                layer.msg(data.msg,{icon:2,time:1500});
            }
        }
    })
}

function leaving_tyes(){//修改回复
    var editorVal =UE.getEditor('editor').getContent();
    var id = $('#id').val();
    $.ajax({
        url:'/admin/member/updetas',
        data:{id:id,reply:editorVal},
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status == 1){
                layer.msg(data.msg,{icon:1,time:1500},function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }else{
                layer.msg(data.msg,{icon:2,time:1500});
            }
        }
    })
}

//根据传过来的id和地址去删除回复
function admin_leaving_del(obj,id,url){
    layer.confirm('确认要删除吗？',function(index){
        $.ajax({
            type: 'POST',
            url:url,
            data:{id:id},
            dataType: 'json',
            success: function(data){
                if(data.status == 1){
                    layer.msg(data.msg,{icon:1,time:1000},function(){
                        $(obj).parents("tr").remove();
                    });
                }else{
                    layer.msg(data.msg,{icon:2,time:1000});
                }

            },
            error:function(data) {
                console.log(data.msg);
            },
        });
    });
}
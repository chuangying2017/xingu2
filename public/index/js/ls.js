mui('#nav').on('tap','a',function(){
    var getAttribute = this.getAttribute('data-href');
    window.location.href = getAttribute;
})

//复制链接
function copyToClipboard(obj,elementId) {
    // 创建元素用于复制
    var aux = document.createElement("input");

    // 获取复制内容
    var content = document.getElementById(elementId).innerHTML || document.getElementById(elementId).text;

    // 设置元素内容
    aux.setAttribute("value", content);

    // 将元素插入页面进行调用
    document.body.appendChild(aux);

    // 复制内容
    aux.select();

    // 将内容复制到剪贴板
    document.execCommand("copy");

    // 删除创建元素
    document.body.removeChild(aux);
    mui.alert('复制成功')
}


function Unbundling(a,b){
    var btnArray = ['确定', '取消'];
    mui.prompt('请输入您的安全密码：', '', '为确保您的账户安全', btnArray, function(e) {
        if (e.index == 0) {
            var password_twoe = e.value;
            $.ajax({
                type: "post",
                url: "/index/suey/Unbundling",
                data: {id: a,password_two:password_twoe},
                dataType: 'json',
                async: false, //设置为同步操作就可以给全局变量赋值成功
                success: function (data) {
                    if(data.status == 1){
                        mui.alert('解除成功');
                        var chiLength = $(b).parent().parent().parent().children().length;
                        if(chiLength>1){
                            setTimeout(function(){   window.location.reload();},1000);
                        }else{
                            $(b).parent().parent().remove();
                            setTimeout(function(){  window.location.href = '/index/user';},1000);
                        }
                    }
                    if(data.status == 2){
                        mui.alert(data.msg);
						setTimeout(function(){   window.location.reload();},1000);
                    }}
            });
        } else {
        }
    })
    document.querySelector('.mui-popup-input input').type='password'
}


function loginLs(a,b,c){
    if($(a).val() == ''){
        $(a).val('');
        $(b).val('');
        $(c).val('');
       return  mui.alert('原登陆密码不能为空')
    }
    if($(a).val() == $(b).val()){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('旧密码不能跟新密码一样！');
    }
    if(!(/^[a-z\d]{6,12}$/i.test($(b).val()))){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('密码格式为6-12位字母和数字组成');
    }
    if($(c).val() !== $(b).val()){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('两次密码不一致');
    }
    mui.ajax('/index/user/loginpass', {
        data: {password:$(a).val(),xpassword:$(b).val(),rexpasswo:$(c).val()},
        dataType: 'json',
        type: 'post', //HTTP请求类型
        timeout: 10000, //超时时间设置为10秒；
        success: function (data) {
            if(data.status == 1){
                mui.alert(data.msg);
                setTimeout(function(){  window.location.href = data.url;},1000);
            }
            if(data.status == 2){
                $(a).val('');
                $(b).val('');
                $(c).val('');
                mui.alert(data.msg);
            }
        }
    });
}

function anquanLs(a,b,c){
    if($(a).val() == ''){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return  mui.alert('原安全密码不能为空')
    }
    if($(a).val() == $(b).val()){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('旧密码不能跟新密码一样！');
    }
    if(!(/^[a-z\d]{6,12}$/i.test($(b).val()))){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('密码格式为6-12位字母和数字组成');
    }
    if($(c).val() !== $(b).val()){
        $(a).val('');
        $(b).val('');
        $(c).val('');
        return mui.alert('两次密码不一致');
    }
    mui.ajax('/index/user/anquanLs', {
        data: {password_two:$(a).val(),xpassword:$(b).val(),rexpasswo:$(c).val()},
        dataType: 'json',
        type: 'post', //HTTP请求类型
        timeout: 10000, //超时时间设置为10秒；
        success: function (data) {
            if(data.status == 1){
                mui.alert(data.msg);
                setTimeout(function(){  window.location.href = data.url;},1500);
            }
            if(data.status == 2){
                $(a).val('');
                $(b).val('');
                $(c).val('');
                mui.alert(data.msg);
            }
        }
    });
}



//时间戳转换日期
function UnixToDate(unixTime, isFull, timeZone) {
    if (typeof (timeZone) == 'number'){
        unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
    }
    var time = new Date(unixTime * 1000);
    var ymdhis = "";
    ymdhis += time.getUTCFullYear() + "-";
    ymdhis += (time.getUTCMonth()+1) + "-";
    ymdhis += time.getUTCDate() + ' ';
    ymdhis += time.getHours() + ':';
    ymdhis += time.getMinutes() + ':';
    ymdhis += time.getSeconds();
    if (isFull === true){
        ymdhis += " " + time.getUTCHours() + ":";
        ymdhis += time.getUTCMinutes() + ":";
        ymdhis += time.getUTCSeconds();
    }
    return ymdhis;
}


//时间戳转换日期
function UnixToDates(unixTime, isFull, timeZone) {
    if (typeof (timeZone) == 'number'){
        unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
    }
    var time = new Date(unixTime * 1000);
    var ymdhis = "";
    ymdhis += time.getUTCFullYear() + "-";
    ymdhis += (time.getUTCMonth()+1) + "-";
    ymdhis += time.getUTCDate();
    if (isFull === true){
        ymdhis += " " + time.getUTCHours() + ":";
        ymdhis += time.getUTCMinutes() + ":";
        ymdhis += time.getUTCSeconds();
    }
    return ymdhis;
}

/**
 * Created by Administrator on 2017/11/1.
 */
var app = new Vue({
    el:'#app',
    data:{
        phoneNumber:'',
        loginPass:'',
        ok:true,
        oks:false,
        rephoneNumber:'',
        reCode:'',
        rey:'',
        repass:'',
        repasses:'',
        tixpas:'',
        retixpas:'',
        reCrad:'',//注册身份证
        price:1,
        bankPhone:'',//银行卡手机号码
        bankUsername:'',//银行卡真实姓名
        bankAccount:'',//银行卡号
        bankCity:'',//银行所在省市区
        bankName:'',//银行名称
        bankCrad:'',//身份证
        banlDetailedCity:'',//银行详细地址
        bankcode:'',//验证码
        src:'/captcha.html',
        bankId:'',
        bankCount:'',
        zhapophoneNumber:'',//找回密码手机号码
        zhapopass:'',
        rezhapopass:'',
        zhaoCode:'',
        loginpas:'',//原登陆密码
        loginxinpass:'',//新登陆密码
        reloginxinpass:'',//确认新登陆密码
        anquanpass:'',//原安全密码
        anquanxinpass:'',//新安全密码
        reanquanxinpass:'',//确认新安全密码
    },
    methods:{
        gui:function(){
            if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(this.phoneNumber))){
                app.loginPass = '';
                return mui.alert('手机号码格式不正确');
            }
            axios.get('/index/login/Verification', {
                params: {
                    mobile:app.phoneNumber,
                    password:app.loginPass
                }
            })
                .then(function (response) {
                    if(response.data.status == 1){
                        mui.alert(response.data.msg)
                        setTimeout(function(){  window.location.href = response.data.urls;},1000);
                    }
                    if(response.data.status == 2){
                        app.loginPass = '';
                        mui.alert(response.data.msg);
                    }
                })
                .catch(function (error) {
                    console.log(error)
                });
            },
        resubmit:function(){
                if(this.tixpas !== this.retixpas){
                    app.retixpas = '';
                    app.tixpas = '';
                    return mui.alert('两次密码不一致');
                }
                axios.get('/index/register/pas', {
                    params: {
                        password:app.tixpas,
                        repassword:app.retixpas,
                    }
                })
                    .then(function (response) {
                        if(response.data.status == 2){
                            app.retixpas = '';
                            app.tixpas = '';
                            return mui.alert(response.data.msg);
                        }
                        if(response.data.status == 1){
                            window.location.href = '/index/index';
                        }
                    })
                    .catch(function (error) {
                        // console.log(error)
                    });
            },
        Toggle:function(){
                if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(this.rephoneNumber))){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('手机号码格式不正确');
                }
                if(this.reCrad == ''){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('身份证必须填写！')
                }
                if(!( /^[0-9a-zA-Z]{6}$/.test(this.rey))){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('邀请码错误或者不存在！');
                }
                if(this.repass !== this.repasses){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('两次密码不一致');
                }
                axios.get('/index/register/lsd',{
                    params: {
                        mobile:app.rephoneNumber,
                        rey:app.rey,
                        reCode:app.reCode,
                        password:app.repass,
                        repassword:app.repasses,
                        card:app.reCrad
                    }
                })
                    .then(function (response) {
                        // console.log(response);
                        if(response.data.status == 2){
                            app.repasses = '';
                            app.repass = '';
                            return mui.alert(response.data.msg);
                        }
                        if(response.data.status == 1){
                            app.ok=!app.ok;
                        }

                    })
                    .catch(function (error) {
                        // console.log(error)
                    });

            },
        submit_bank:function(){
                if(this.bankUsername == ''){
                    return mui.alert('请输入开户姓名');
                }
                if(!(/^\d{15,21}$/.test(this.bankAccount))){
                    return mui.alert('银行卡格式错误！请核对！')
                }
                if(this.bankName == ''){
                    return mui.alert('开户银行为必须');
                }
				if(this.bankCrad == ''){
                    return mui.alert('身份证为必须！');
                }
                if(this.bankcode == ''){
                    return mui.alert('验证码错误2！');
                }
				$.ajax({
                    type: "post",
                    url: "/index/suey/bankyl",
                    data: {name:app.bankUsername,bank:app.bankName,zhihang:app.banlDetailedCity,bank_user:app.bankAccount,bankcode:app.bankcode,bankId:app.bankId,bankShen:app.bankCrad},
                    dataType: 'json',
                    async: false, //设置为同步操作就可以给全局变量赋值成功
                    success: function (data) {
                        console.log(data);
                                if(data.status == 2){

                                    app.src = '/captcha.html?id='+Math.random();
                                    // alert(app.src);
                                    return mui.alert(data.msg);
                                }
                                if(data.status == 1){
                                    mui.alert(data.msg);
                                    setTimeout(function(){  window.location.href = '/index/suey/bank';},1000);
                                }
                    }
                })
              //  axios.get('/index/suey/bankyl', {
                //    params: {
                  //      name:app.bankUsername,
                    //    bank:app.bankName,
                     //   zhihang:app.banlDetailedCity,
                      //  bank_user:app.bankAccount,
               //         bankcode:app.bankcode,
                //        bankId:app.bankId
                 //   }
                //})
                 //   .then(function (response) {
                        // console.log(response);
                 //       if(response.data.status == 2){
                  //          app.src = '/captcha.html?id='+Math.random();
                            // alert(app.src);
                  //          return mui.alert(response.data.msg);
                  //      }
                   //     if(response.data.status == 1){
                    //        mui.alert(response.data.msg);
                    //        setTimeout(function(){  window.location.href = '/index/suey/bank';},1000);
                    //    }

                   //  })
                   //  .catch(function (error) {
                        // console.log(error)
                   //  });
            },
        zhaom:function(){
                if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(this.zhapophoneNumber))){
                    app.zhapopass = '';
                    app.zhapopass = '';
                    return mui.alert('手机号码格式不正确');
                }
                if(app.rezhapopass !== app.zhapopass){
                    app.zhapopass = '';
                    app.rezhapopass = '';
                    return mui.alert('两次密码不一致');
                }
                axios.get('/index/Register/bankyl', {
                    params: {
                        mobile:app.zhapophoneNumber,
                        password:app.zhapopass,
                        repassword:app.rezhapopass,
                        code:app.zhaoCode
                    }
                })
                    .then(function (response) {
                        console.log(response);
                        if(response.data.status == 2){
                            return mui.alert(response.data.msg);
                        }
                        if(response.data.status == 1){
                            mui.alert(response.data.msg);
                            setTimeout(function(){ window.location.href = '/index/login';},1000);
                        }

                    })
                    .catch(function (error) {
                        // console.log(error)
                    });
            },
        },
})







//倒计时
function time(obj,setTime){

    if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(app.rephoneNumber))){
        return mui.alert('手机号码格式不正确');
    }
    axios.get('/index/register/duanxin', {
        params: {
            mobile:app.rephoneNumber,
        }
    })
        .then(function (response) {
            if(response.data.status==1){
                $(obj).html('120');
                $(obj).css('font-size','15px');
                mui.alert(response.data.msg);
                var time=parseInt($(obj).html());
                $(obj).attr('disabled','disabled');
                setTime=setInterval(function(){
                    if(time<=0){
                        clearInterval(setTime);
                        $(obj).html('重新获取');
                        $(obj).css('font-size','14px');
                        $(obj).removeAttr('disabled');
                        return;
                    }
                    time--;
                    $(obj).html(time);
                },1000);
            }else{
                return mui.alert(response.data.msg);
            }
        })
        .catch(function (error) {
            // console.log(error)
        });
}


//倒计时
function timess(obj,setTime){
    axios.get('/index/suey/htr', {
    })
        .then(function (response) {
            if(response.data.status==1){
                $(obj).html('120');
                $(obj).css('font-size','15px');
                mui.alert(response.data.msg);
                var time=parseInt($(obj).html());
                $(obj).attr('disabled','disabled');
                setTime=setInterval(function(){
                    if(time<=0){
                        clearInterval(setTime);
                        $(obj).html('重新获取');
                        $(obj).css('font-size','14px');
                        $(obj).removeAttr('disabled');
                        return;
                    }
                    time--;
                    $(obj).html(time);
                },1000);
            }else{
                return mui.alert(response.data.msg);
            }
        })
        .catch(function (error) {
            // console.log(error)
        });
}


//倒计时
function times(obj,setTime){

    if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(app.zhapophoneNumber))){
        return mui.alert('手机号码格式不正确');
    }
    axios.get('/index/register/rezhao', {
        params: {
            mobile:app.zhapophoneNumber,
        }
    })
        .then(function (response) {
            if(response.data.status==1){
                $(obj).html('120');
                $(obj).css('font-size','15px');
                mui.alert(response.data.msg);
                var time=parseInt($(obj).html());
                $(obj).attr('disabled','disabled');
                setTime=setInterval(function(){
                    if(time<=0){
                        clearInterval(setTime);
                        $(obj).html('重新获取');
                        $(obj).css('font-size','14px');
                        $(obj).removeAttr('disabled');
                        return;
                    }
                    time--;
                    $(obj).html(time);
                },1000);
            }else{
                return mui.alert(response.data.msg);
            }
        })
        .catch(function (error) {
            // console.log(error)
        });
}


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
        price:1
    },
    methods:{
        gui:function(){
            if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(this.phoneNumber))){
                app.loginPass = '';
                return mui.alert('手机号码格式不正确');
            }
            if(!(/^[a-z\d]{6,12}$/i.test(this.loginPass))){
                app.loginPass = '';
                return mui.alert('密码错误');
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
                if(!(/^[a-z\d]*$/i.test(this.tixpas))){
                    app.retixpas = '';
                    app.tixpas = '';
                    return mui.alert('密码格式为6-12位数字和字母');
                }
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
                            window.location.href = '/index/index/index';
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
                if(!( /^[0-9a-zA-Z]{6}$/.test(this.rey))){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('邀请码错误或者不存在！');
                }
                if(!(/^[a-z\d]*$/i.test(this.repass))){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('密码格式为6-12位数字和字母');
                }
                if(this.repass !== this.repasses){
                    app.repasses = '';
                    app.repass = '';
                    return mui.alert('两次密码不一致');
                }
                axios.get('/index/register/lsd', {
                    params: {
                        mobile:app.rephoneNumber,
                        rey:app.rey,
                        reCode:app.reCode,
                        password:app.repass,
                        repassword:app.repasses
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
                            app.oks = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error)
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
                $(obj).html('60');
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

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


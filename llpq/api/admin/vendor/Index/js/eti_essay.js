//文件原生ajax上传
var xhr;
    function createXMLHttpRequest()
    {
        if(window.ActiveXObject)
        {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else if(window.XMLHttpRequest)
        {
            xhr = new XMLHttpRequest();
        }
    }
    function UpladFile()
    {
        var fileObj = document.getElementById("file").files[0];//获取上传文件
        var FileController = 'http://'+host+'/admin/Info/FileUpload_suo';//上传地址
        var form = new FormData();
        form.append("myfile", fileObj);//设置上传名称
        createXMLHttpRequest();
        xhr.onreadystatechange = handleStateChange;
        xhr.open("post", FileController, true);
        xhr.send(form);
    }
    function handleStateChange()
    {//回调函数
        if(xhr.readyState == 4)
        {
            if (xhr.status == 200 || xhr.status == 0)
            {
                var result = xhr.responseText;            
                var obj = new Function("return" + result)();//转换后的JSON对象 
                console.log(obj);
                document.getElementById("list_im").src =obj;
               document.getElementById("outline_img").value =obj;
               clos1();
            }
        }
    }
// 添加一个插件
CKEDITOR.plugins.add('code', {
  
// 插件初始化
init: function (editor) {
  
    // 添加code按钮
    editor.ui.addButton('code', {
  
        // 鼠标移到按钮提示文字
        label: 'code',
  
        // 命令
        command: 'codel',
  
        // 图标（相对于插件本身目录下）
        icon: this.path + 'images/code.gif',
  
        // 添加点击事件   
        click:function(){
              
            // 获取选取的文本
            var selectText = editor.getSelection().getSelectedText();
  
            // 判断选取的文本是否为空
            // 如果为空就啥都不干
            if( selectText !== ""  ){
  
                // 判断你是要干嘛？
                // 你是要添加code标签呢？
                // 还是你要取消code标签？
                if( editor.getSelection().getStartElement().getName() !== "code" ){
                      
                    // 添加code标签
                    var insertHtml = '<code>' + selectText + '</code>';
                    editor.insertHtml(insertHtml);
                }else{
                    // 取消code标签
                    editor.insertText(selectText);      
                }
            }
  
        }
  
    });
}
  
  
});
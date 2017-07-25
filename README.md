###TP5框架写的一个小项目

完成了文章的基本功能，项目主要重点在对文章(支持markdown)的图片处理。

上传附件在`writable/tmp`文件夹中，在逻辑入库时移动到`writable/update`文件夹中，默认是`原图大小`、`800x600`、`400x300`、`200x150`、`origin`（除`origin`外都加水印：public/watermark.png）；


目录结构
www  WEB部署目录（或者子目录）
├─application           应用目录
├─public                WEB目录（对外访问目录）
├─thinkphp              框架（需要composer insert）
├─config                配置文件
├─extend                扩展类库目录
├─writable              上传附件目录(需要自建文件夹并给0770及以上权限)
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
├─*.conf                nginx配置文件


# thinkphp-study
本项目是 ThinkPHP 3.2.x 的研究案例练习，你可以下载本项目中的代码来了解 ThinkPHP 3.2.x 
中的所有功能。

## 获取 ThinkPHP
ThinkPHP 支持 Composer 安装部署:

    composer create-project topthink/thinkphp your-project-name

不过由于 composer 在中国访问比较缓慢，所以建议去官方网站网站上去下载 thinkphp 
的发布版。喜欢当深入研究的，或者希望参与项目开发的，可以获取 Git 
版本，帮助开发。

    # 参与开源项目，从这里开始 ->
    Github： https://github.com/liu21st/thinkphp
    Oschina： http://git.oschina.net/liu21st/thinkphp.git
    Code： https://code.csdn.net/topthink2011/ThinkPHP

## 环境要求
- PHP 5.3 以上版本（**注意** PHP5.3dev版本和PHP6均不支持）
- Apache、IIS ... 基本上 PHP 可以运行的，都支持

## 目录结构

    www  WEB部署目录（或者子目录）
    ├─index.php       入口文件
    ├─README.md       README文件
    ├─Application     应用目录
    ├─Public          资源文件目录
    └─ThinkPHP        框架目录

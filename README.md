# thinkphp-study
本项目是 ThinkPHP 3.2.x 的研究案例练习，你可以下载本项目中的代码来了解 ThinkPHP 3.2.x 
中的所有功能。

GitHub: https://github.com/dotku/thinkphp-study

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

## 入口文件

是 ThinkPHP 预载前的一些定义部署，我一般是一个 Module 一个入口文件，为入口减压，官方网站上该章节的唯一亮点是:

> 给THINK_PATH和APP_PATH定义绝对路径会提高系统的加载效率。

## 自动生成

这个是我喜欢 ThinkPHP 的一个地方，虽然没有 Leveral 那种提供命令行的创建方式，通过 define 来创建相应的文件目录也是不错，特别是在共享主机的环境下。

    // 自动生成安全目录保护文件 default.html
    define('DIR_SECURE_FILENAME', 'default.html'); 
    
    // 绑定模块，并自动生成相应的控制器文件
    define('BIND_MODULE','Admin');
    define('BUILD_CONTROLLER_LIST','Index,User,Menu'); 
    
    define('APP_PATH','./Application/');
    require './ThinkPHP/ThinkPHP.php';

## 控制器
### 必须
控制器首字母必须大写
### 可以 
- 可以保护数字
- 文件内的 Class 命名可以不匹配字母大小，只要字母对应上就行(eg. /index.php/Home/Camel, /index.php/Home/Camel2)

### 不可以 
后面的字母都不可以大写 (eg. /index.php/Home/Camel)
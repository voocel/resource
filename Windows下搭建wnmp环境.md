# <center>Windows下搭建wnmp环境</center>
### 下载
1.在E盘下新建文件夹wnmp，在wnmp下分别建立php、mysql、www四个文件夹

2.下载nginx直接解压到E盘的wnmp文件夹，并将带有版本号的nginx文件夹重命名为nginx 路径为：E:/wnmp/nginx
下载地址：http://nginx.org/download/nginx-1.13.4.zip

3.下载win版php解压到E盘wnmp的php文件夹内 路径为：E:/wnmp/php
下载地址：http://windows.php.net/downloads/releases/php-7.1.8-nts-Win32-VC14-x86.zip

4.下载win版MySQL解压到wnmp下的mysql文件夹内 路径为：E:/wnmp/mysql
下载地址：https://dev.mysql.com/get/Downloads/MySQL-5.7/mysql-5.7.19-winx64.zip

5.下载RunHiddenConsole.exe的作用是在执行完命令行脚本后可以自动关闭脚本，而从脚本中开启的进程不被关闭。
下载地址：http://redmine.lighttpd.net/attachments/660/RunHiddenConsole.zip

### 安装与配置
6.php的安装与配置
将php文件夹内的php.ini-development文件重命名为php.ini，并打开将.将以下配置一一更改
```
;extension_dir = "./ext"
;extension = php_mysqli.dll
;extension=php_pdo_mysql.dll
;extension=php_openssl.dll
;cgi.fix_pathinfo=1
;date.timezone =
enable_dl = Off
;cgi.force_redirect = 1
;fastcgi.impersonate = 1
;cgi.rfc2616_headers = 0
```
更改为
```
extension_dir = "E:/wnmp/php/ext"
extension = php_mysqli.dll
extension=php_pdo_mysql.dll
extension=php_openssl.dll
cgi.fix_pathinfo=1
date.timezone = Asia/Shanghai
enable_dl = On
cgi.force_redirect = 1
fastcgi.impersonate = 1
cgi.rfc2616_headers = 0
```
7.包装成.bat文件，避免每次重复敲击命令
首先把下载好的RunHiddenConsole.zip包解压到nginx目录内，然后来创建脚本，命名为“start_nginx.bat”，并将其内容编辑为：
```
@echo off
REM Windows 下无效
REM set PHP_FCGI_CHILDREN=5

REM 每个进程处理的最大请求数，或设置为 Windows 环境变量
set PHP_FCGI_MAX_REQUESTS=1000

echo Starting PHP FastCGI...
RunHiddenConsole E:/wnmp/php/php-cgi.exe -b 127.0.0.1:9000 -c E:/wnmp/php/php.ini

echo Starting nginx...
RunHiddenConsole E:/wnmp/nginx/nginx.exe -p E:/wnmp/nginx
```
再另外创建一个名为stop_nginx.bat的脚本用来关闭nginx：

```
@echo off
echo Stopping nginx...
taskkill /F /IM nginx.exe > nul
echo Stopping PHP FastCGI...
taskkill /F /IM php-cgi.exe > nul
exit
```

做好后，是这样的。这样，我们的服务脚本也都创建完毕了。双击start_nginx.bat，这样nginx服务就启动了，而且php也以fastCGI的方式运行了。

8.安装mysql
(1) 手动创建好my.ini文件
```
[mysqld]
character-set-server=utf8
#绑定IPv4和3306端口
bind-address = 0.0.0.0
port = 3306
# 设置mysql的安装目录
basedir=E:/wnmp/mysql
# 设置mysql数据库的数据的存放目录
datadir=E:/wnmp/mysql/data
# 允许最大连接数
max_connections=200
# skip_grant_tables
[mysql]
default-character-set=utf8
[mysql.server]
default-character-set=utf8
[mysql_safe]
default-character-set=utf8
[client]
default-character-set=utf8
```
*将此文件放到mysql文件夹中（必须）*

(2) 使用管理员身份打开命令提示符
切换目录至mysql包所在的bin目录。然后输入 `mysqld.exe -install`
执行命令后，提示：Service successfully installed. 表示安装成功.

(3) 初始化mysql数据，并创建一个具有空密码的root用户，打开cmd执行如下命令：
`mysqld --initialize-insecure --user=mysql`
注意：最后的参数 --user=mysql 在 windows 也可以不用添加，但在 unix 等系统下好像很重要。
执行命令后，等一会后，系统会自动生成相应的 data 目录，并自动创建好空密码的 root 用户。此时表示初始化成功。

(4) 在cmd（命令提示符）中，输入下面的命令，启动mysql服务。
`net start mysql`

(5) 在服务启动后，因为刚创建的 root 用户是空密码的，因此，可以根据需要，进行密码设定。
可执行如下命令：
`mysqladmin -u root -p password 此处输入新的密码`
`Enter password: 此处输入旧的密码`

*请注意：在输入旧密码（或没改过密码的就直接回车）后，系统很久没响应，然后报错（10060）。
原因：mysql没有通过windows防火墙
解决方法：将D：\mysql\bin\mysqld.exe 添加到windows防火墙允许通过的应用中。*

9.nginx配置很简单，想必大家都会，就不写了。可以参考在linux下部署的nginx配置部分(将站点根目录配置到www目录即可)

## windows下安装composer
下载composer.phar放置在php.exe处
新建一个composer.bat文件，在里面写入一下内容(在已经配置php环境变量的前提下)：
```
@ECHO OFF  
 php "%~dp0composer.phar" %*  
```

## Apache 安装
下载地址：https://www.apachelounge.com/download/VC15/binaries/httpd-2.4.27-Win64-VC15.zip

1.解压到wnmp中 路径为：`E:/wnmp/Apache24`

2.使用cmd进入到apache的bin目录下执行
`httpd.exe -k install`
来安装服务

3.编辑配置文件
```
(1)进入conf目录，然后编辑httpd.conf
(2)修改`Define SRVROOT "/Apache24"`为Define SRVROOT "E:/wnmp/Apache24"
(3)修改DirectoryIndex index.html为DirectoryIndex index.php index.html     则可默认支持PHP
(4)将;LoadModule rewrite_module modules/mod_rewrite.so前的分号去掉，开启重写功能
(5)将;Include conf/extra/httpd-vhosts.conf前的分号去掉，开启虚拟主机
(6)添加php模块
LoadModule php7_module E:/wnmp/php/php7apache2_4.dll
<IfModule php7_module> 
    PHPIniDir "E:/wnmp/php/" 
    AddType application/x-httpd-php .php
    AddType application/x-httpd-php-source .phps
</IfModule>
(7)进入extra文件夹，编辑httpd-vhosts.conf
在最后面添加虚拟主机：
<VirtualHost *:80>    # 指定虚拟主机的IP地址和端口号
    DocumentRoot "E:/wnmp/Apache24/htdocs/"   # 指定web文件目录
    ServerName localhost
	DirectoryIndex index.php index.html index.htm
        ErrorLog "logs/error.log"
        CustomLog "logs/access.log" common
	<Directory "E:/wnmp/Apache24/htdocs/">   # 定义目录访问权限
        Options Indexes FollowSymLinks    # 固定格式-若目录下有index则自动打开index文件，如果没有则列出文件列表
        AllowOverride All   # 固定格式
       # Order allow,deny  # 匹配顺序为先允许，后拒绝
       # Allow from all   # 设置允许所有人访问 (经测试这两项配置需要注释掉才能正常启动)
        Require all granted  # 对这个目录给予授权
	</Directory>
</VirtualHost>
```
重启Apache即可解析php文件了 `httpd.exe -k restart`

*注意:PHP需要是TS版的,因为NTS版没有`php7apache2_4.dll`文件*
# 将 Nginx 配置为Web服务器
抽象来说，将 Nginx 配置为 Web 服务器就是定义处理哪些 URLS 和如何处理这些URLS 对应的请求。具体来说，就是定义一些虚拟服务器（Virtual Servers），控制具有特定 IP 和域名的请求。

更具体的来说， Nginx 通过定义一系列 locations 来控制对 URIS 的选择。每一个 location 定义了对映射到自己的请求的处理场景：返回一个文件或者代理请求，或者根据不同的错误代码返回不同的错误页面。另外，根据 URI 的不同，请求也可以被重定向到其它 server 或者 location 。
 
## 设置虚拟服务器

listen：
Nginx 配置文件至少包含一个 server 命令 ，用来定义虚拟服务器。当请求到来时， Nginx 会首先选择一个虚拟服务器来处理该请求。

虚拟服务器定义在 http 上下文中的 server 中：
```nginx
http {
    server {
        # Server configuration
    }
}
```
注意： http 中可以定义多个 server
server 配置块使用 listen 命令监听本机 IP 和端口号（包括 Unix domain socket and path），支持 IPv4、IPv6，IPv6地址需要用方括号括起来：
```nginx
server {
    listen 127.0.0.1:8080;  # IPv4地址，8080端口
    # listen [2001:3CA1:10F:1A:121B:0:0:10]:80;   # IPv6地址，80端口
    # listen [::]:80;  # 听本机的所有IPv4与IPv6地址，80端口
    # The rest of server configuration
}
```
上述配置，如果不写端口号，默认使用80端口，如果不写 IP ，则监听本机所有 IP。

server_name：
如果多个 server 的 listen IP 和端口号一模一样， Nginx 通过请求头中的 Host 与 server_name 定义的主机名进行比较，来选择合适的虚拟服务器处理请求：
```nginx
server {
    listen      80;
    server_name lufficc.com  www.lufficc.com;
    ...
}
```
server_name 的参数可以为：

完整的主机名，如：api.lufficc.com 。
含有通配符（含有 *），如：*.lufficc.com 或 api.* 。
正则表达式，以 ~ 开头。
通配符只能在开头或结尾，而且只能与一个 . 相邻。www.*.example.org 和 w*.example.org 均无效。 但是，可以使用正则表达式匹配这些名称，例如 ~^www\..+\.example\.org$ 和 ~^w.*\.example\.org$ 。 而且 * 可以匹配多个部分。 名称 * .example.org 不仅匹配 www.example.org，还匹配www.sub.example.org。
对于正则表达式：Nginx 使用的正则表达式与 Perl 编程语言（PCRE）使用的正则表达式兼容。 要使用正则表达式，且必须以 ~ 开头。
命名的正则表达式可以捕获变量，然后使用：
```nginx
server {
    server_name   ~^(www\.)?(?<domain>.+)$;

    location / {
        root   /sites/$domain;
    }
}
```
小括号 () 之间匹配的内容，也可以在后面通过 $1 来引用，$2 表示的是前面第二个 () 里的内容。因此上述内容也可写为：
```nginx
server {
    server_name   ~^(www\.)?(.+)$;

    location / {
        root   /sites/$2;
    }
}
```
一个 server_name 示例：
```nginx
server {
    listen      80;
    server_name api.lufficc.com  *.lufficc.com;
    ...
}
```
同样，如果多个名称匹配 Host 头部， Nginx 采用下列顺序选择：

完整的主机名，如 api.lufficc.com。
最长的，且以 * 开头的通配名，如：*.lufficc.com。
最长的，且以 * 结尾的通配名，如：api.* 。
第一个匹配的正则表达式。（按照配置文件中的顺序）
即优先级：api.lufficc.com > *.lufficc.com > api.* > 正则。

如果 Host 头部不匹配任何一个 server_name ,Nginx 将请求路由到默认虚拟服务器。默认虚拟服务器是指：nginx.conf 文件中第一个 server 或者 显式用 default_server 声明：
```nginx
server {
    listen      80 default_server;
    ...
}
```
## 配置 location

### URI 与 location 参数的匹配
当选择好 server 之后，Nginx 会根据 URIs 选择合适的 location 来决定代理请求或者返回文件。

location 指令接受两种类型的参数：

前缀字符串（路径名称）
正则表达式
对于前缀字符串参数， URIs 必须严格的以它开头。例如对于 /some/path/ 参数，可以匹配 /some/path/document.html ，但是不匹配 /my-site/some/path，因为 /my-site/some/path 不以 /some/path/ 开头。
```nginx
location /some/path/ {
    ...
}
```
对于正则表达式，以 ~ 开头表示大小写敏感，以 ~* 开头表示大小写不敏感。注意路径中的 . 要写成 \. 。例如一个匹配以 .html 或者 .htm 结尾的 URI 的 location：
```nginx
location ~ \.html? {
    ...
}
```
正则表达式的优先级大于前缀字符串。如果找到匹配的前缀字符串，仍继续搜索正则表达式，但如果前缀字符串以 ^~ 开头，则不再检查正则表达式。

具体的搜索匹配流程如下：

将 URI 与所有的前缀字符串进行比较。
= 修饰符表明 URI 必须与前缀字符串相等（不是开始，而是相等），如果找到，则搜索停止。
如果找到的最长前缀匹配字符串以 ^~ 开头，则不再搜索正则表达式是否匹配。
存储匹配的最长前缀字符串。
测试对比 URI 与正则表达式。
找到第一个匹配的正则表达式后停止。
如果没有正则表达式匹配，使用 4 存储的前缀字符串对应的 location。
= 修饰符拥有最高的优先级。如网站首页访问频繁，我们可以专门定义一个 location 来减少搜索匹配次数（因为搜索到 = 修饰的匹配的 location 将停止搜索），提高速度：
```nginx
location = / {
    ...
}
```
### 静态文件和代理
location 也定义了如何处理匹配的请求：返回静态文件 或者 交给代理服务器处理。下面的例子中，第一个 location 返回 /data 目录中的静态文件，第二个 location 则将请求传递给 https://lufficc.com 域名的服务器处理：
```nginx
server {
    location /images/ {
        root /data;
    }

    location / {
        proxy_pass https://lufficc.com;
    }
}
```
root 指令定义了静态文件的根目录，并且和 URI 拼接形成最终的本地文件路径。如请求 /images/example.png，则拼接后返回本地服务器文件 /data/images/example.png 。

proxy_pass 指令将请求传递到 URL 指向的代理服务器。让后将来自代理服务器的响应转发给客户端。 在上面的示例中，所有不以 /images / 开头的 URI 的请求都将传递给代理服务器处理。

比如我把 proxy_pass 设置为 https://www.baidu.com/，那么访问 http://search.lufficc.com/ 将得到百度首页一样的响应（页面）（感兴趣的童鞋可以自己试一试搜索功能，和百度没差别呢）：
```nginx
server{
      listen 80;
      server_name search.lufficc.com;
      location / {
              proxy_pass https://www.baidu.com;
      }
}
```
## 使用变量（Variables）

你可以使用变量来使 Nginx 在不同的请求下采用不同的处理方式。变量是在运行时计算的，用作指令的参数。 变量由 $ 开头的符号表示。 变量基于 Nginx 的状态定义信息，例如当前处理的请求的属性。

有很多预定义变量，例如核心的 HTTP 变量，你也可以使用 set，map 和 geo 指令定义自定义变量。 大多数变量在运行时计算，并包含与特定请求相关的信息。 例如，$remote_addr 包含客户端 IP 地址，$uri 保存当前URI值。

一些常用的变量如下：
~~~
变量名称	作用
$uri	请求中的当前URI(不带请求参数)，它可以通过内部重定向，或者使用index指令进行修改，$uri不包含主机名,如 /foo/bar.html。
$arg_name	请求中的的参数名，即“?”后面的arg_name=arg_value形式的arg_name
$hostname	主机名
$args	请求中的参数值
$query_string	同 $args
$request	代表客户端的请求地址
$request_uri	这个变量等于包含一些客户端请求参数的原始URI，它无法修改，不包含主机名，如：/cnphp/test.php?arg=freemouse。
...	...
~~~
一个简单的应用就是从 http 重定向到 https 时带上路径信息：
```nginx
server{
       ...
       return      301 https://lufficc.com$request_uri;
       ...
}
```
## 返回特定状态码

如果你的网站上的一些资源永久移除了，最快最简洁的方法就是使用 return 指令直接返回：
```nginx
location /wrong/url {
    return 404;
}
```
return 的第一个参数是响应代码。可选的第二个参数可以是重定向（对应于代码301，302，303和307）的 URL 或在响应正文中返回的文本。 例如：
```nginx
location /permanently/moved/url {
    return 301 http://www.example.com/moved/here;
}
return 指令可以包含在 location 和 server 上下文中：

server{
      location / {
              return 404;
      }
}
```
或者：
```nginx
server{
      ...
      return 404;
      location / {
          ...            
      }
}
```
## 错误处理

error_page 命令可以配置特定错误码的错误页面，或者重定向到其他的页面。下面的示例将在 404 错误发生时返回 /404.html 页面。

error_page 404 /404.html;
error_page 命令定义了如何处理错误，因此不会直接返回，而 return 确实会立即返回。当代理服务器或者 Nginx 处理时产生相应的错误的代码，均会返回相应的错误页面。

在下面的示例中，当 Nginx 找不到页面时，它将使用代码301替换代码404，并将客户端重定向到 http://example.com/new/path.html 。 此配置很有用，比如当客户端仍尝试用旧的 URI 访问页面时，301代码通知浏览器页面已永久移除，并且需要自动替换为返回的新地址。
```nginx
location /old/path.html {
    error_page 404 =301 http:/example.com/new/path.html;
}
```
## 重写 URIs

rewrite 指令可以多次修改请求的 URI。rewrite 的第一个参数是 URI需要匹配的正则表达式，第二个参数是将要替换的 URI。第三个参数可选，指示是否继续可以重写或者返回重定向代码（301或302）。例如：
```nginx
location /users/ {
    rewrite ^/users/(.*)$ /show?user=$1 break;
}
```
您可以在 server 和 location 上下文中包括多个 rewrite 指令。 Nginx 按照它们发生的顺序一个一个地执行指令。 当选择 server 时，server 中的 rewrite 指令将执行一次。

在 Nginx 处理一组 rewrite 指令之后，它根据新的 URI 选择 location 。 如果所选 location 仍旧包含 rewrite 指令，它们将依次执行。 如果 URI 匹配所有，则在处理完所有定义的 rewrite 指令后，搜索新的 location 。

以下示例将 rewrite 指令与 return 指令结合使用：
```nginx
server {
    ...
    rewrite ^(/download/.*)/media/(.*)\..*$ $1/mp3/$2.mp3 last;
    rewrite ^(/download/.*)/audio/(.*)\..*$ $1/mp3/$2.ra  last;
    return  403;
    ...
}
```
诸如 /download/some/media/file 的 URI 被改为 /download/some/mp3/file.mp3 。 由于 last 标志，后续指令（第二个 rewrite 指令和 return 指令）被跳过，但 Nginx 继续以更改后的 URI 处理请求。 类似地，诸如 /download/some/audio/file 的 URI 被替换为 /download/some/mp3/file.ra。 如果 URI 不匹配 rewrite 指令，Nginx 将403 错误代码返回给客户端。

last 与 break的区别是：

last ： 在当前 server 或 location 上下文中停止执行 rewrite 指令，但是 Nginx 继续搜索与重写的URI匹配的 location，并应用新 location 中的任何 rewrite 指令（这意味着 URI 可能再次改变）。
break ：停止当前上下文中 rewrite 指令的处理，并取消搜索与新 URI 匹配的 location。 不会执行新 location中的 rewrite 指令。

## 附录

### 常用正则
~~~
. ： 匹配除换行符以外的任意字符
? ： 重复0次或1次
+ ： 重复1次或更多次
*： 重复0次或更多次
\d ：匹配数字
^ ： 匹配字符串的开始
$ ： 匹配字符串的结束
{n} ： 重复n次
{n,} ： 重复n次或更多次
[c] ： 匹配单个字符c
[a-z]： 匹配a-z小写字母的任意一个
~~~
### 全局变量
~~~
$args ： #这个变量等于请求行中的参数，同$query_string
$content_length ： 请求头中的Content-length字段。
$content_type ： 请求头中的Content-Type字段。
$document_root ： 当前请求在root指令中指定的值。
$host ： 请求主机头字段，否则为服务器名称。
$http_user_agent ： 客户端agent信息
$http_cookie ： 客户端cookie信息
$limit_rate ： 这个变量可以限制连接速率。
$request_method ： 客户端请求的动作，通常为GET或POST。
$remote_addr ： 客户端的IP地址。
$remote_port ： 客户端的端口。
$remote_user ： 已经经过Auth Basic Module验证的用户名。
$request_filename ： 当前请求的文件路径，由root或alias指令与URI请求生成。
$scheme ： HTTP方法（如http，https）。
$server_protocol ： 请求使用的协议，通常是HTTP/1.0或HTTP/1.1。
$server_addr ： 服务器地址，在完成一次系统调用后可以确定这个值。
$server_name ： 服务器名称。
$server_port ： 请求到达服务器的端口号。
$request_uri ： 包含请求参数的原始URI，不包含主机名，如：/foo/bar.php?arg=baz。
$uri ： 不带请求参数的当前URI，$uri不包含主机名，如/foo/bar.html。
$document_uri ： 与$uri相同。
例如请求：http://localhost:88/test1/test2/test.php
$host：localhost
$server_port：88
$request_uri：/test1/test2/test.php
$document_uri：/test1/test2/test.php
$document_root：/var/www/html
$request_filename：/var/www/html/test1/test2/test.php
~~~

## （总结）Nginx配置文件nginx.conf中文详解
~~~
#定义Nginx运行的用户和用户组
user www www;

#nginx进程数，建议设置为等于CPU总核心数。
worker_processes 8;

#全局错误日志定义类型，[ debug | info | notice | warn | error | crit ]
error_log /var/log/nginx/error.log info;

#进程文件
pid /var/run/nginx.pid;

#一个nginx进程打开的最多文件描述符数目，理论值应该是最多打开文件数（系统的值ulimit -n）与nginx进程数相除，但是nginx分配请求并不均匀，所以建议与ulimit -n的值保持一致。
worker_rlimit_nofile 65535;

#工作模式与连接数上限
events
{
#参考事件模型，use [ kqueue | rtsig | epoll | /dev/poll | select | poll ]; epoll模型是Linux 2.6以上版本内核中的高性能网络I/O模型，如果跑在FreeBSD上面，就用kqueue模型。
use epoll;
#单个进程最大连接数（最大连接数=连接数*进程数）
worker_connections 65535;
}

#设定http服务器
http
{
include mime.types; #文件扩展名与文件类型映射表
default_type application/octet-stream; #默认文件类型
#charset utf-8; #默认编码
server_names_hash_bucket_size 128; #服务器名字的hash表大小
client_header_buffer_size 32k; #上传文件大小限制
large_client_header_buffers 4 64k; #设定请求缓
client_max_body_size 8m; #设定请求缓
sendfile on; #开启高效文件传输模式，sendfile指令指定nginx是否调用sendfile函数来输出文件，对于普通应用设为 on，如果用来进行下载等应用磁盘IO重负载应用，可设置为off，以平衡磁盘与网络I/O处理速度，降低系统的负载。注意：如果图片显示不正常把这个改成off。
autoindex on; #开启目录列表访问，合适下载服务器，默认关闭。
tcp_nopush on; #防止网络阻塞
tcp_nodelay on; #防止网络阻塞
keepalive_timeout 120; #长连接超时时间，单位是秒

#FastCGI相关参数是为了改善网站的性能：减少资源占用，提高访问速度。下面参数看字面意思都能理解。
fastcgi_connect_timeout 300;
fastcgi_send_timeout 300;
fastcgi_read_timeout 300;
fastcgi_buffer_size 64k;
fastcgi_buffers 4 64k;
fastcgi_busy_buffers_size 128k;
fastcgi_temp_file_write_size 128k;

#gzip模块设置
gzip on; #开启gzip压缩输出
gzip_min_length 1k; #最小压缩文件大小
gzip_buffers 4 16k; #压缩缓冲区
gzip_http_version 1.0; #压缩版本（默认1.1，前端如果是squid2.5请使用1.0）
gzip_comp_level 2; #压缩等级
gzip_types text/plain application/x-javascript text/css application/xml;
#压缩类型，默认就已经包含text/html，所以下面就不用再写了，写上去也不会有问题，但是会有一个warn。
gzip_vary on;
#limit_zone crawler $binary_remote_addr 10m; #开启限制IP连接数的时候需要使用

upstream blog.ha97.com {
#upstream的负载均衡，weight是权重，可以根据机器配置定义权重。weigth参数表示权值，权值越高被分配到的几率越大。
server 192.168.80.121:80 weight=3;
server 192.168.80.122:80 weight=2;
server 192.168.80.123:80 weight=3;
}

#虚拟主机的配置
server
{
#监听端口
listen 80;
#域名可以有多个，用空格隔开
server_name www.ha97.com ha97.com;
index index.html index.htm index.php;
root /data/www/ha97;
location ~ .*\.(php|php5)?$
{
fastcgi_pass 127.0.0.1:9000;
fastcgi_index index.php;
include fastcgi.conf;
}
#图片缓存时间设置
location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
{
expires 10d;
}
#JS和CSS缓存时间设置
location ~ .*\.(js|css)?$
{
expires 1h;
}
#日志格式设定
log_format access '$remote_addr - $remote_user [$time_local] "$request" '
'$status $body_bytes_sent "$http_referer" '
'"$http_user_agent" $http_x_forwarded_for';
#定义本虚拟主机的访问日志
access_log /var/log/nginx/ha97access.log access;

#对 "/" 启用反向代理
location / {
proxy_pass http://127.0.0.1:88;
proxy_redirect off;
proxy_set_header X-Real-IP $remote_addr;
#后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
#以下是一些反向代理的配置，可选。
proxy_set_header Host $host;
client_max_body_size 10m; #允许客户端请求的最大单文件字节数
client_body_buffer_size 128k; #缓冲区代理缓冲用户端请求的最大字节数，
proxy_connect_timeout 90; #nginx跟后端服务器连接超时时间(代理连接超时)
proxy_send_timeout 90; #后端服务器数据回传时间(代理发送超时)
proxy_read_timeout 90; #连接成功后，后端服务器响应时间(代理接收超时)
proxy_buffer_size 4k; #设置代理服务器（nginx）保存用户头信息的缓冲区大小
proxy_buffers 4 32k; #proxy_buffers缓冲区，网页平均在32k以下的设置
proxy_busy_buffers_size 64k; #高负荷下缓冲大小（proxy_buffers*2）
proxy_temp_file_write_size 64k;
#设定缓存文件夹大小，大于这个值，将从upstream服务器传
}

#设定查看Nginx状态的地址
location /NginxStatus {
stub_status on;
access_log on;
auth_basic "NginxStatus";
auth_basic_user_file conf/htpasswd;
#htpasswd文件的内容可以用apache提供的htpasswd工具来产生。
}

#本地动静分离反向代理配置
#所有jsp的页面均交由tomcat或resin处理
location ~ .(jsp|jspx|do)?$ {
proxy_set_header Host $host;
proxy_set_header X-Real-IP $remote_addr;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_pass http://127.0.0.1:8080;
}
#所有静态文件由nginx直接读取不经过tomcat或resin
location ~ .*.(htm|html|gif|jpg|jpeg|png|bmp|swf|ioc|rar|zip|txt|flv|mid|doc|ppt|pdf|xls|mp3|wma)$
{ expires 15d; }
location ~ .*.(js|css)?$
{ expires 1h; }
}
}
~~~
## 参考

https://www.nginx.com/resources/admin-guide/nginx-web-server/
http://seanlook.com/2015/05/17/nginx-location-rewrite/
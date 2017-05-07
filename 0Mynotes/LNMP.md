## 一、更新系统软件
```
    yum update
```
## 二、安装编译工具:
```
    yum install gcc automake autoconf libtool gcc-c++
```
## 三、安装nginx
### 方法一：

##### 1. 安装nginx源：
```
       yum localinstall http://nginx.org/packages/centos/7/noarch/RPMS/nginx-release-centos-7-0.el7.ngx.noarch.rpm
```
##### 2. 安装nginx：
```
       yum install nginx
```
##### 3. 启动nginx：
```
       service nginx start
       成功显示:Redirecting to /bin/systemctl start nginx.service
       查看状态：systemctl status nginx   或者 service nginx status
```
##### 4. 访问http://你的ip/
       如果成功安装会出来nginx默认的欢迎界面
      括号内为设置开机自启
     （systemctl enable nginx
ln -s '/usr/lib/systemd/system/nginx.service' '/etc/systemd/system/multi-user.target.wants/nginx.service'）

### 方法二：
     
##### 1、安装编译软件
```
     yum -y install gcc gcc-c++ wget
```
##### 2、安装依赖
```
     yum -y install gcc wget automake autoconf libtool libxml2-devel libxslt-devel perl-devel perl-ExtUtils-Embed         pcre-devel openssl-devel
```
##### 3、进入目录
```
     cd /usr/local/src/
```
##### 4、下载源码
```
     wget http://nginx.org/download/nginx-1.12.0.tar.gz
```
##### 5、解压
```
     tar zxvf nginx-1.12.0.tar.gz
```
##### 6、进入目录
```
     cd nginx-1.12.0/
```
##### 7、创建缓存目录
```
     mkdir -p /var/cache/nginx
```
##### 8、configure
```
     ./configure \
--prefix=/usr/local/nginx \
--sbin-path=/usr/sbin/nginx \
--conf-path=/etc/nginx/nginx.conf \
--error-log-path=/var/log/nginx/error.log \
--http-log-path=/var/log/nginx/access.log \
--pid-path=/var/run/nginx.pid \
--lock-path=/var/run/nginx.lock \
--http-client-body-temp-path=/var/cache/nginx/client_temp \
--http-proxy-temp-path=/var/cache/nginx/proxy_temp \
--http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp \
--http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp \
--http-scgi-temp-path=/var/cache/nginx/scgi_temp \
--user=nobody \
--group=nobody \
--with-pcre \
--with-http_v2_module \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_flv_module \
--with-http_mp4_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-http_v2_module \
--with-threads \
--with-stream \
--with-stream_ssl_module
```
#####  9、编译安装
     ```
     make && make install
     ```
#####  10、然后配置服务：vim /usr/lib/systemd/system/nginx.service
```
         [Unit]
         Description=nginx - high performance web server 
         Documentation=http://nginx.org/en/docs/
         After=network.target remote-fs.target nss-lookup.target

         [Service]
         Type=forking
         PIDFile=/var/run/nginx.pid
         ExecStartPre=/usr/sbin/nginx -t -c /etc/nginx/nginx.conf
         ExecStart=/usr/sbin/nginx -c /etc/nginx/nginx.conf
         ExecReload=/bin/kill -s HUP $MAINPID
         ExecStop=/bin/kill -s QUIT $MAINPID
         PrivateTmp=true

         [Install]
         WantedBy=multi-user.target
```
##### 11、开启开机启动：
```
     systemctl enable nginx.service
```
##### 12、启动：
```
     systemctl start nginx.service
```
       
       pkill -9 nginx（强制关闭Nginx）firewall-cmd --zone=public --add-port=80/tcp --permanent（开放80端口）
       systemctl restart firewalld.service（重启防火墙）
       ps aux |grep nginx(查看nginx进程)  netstat -tunpl |grep :80（查看80端口）

## 四、安装MySQL5.7.*
#### 1. 安装mysql源：
```
       yum localinstall  http://dev.mysql.com/get/mysql57-community-release-el7-7.noarch.rpm
```
#### 2. 安装mysql：
```
       yum -y install mysql-community-server
```
       确认一下mysql的版本，有时可能会提示mysql5.6
#### 3. 安装mysql的开发包，以后会有用
```
       yum -y install mysql-community-devel
```
#### 4. 启动mysql
```
       service mysqld start
```
       成功返回Redirecting to /bin/systemctl start mysqld.service
#### 5. 查看mysql启动状态
```
       service mysqld status
```
       出现pid证明启动成功
#### 6. 获取mysql默认生成的密码
```
       grep 'temporary password' /var/log/mysqld.log
       2017-02-10T14:59:42.328736Z 1 [Note] A temporary password is generated for root@localhost: s/giN9Vo>L9h 
```
       冒号后面的都是密码(没有空格) 复制就好
#### 7. 换成自己的密码
```
       mysql -uroot -p 
       Enter password:输入上面复制的密码
```
#### 8. 更换密码：
```
       mysql> ALTER USER 'root'@'localhost' IDENTIFIED BY 'MyNewPass4!';
```
       这个密码一定要足够复杂，不然会不让你改，提示密码不合法（ERROR 1819 (HY000): Your password does not satisfy the current policy requirements）
       如果不想设置那么复杂的密码可以通过修改validate_password_policy参数的值
```
       mysql> set global validate_password_policy=0;
```
       这样，判断密码的标准就基于密码的长度了。这个由validate_password_length参数来决定。
#### 9. 退出mysql：
```
       mysql> quit;
```
#### 10. 用新密码再登录，试一下新密码
```
        mysql -uroot -p 
        Enter password:输入你的新密码
```
#### 11. 确认密码正确后，退出mysql;
```
        mysql> quit;
```
## 五、编译安装php7.0.0 (创建用户作为用于启动nginx进程的用户 groupadd nginx    useradd -r -g nginx nginx)

#### 1. 下载php7源码包
```
       cd /root & wget -O php7.tar.gz http://cn2.php.net/get/php-7.0.1.tar.gz/from/this/mirror
```
#### 2. 解压源码包
       源码包的位置可以使用 find / -name php7 查找 并进入所在文件夹
```
       tar -xvf php7.tar.gz
```
#### 3. 打开解压好的包
```
       cd php-7.0.1
```
#### 4. 安装php依赖包：（若yum源没有libmcrypt则：yum -y install epel-release  //扩展包更新包yum  update //更新yum源
```
                        yum -y install libmcrypt libmcrypt-devel mcrypt mhash  就ok了）
       yum -y install libxml2 libxml2-devel openssl openssl-devel bzip2 bzip2-devel libcurl libcurl-devel 
                   libjpeg libjpeg-devel libpng libpng-devel freetype freetype-devel gmp gmp-devel libmcrypt 
                   libmcrypt-devel readline readline-devel libxslt libxslt-devel
```


#### 5. 编译配置:(如果以前编译安装过php可通过php -i | grep configure | sed -e "s/Configure Command =>  //; s/'//g")获得
```
       ./configure \
--prefix=/usr/local/php \
--with-config-file-path=/etc \
--enable-fpm \
--with-fpm-user=nginx  \
--with-fpm-group=nginx \
--enable-inline-optimization \
--disable-debug \
--disable-rpath \
--enable-shared  \
--enable-pcntl \
--enable-soap \
--with-libxml-dir \
--with-xmlrpc \
--with-openssl \
--with-mcrypt \
--with-mhash \
--with-pcre-regex \
--with-sqlite3 \
--with-zlib \
--enable-bcmath \
--with-iconv \
--with-bz2 \
--enable-calendar \
--with-curl \
--with-cdb \
--enable-dom \
--enable-exif \
--enable-fileinfo \
--enable-filter \
--with-pcre-dir \
--enable-ftp \
--with-gd \
--with-openssl-dir \
--with-jpeg-dir \
--with-png-dir \
--with-zlib-dir  \
--with-freetype-dir \
--enable-gd-native-ttf \
--enable-gd-jis-conv \
--with-gettext \
--with-gmp \
--with-mhash \
--enable-json \
--enable-mbstring \
--enable-mbregex \
--enable-mbregex-backtrack \
--with-libmbfl \
--with-onig \
--enable-pdo \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-zlib-dir \
--with-pdo-sqlite \
--with-readline \
--enable-session \
--enable-shmop \
--enable-simplexml \
--enable-sockets  \
--enable-sysvmsg \
--enable-sysvsem \
--enable-sysvshm \
--enable-wddx \
--with-libxml-dir \
--with-xsl \
--enable-zip \
--enable-mysqlnd-compression-support \
--with-pear \
--enable-opcache
```
#### 6.正式安装：
```
      make && make install
```
#### 7.配置环境变量：
```
      vi /etc/profile 
```      
      在末尾追加(全局)
```
      PATH=$PATH:/usr/local/php/bin
export PATH
```
      执行命令使得改动立即生效
```
      source /etc/profile
```
#### 8.配置php-fpm
```
      cp php.ini-production /etc/php.ini
      cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
      cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf
      cp sapi/fpm/init.d.php-fpm /etc/init.d/php-fpm
      cp sapi/fpm/php-fpm.service /usr/lib/systemd/system/    (配置 php-fpm 启动服务脚本,使用service启动，编辑本文件                                                      若有${prefix}，则用具体安装地址替换，再重启systemctl daemon-reload)
      chmod +x /etc/init.d/php-fpm
```
#### 9.启动php-fpm
```
      /etc/init.d/php-fpm start
      括号内为设置开机自启
      (systemctl enable php-fpm
ln -s '/usr/lib/systemd/system/php-fpm.service' '/etc/systemd/system/multi-user.target.wants/php-fpm.service')

```
## composer安装：
```
     //下载安装脚本composer-setup.php到当前目录
       php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');"
     //执行安装过程。
       php composer-setup.php
     //删除安装脚本
       php -r "unlink('composer-setup.php');"
     //更换Packagist中国全量镜像，修改 composer 的全局配置文件
```
# <center>Laravel 网站优化</center>
## 关闭debug
打开.env文件，把debug设置为false
```
APP_ENV=local
APP_DEBUG=false
APP_KEY=base64:6ouIfKdFXfaIGZrH9qBCKAWupg4kVwuRsRGpeQnCRh4=
```

## 缓存路由和配置
```
php artisan route:cache

php artisan config:cache
```

## composer优化
```
sudo composer dump-autoload --optimize
```

## Laravel优化命令
```
php artisan optimize
```

## 使用Laravel缓存
使用Laravel的Cache方法缓存内容，有文件缓存，数据库缓存，redis缓存。
```php
$lists = Cache::remember('travel.destination.lists', 20, function () {
    return $this->destination->getList();
});
```

## 使用CDN
如七牛、网易、百度、阿里等CDN，不过收费

## 使用PHP7并开启OPcache
```
apt-get install php70-php-opcache.x86_64
```
然后使用service php70-php-fpm restart命令重启php-fpm。

注：不同的系统和环境根据自己的情况安装和开启opache,通过phpinfo()查看是否安装

## nginx开启gzip压缩
>在服务器Nginx开启gzip压缩是优化网站性能的方法之一，可以有效减少服务器带宽的消耗，缺点是会增大CPU的占用率，但是很多时候CPU往往是空闲最多的。

**在Nginx开启gzip压缩**

打开nginx.conf文件，添加如下：
```
gzip on;
    gzip_min_length 1k;
    gzip_buffers 16 64k;
    gzip_http_version 1.1;
    gzip_comp_level 9;
    gzip_types text/plain application/x-javascript application/javascript text/css application/xml text/javascript application/x-httpd-php image/jpeg image/gif image/png font/ttf font/otf image/svg+xml;
    gzip_vary on;
```
**gzip参数的一些介绍**

`GZIP ON|OFF`

开启或者关闭gzip模块

``GZIP_MIN_LENGTH 1000``

设置允许压缩的页面最小字节数，页面字节数从header头中的Content-Length中进行获取。默认值是0，不管页面多大都压缩。建议设置成大于1k的字节数，小于1k可能会越压越大。 即: gzip_min_length 1024

```GZIP_PROXIED EXPIRED NO-CACHE NO-STORE PRIVATE AUTH;```

Nginx作为反向代理的时候启用，开启或者关闭后端服务器返回的结果，匹配的前提是后端服务器必须要返回包含”Via”的 header头。

`GZIP_TYPES TEXT/PLAIN APPLICATION/XML;`

匹配MIME类型进行压缩，（无论是否指定）”text/html”类型总是会被压缩的。

**通过浏览器判断是否开启gzip压缩**
查看控制台http响应 Content-Encoding字段是gzip，表示该网页是经过gzip压缩的
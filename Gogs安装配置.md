## 安装nginx
`sudo yum -y install nginx`

## 安装git
`sudo yum -y install git`

## 安装MySQL
`sudo yum -y install mysql-server`

## 创建gogs数据库
```
set global storage_engine = 'InnoDB';
create database gogs default character set utf8mb4 collate utf8mb4_unicode_ci;
create user 'gogs'@'localhost' identified by '你的密码';
grant all privileges on gogs.* to 'gogs'@'localhost';
flush privileges;
exit;
```

## 为gogs创建用户
`sudo adduser git`

## 下载并解压gogs
```
su git
cd ~
wget https://dl.gogs.io/0.11.34/linux_amd64.tar.gz
sudo tar zxvf linux_amd64.tar.gz -C /usr/local
```
## 自定义配置文件
Gogs的配置要自己创建文件，在Gogs根目录下创建自定义配置文件（文件目录以及名称是固定的）:

`mkdir -p custom/conf`

`vim custom/conf/app.ini`

```
APP_NAME = Go Git Service
RUN_USER = git
RUN_MODE = prod

[database]
DB_TYPE  = mysql
HOST     = 127.0.0.1:3306
NAME     = gogs
USER     = gogs
PASSWD   = gogs
SSL_MODE = disable
PATH     = data/gogs.db

[repository]
ROOT = /home/git/gogs-repositories

[server]
DOMAIN       = 127.0.0.1
HTTP_PORT    = 3000
ROOT_URL     = http://127.0.0.1:3000/
DISABLE_SSH  = false
SSH_PORT     = 22
OFFLINE_MODE = false

[mailer]
ENABLED = false

[service]
REGISTER_EMAIL_CONFIRM = false
ENABLE_NOTIFY_MAIL     = false
DISABLE_REGISTRATION   = false
ENABLE_CAPTCHA         = true
REQUIRE_SIGNIN_VIEW    = false

[picture]
DISABLE_GRAVATAR = false

[session]
PROVIDER = file

[log]
MODE      = file
LEVEL     = Info
ROOT_PATH = /usr/local/gogs/log

[security]
INSTALL_LOCK = true
SECRET_KEY   = i4B7R55aRaFdw8j
```

### 启动gogs
/usr/local/gogs/gogs web

### 后台启动方案一
`nohup /usr/local/gogs/gogs web &`

`tail -f nohup.out`

然后访问http://服务器IP:3000/install

### 后台运行方案二 （加入系统服务）
```
复制并修改脚本文件
#sudo vim /usr/local/gogs/scripts/init/centos/gogs(修改对应的地址和用户)
#sudo cp /usr/local/gogs/scripts/init/centos/gogs /etc/init.d/
#sudo chmod +x /etc/init.d/gogs

sudo cp /usr/local/gogs/scripts/systemd/gogs.service /etc/systemd/system/
sudo vim /etc/systemd/system/gogs.service  (修改对应的地址和用户)

sudo systemctl daemon-reload
sudo service gogs start（返回ok，但查看status还是失败状态且无法访问，则还是因为地址用户修改不正确）
sudo chkconfig --add gogs  (开机自启)
```
## nginx 反向代理
```
server {
    server_name 二级域名或IP;
    listen 80; # 或者 443，如果你使用 HTTPS 的话
    # ssl on; 是否启用加密连接
    # 如果你使用 HTTPS，还需要填写 ssl_certificate 和 ssl_certificate_key

    location / { # 如果你希望通过子路径访问，此处修改为子路径，注意以 / 开头并以 / 结束
        proxy_pass http://127.0.0.1:3000/;
    }
}
```
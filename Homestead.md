## 基本介绍
> Homestead 可以运行在 Windows 、 Mac 或 Linux 系统上，并且里面包含了 Nginx Web 服务器、 PHP 7.1 、 MySQL 、 Postgres 、 Redis 、 Memcached 、 Node等以及所有利于你开发 laravel 应用的其他程序。

## 内置软件
+ Ubuntu 16.04
+ Git
+ PHP 7.1、7.0、5.6
+ Nginx
+ MySQL
+ MariaDB
+ Sqlite3
+ Postgres
+ Composer
+ Node (With Yarn, Bower, Grunt, and Gulp)
+ Redis
+ Memcached
+ Beanstalkd
+ Mailhog
+ ngrok

## 安装与配置(windows用户自行安装git方便操作以下命令)
### 安装vagrant
[vagrant-win](https://releases.hashicorp.com/vagrant/1.9.7/vagrant_1.9.7_x86_64.msi?_ga=2.103239433.86842748.1500269595-159348511.1499308757)
[vagrant-linux](https://releases.hashicorp.com/vagrant/1.9.7/vagrant_1.9.7_x86_64.rpm?_ga=2.103239433.86842748.1500269595-159348511.1499308757)
### 安装Virtualbox
[virtualbox-win](http://download.virtualbox.org/virtualbox/5.1.22/VirtualBox-5.1.22-115126-Win.exe)
[virtualbox-linux](https://www.virtualbox.org/wiki/Linux_Downloads)
### 导入box
```
vagrant box add laravel/homestead
```
由于众所周知的原因,上述方法可能需要下载个几天，所以推荐直接用迅雷下载virtualbox版的box文件

- [box文件下载](https://atlas.hashicorp.com/laravel/boxes/homestead/versions/2.2.0/providers/virtualbox.box)

接下来我新建了一个文件夹名为homestead，然后我将下好的box重命名为homestead.box放入，然后在此文件夹内运行如下命令
```
vagrant box add laravel/homestead E:\homestead\homestead.box
vagrant box list
```
会反馈一条信息：laravel/homestead (virtualbox, 0)  这说明box已经添加进来了

### 下载官方homestead配置
```
git clone https://github.com/laravel/homestead.git Homestead
```
克隆的文件夹内有init.sh和init.bat文件，运行如下命令进行初始化生成homestead.yml配置文件
```
bash init.sh
```
或者直接双击init.bat

### 配置Homestead.yaml
##### 设置 IP及Provider
Homestead.yaml文件中的provider键表示使用哪个 Vagrant 提供者：virtualbox、vmware_fushion或者vmware_workstation
```
   ip: "192.168.10.10"
   provider: virtualbox
```
##### 配置共享文件夹
Homestead.yaml文件中的folders属性列出了所有主机和 Homestead 虚拟机共享的文件夹，一旦这些目录中的文件有了修改，将会在本地和   Homestead 虚拟机之间保持同步，如果有需要的话，你可以配置多个共享文件夹（一般一个就够了），如果要开启 NFS，只需简单添加一个标识到同步文件夹配置
```
folders:
    - map: D:/homestead/code  #（这是我本地的文件夹）
      to: /home/vagrant/Code
      type: "nfs"
```

##### 配置 Nginx 站点
通过sites属性可以方便地将域名映射到 Homestead 虚拟机的指定目录，Homestead.yaml中默认已经配置了一个示 例站点。和共享文件夹一样，可以配置多个站点：
```
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
    - map: yii2.app
      to: /home/vagrant/Code/yii2/basic/web
```

##### 修改Hosts文件
把 Nginx 站点配置中的域名添加到本地机器上的hosts文件中，该文件会将对本地域名的请求重定向到 Homestead 虚拟机
```
192.168.10.10  homestead.app
192.168.10.10  www.yii2.com
```

### 修改homestead.rb文件
如果这时候你直接在Homestead目录下启动homestead虚拟机，肯定会得到反复叫你下载virtualbox的提示，猜测这是由于手动添加的virtualbox没有保存版本信息的缘故(可以使用命令vagrant box list来查看)。所以可以通过修改Homestead/scripts/homestead.rb来解决这一个问题，找到

config.vm.box_version = settings["version"] ||= ">= 2.0.0"这一行，将其修改为config.vm.box_version = settings["version"] ||= ">= 0"即可

### 启动虚拟机
进入Homestead目录(git clone下来里面的那个目录)，使用命令vagrant up命令启动虚拟机，可使用vagrant ssh登陆虚拟机。顺便一提，虚拟机数据库的root用户密码为secret，远程连接是用户homestead 密码 secret
> 若提示ssh的key没有生成则执行：ssh-keygen
>
> 更换composer中国镜像：composer config -g repo.packagist composer https://packagist.phpcomposer.com
>
>若访问网站出现文件未找到（No input file specified.）则需要执行：vagrant provision


## 命令操作
- Homestead 目录下

开机： vagrant up
关机： vagrant halt
更改homestead.yml后使用：vagrant provision
盒子列表：vagrant box list
重新加载配置：vagrant --reload
从vagrant中去除添加的盒子：vagrant box  remove '盒子名称'
销毁虚拟机： vagrant destroy --force
登录: vagrant ssh

- 访问网站

绑定hosts 192.168.10.10 homestead.app
访问 http://homestead.app

- SSH登录

通过ssh登录 ssh vagrant@127.0.0.1 -p 2222 或者创建别名 alias vm="ssh vagrant@127.0.0.1 -p 2222"，使用vm登录
通过在Homestead 目录使用 vagrant ssh 命令

- 连接虚拟机内Mysql

mysql -h 127.0.0.1:33060 -u homestead -p secret

- 端口映射

SSH: 2222 → Forwards To 22
HTTP: 8000 → Forwards To 80
HTTPS: 44300 → Forwards To 443
MySQL: 33060 → Forwards To 3306
Postgres: 54320 → Forwards To 5432

- 增加额外端口

ports:
    - send: 93000
      to: 9300
    - send: 7777
      to: 777
      protocol: udp

- 增加站点

方式一 Homestead.yaml 文件中增加站点
Homestead 目录中执行 vagrant provision
会破坏以后数据库

方式二 Homestead环境中的 serve 命令
SSH 进入 Homestead 环境中
执行下列命令serve domain.app /home/vagrant/Code/path/to/public/directory 80


## License

[MIT](http://opensource.org/licenses/MIT)

Copyright (c) 2016-present, Voocel

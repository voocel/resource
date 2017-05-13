# Docker安装
### 1、使用下面的shell命令安装Docker
```
  $ curl -sSL https://get.docker.com/ | sh
  或者 yum -y install docker 
```
### 2、启动docker服务
```
  service docker start
```
### 3、使用Docker创建一个nginx的容器：(用国内网易云镜像，提高下载速度)
```
  docker run -d --name=web -p 80:80 hub.c.163.com/library/nginx:latest
  (创建一个名称为web的容器，并把容器内部的80端口与宿主机上的80端口做映射，使得通过宿主机80端口的流量转发到容器内部的80端口  上。其中 -d 为守护进程在后台启动，-p 端口映射)
```
### 4、docker ps 列出正在运行的容器，现在访问宿主机地址的80端口，看到nginx的欢迎页面。
```   
   docker images 能看到目前的镜像
```
### 5、进入刚才创建的容器内部
```
   docker exec -i -t web bash
   退出：exit
   (-t:在新容器内指定一个伪终端或终端。-i:允许你对容器内的标准输入 (STDIN) 进行交互。)
```
### 6、查看容器的更多详细信息
```
   docker inspect web
   在networks部分可以看到容器的网络环境信息：
   可以看到这个容器的IP地址为172.17.0.2，网关地址为172.17.0.1。
```
### 7、宿主和容器间的通信
```
   docker在启动的时候会在宿主机上创建一块名为docker0的网卡，可以用ifconfig查看ip地址也是172.17.0.1.
   docker容器就是通过这张名为docker0的网卡进行通信
   
      真正实现端口转发的魔法的是nat规则。如果容器使用-p指定映射的端口时，docker会通过iptables创建一条nat规则，把宿主机打到   映射端口的数据包通过转发到docker0的网关，docker0再通过广播找到对应ip的目标容器，把数据包转发到容器的端口上。反过来，如   果docker要跟外部的网络进行通信，也是通过docker0和iptables的nat进行转发，再由宿主机的物理网卡进行处理，使得外部可以不知   道容器的存在。

   使用iptables -t nat命令可以看到添加的nat规则：
   可以观察到流量转发到了172.17.0.2的80端口上，这个地址就是刚才创建容器使用的IP地址。

   提示：容器具有隔离性优势，更好地控制环境和版本的隔离
```
### 8、下面新建一个应用ping，作用是统计该应用的访问次数，每次访问页面将次数累加1，返回响应次数给前端页面，并把访问次数存到数据库中。
```
   使用redis作为ping的数据库，与之前类似，拉取redis的镜像，运行容器。
   docker pull redis
   docker run -d --name=redis -p 6379 redis
```



# 注意：
```
    启动docker web服务时 虚拟机端口转发 外部无法访问（WARNING: IPv4 forwarding is disabled. Networking will not work.）
    或者宿主机可以访问，但外部无法访问
    # vi /etc/sysctl.conf
   或者
    # vi /usr/lib/sysctl.d/00-system.conf
   添加如下代码：
    net.ipv4.ip_forward=1
```
### 重启network服务
```
   # systemctl restart network
```
### 查看是否修改成功
```
   # sysctl net.ipv4.ip_forward

   如果返回为“net.ipv4.ip_forward = 1”则表示成功了
```
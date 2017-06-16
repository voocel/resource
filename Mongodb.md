### 1、下载
    wget https://fastdl.mongodb.org/linux/mongodb-linux-x86_64-amazon-3.4.5.tgz
### 2、解压
    tar -zxvf mongodb-linux-x86_64-amazon-3.4.5.tgz
### 3、将解压包拷贝到指定目录                               
    mv  mongodb-linux-x86_64-amazon-3.4.5 /usr/local/mongodb
### 4、配置环境变量
    export PATH=/usr/local/mongodb/bin:$PATH
    或者：PATH=$PATH:/usr/local/mongodb/bin export PATH
          source /etc/profile


#### 5、创建目录
*MongoDB的数据存储在data目录的db目录下，但是这个目录在安装过程不会自动创建，所以你需要手动创建data目录，并在data目录中创建db目录。
以下实例中我们将data目录创建于目录(/usr/local/mongodb)。
注意：/data/db 是 MongoDB 默认的启动的数据库路径(--dbpath)。*

    mkdir -p /usr/local/mongodb/data/db
    mkdir -p /usr/local/mongodb/log
    touch /usr/local/mongodb/log/mongo.log

### 6、启动mongodb服务
```
   编写配置文件
   vim /usr/local/mongodb/bin/mongo.conf
   写入以下内容：
       ##数据文件
       dbpath=/usr/local/mongodb/data/db
       ##日志文件
       logpath=/usr/local/mongodb/log/mongo.log
       #错误日志采用追加模式，配置这个选项后mongodb的日志会追加到现有的日志文件，而不是从新创建一个新文件
       logappend=true    
       #启用日志文件，默认启用
       journal=true    
       #这个选项可以过滤掉一些无用的日志信息，若需要调试使用请设置为false
       quiet=false   
       #端口号 默认为27017，注意这里端口若是修改后，要用mongo --port=xxxx连接，否则报错。
       port=27017 
       #声明这是一个集群的分片，默认端口27017
       #shardsvr=true  
       #设置每个数据库将被保存在一个单独的目录
       #directoryperdb=true
       #PID File的完整路径，如果没有设置则没有PID文件
       #pidfilepath=/usr/local/mongodb/mongo.pid
       #关闭http接口，默认关闭27017端口访问
       #nohttpinterface=true
       #开启认证
       #auth=true
       #设置开启简单的rest API,然后打开27017+1000网页端口 MongoDB 提供了简单的 HTTP 用户界面
       rest=true

  启动：mongod --config /usr/local/mongodb/bin/mongo.conf --fork --port 27017  (推荐)

  也可简单启动：
  mongod --dbpath=/usr/local/mongodb/data/db --logpath=/usr/local/mongodb/log/db.log --fork --port 27017   以守护进程的方式启动
```

### 7. 把MongoDB设置为系统服务并且设置开机启动
```
vim /etc/rc.d/init.d/mongod
写入一下内容：
ulimit -SHn 655350
#!/bin/sh
# chkconfig: - 64 36
# description:mongod
case $1 in
start)
/usr/local/mongodb/bin/mongod --config /usr/local/mongodb/bin/mongo.conf --fork
;;
    
stop)
/usr/local/mongodb/bin/mongo 127.0.0.1:27017/admin --eval "db.shutdownServer()"
;;
    
status)
/usr/local/mongodb/bin/mongo 127.0.0.1:27017/admin --eval "db.stats()"
;;
esac


chmod +x /etc/rc.d/init.d/mongod
(成功后可通过service mongod start 启动)
```
### 8、启动客户端
   mongo     #进入交互式mongoDB后台后

   MongoDB 提供了简单的 HTTP 用户界面。需要在启动的时候指定参数 --rest或在配置文件mongo.conf中设置rest=true 。
   MongoDB 的 Web 界面访问端口比服务的端口多1000。即访问localhost:28017

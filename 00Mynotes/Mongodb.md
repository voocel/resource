# Mongoddb安装
### 1、下载
```
   curl -O https://fastdl.mongodb.org/linux/mongodb-linux-x86_64-3.0.6.tgz
```
### 2、解压
```
   tar -zxvf mongodb-linux-x86_64-3.0.6.tgz                                   
```
### 3、将解压包拷贝到指定目录
```
   mv  mongodb-linux-x86_64-3.0.6/ /usr/local/mongodb
```
### 4、配置环境变量
```
   export PATH=<mongodb-install-directory>/bin:$PATH
   <mongodb-install-directory> 为你 MongoDB 的安装路径。如本文的 /usr/local/mongodb 。
```
### 5、MongoDB的数据存储在data目录的db目录下，但是这个目录在安装过程不会自动创建，所以你需要手动创建data目录，并在data目录中创   建db目录。
```
   以下实例中我们将data目录创建于根目录下(/)。
   注意：/data/db 是 MongoDB 默认的启动的数据库路径(--dbpath)。
   mkdir -p /data/db
```
### 6、直接启动
```
   ./mongod
```
### 7、以守护进程的方式启动
```
   mongod --dbpath=/data/db --logpath=/data/db/db.log --fork --port 27017   
```
### 8、进入交互式mongoDB后台后
```
   $ cd /usr/local/mongodb/bin
   $ ./mongo                         
```
### 9、MongoDB 提供了简单的 HTTP 用户界面。需要在启动的时候指定参数 --rest 。
```
   $ ./mongod --dbpath=/data/db --rest
   MongoDB 的 Web 界面访问端口比服务的端口多1000。即访问localhost:28017
   mongod --dbpath=/data/db --logpath=/data/db/db.log --fork --port 27017 --rest   守护进程的方式
```
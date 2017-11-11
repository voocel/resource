# 匿名访问
1. sudo yum install -y samba samba-client samba-common

2. sudo mv /etc/samba/smb.conf /etc/samba/smb.conf.bak

3. sudo vim /etc/samba/smb.conf

4. 
```
[global]
workgroup = WORKGROUP
server string = Samba Server %v
netbios name = centos
security = user
map to guest = bad user
dns proxy = no
#log file = /var/log/samba/log.%m     #定义Samba用户的日志文件，%m代表客户端主机名
#===============共享文件Share Definitions ========
[Anonymous]
path = /samba/anonymous
browsable =yes
writable = yes
guest ok = yes
read only = no
#create mode = 0664    #默认创建文件权限
#directory mode = 0775    #默认创建目录权限
(注意：在samba4中 share 和 server已经被禁用，需要用 security = user 和
  map to guest =Bad User来实现无密码访问目录)
```
5. mkdir -p /samba/anonymous
   sudo systemctl enable smb.service
   sudo systemctl enable nmb.service
   sudo systemctl restart smb.service
   sudo systemctl restart nmb.service

6. CentOs7的防火墙cmd会阻止Samba的访问，为了摆脱这个，我们运行
```
  sudo firewall-cmd --permanent --zone=public --add-service=samba
  sudo firewall-cmd --reload
```
7. 现在你可以在Windows中访问CentOS7的共享文件了，在命令提示行中输入：
`\\centos`
下面是浏览到的文件夹，如果你尝试着去创建一个文件，你会得到一个没有权限的错误信息、
检查这个共享文件夹的权限：ls -l

8. 我们下面给匿名用户一个权限:(为了给所有用户使用,所以给该目录授权nobody权限)
  ```
  cd /samba
  sudo chmod -R 0755 anonymous/
  sudo chown -R nobody:nobody anonymous/
  ls -l anonymous/
```
9. 配置Selinux
  chcon -t samba_share_t anonymous/
  (到此可以无密码使用了)

# 权限访问
1. 安全的Samba服务器
为了这个，我创建了一个组：smbgrp 和用户abc通过认证来访问Samba服务器。
```
  groupadd smbgrp
  useradd abc -G smbgrp
  smbpasswd -a abc      添加用户abc到Samba用户数据库中
```
2. 现在在Samba文件夹下创建一个文件夹：Secured ,并且给出权限：
```
  mkdir -p /samba/secured
```
3. 允许Selinux来监听:
```
  cd /samba
  chmod -R 0777 secured/
  chcon -t samba_share_t secured/
```
4. 再次编辑配置文件：
```
[...]
[secured]
 path = /samba/secured
 valid users = @smbgrp      #允许访问该共享的samba用户.例如：valid users = david，@dave，@tech（多个用户或者组中间用逗号隔开，如果要加入一个组就用“@组名”表示。）
 #invalid users = 用户      #说明：invalid users用来指定不允许访问该共享资源的用户。 例如：invalid users = root，@bob（多个用户或者组中间用逗号隔开。）
 #write list = 用户         #说明：write list用来指定可以在该共享下写入文件的用户。例如：write list = david，@dave
 guest ok = no              #是否允许guest账户访问
 writable = yes             #是否可写 
 browsable = yes            #浏览权限设置
 ```
 ```
 systemctl restart smb.service
 
 systemctl restart nmb.service
```
5. 命令:`testparm`      测试smb.conf配置是否正确

6. 现在在windows机器中可以使用相应的凭证(用户名和密码)来查看文件夹

7. 你的用户abc同样面对着写入权限的问题,让我们来给出权限

 `cd /samba`

 `chown -R abc:smbgrp secured/`

现在Samba用户在共享的目录中有写入的权限了，可以正常使用了.
至此,第一个文件夹是无需认证可直接使用,第二个文件夹secured是需要认证密码才可使用
`service smb restart` 重启服务
(注：如果你的 chcon命令不成功，请按下面的方式尝试：`chcon -h system_u:object_r:forderA  /path/to/B`)



chrome安装
sudo vim /etc/yum.repos.d/google-chrome.repo

[google-chrome]
name=google-chrome
baseurl=http://dl.google.com/linux/chrome/rpm/stable/$basearch
enabled=1
gpgcheck=1
gpgkey=https://dl-ssl.google.com/linux/linux_signing_key.pub

sudo yum -y install google-chrome-stable --nogpgcheck
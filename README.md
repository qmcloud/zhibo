@[TOC](分布式直播系统架构)

## 架构简介
技术群: ![Alt](https://img-blog.csdnimg.cn/20200623093238797.png)
**GitHub**:https://github.com/DOUBLE-Baller/momo[点击跳转](https://github.com/DOUBLE-Baller/momo).

	注：本教程适用于 Go 1.13 版本，因为 Go 1.11 才正式引入了 Go Module 作为包管理器。

**针对市面上现有的直播系统多为单机裸奔版本，系统臃肿，业务耦合 IM API 性能差，不可横向扩展等等诸多问题，所以采用高可用的分布式系统尤为必要，用到技术如下：**

后台管理：**laravel**
分布式：**go mirco + micro api + etcd + kafka**等 
前端：**vue**
移动端： **ios+android+小程序**等
数据库：**mysql+redis**
通讯框架：**gprc**
长连接通讯协议：**protocol buffers**


## 环境搭建
  **准备工具先，比如需要开发的东西，虚拟机镜像，工具等等。**
网盘链接：https://pan.baidu.com/s/1WsfqcAZ8Ph6id39gbOMjbQ 
提取码：7nq3
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922185921775.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)
**安装虚拟机**
> 1.安装虚拟机 centos7 系统  镜像链接: [https://www.centos.org/download/](https://www.centos.org/download/)	此处省略··

**安装golang** 
```bash
wget http://www.golangtc.com/static/go/go1.3.linux-amd64.tar.gz
```
```bash
tar -C /usr/local -zxvf  go1.3.linux-amd64.tar.gz 
```

```bash
vim /etc/profile	
// 在最后添加
export GOROOT=/usr/local/go
export PATH=$PATH:$GOROOT/bin
export GOPATH="$HOME/go
export GO111MODULE=on
export GOPROXY=https://goproxy.io
```
**成功**
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922210610471.png#pic_center)
**安装etcd**
```bash
curl -L https://github.com/coreos/etcd/releases/download/v3.3.2/etcd-v3.3.2-linux-amd64.tar.gz -o etcd-v3.3.2-linux-amd64.tar.gz
```
```bash
tar -zxf etcd-v3.3.2-linux-amd64.tar.gz
```
```bash
解压后是一些文档和两个二进制文件etcd和etcdctl。etcd是server端，etcdctl是客户端。
```
```bash
mv etcd-v3.3.2-linux-amd64/etcd* /$GOPATH/bin
```
```bash
./etcd 
```
**成功**
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922213137699.png#pic_center)
**安装 Protobuf 相关工具**

```bash
先创建项目文件 mkdir /www/go/live
```
```bash
cd /www/go/live
```
```bash
go mod init live
#自动生成go.mod文件
```
**Go Micro是Go开发微服务的RPC框架**
```bash
go get github.com/micro/go-micro
```
**安装 protoc**
```bash
可以从这里 https://github.com/protocolbuffers/protobuf/releases 下载`最新`版的 protoc 截止教程结束本人下载最新的是protobuf-all-3.13.0.tar：
./configure
make && make install
注意：没有c++ 的提前安一下，否则编译不过去，这种问题就不用赘述了，这个都不懂也没必要继续往下看了
```
```bash
protoc --version
```
![成功](https://img-blog.csdnimg.cn/20200922221355356.png#pic_center)
**安装 protoc-gen-micro**
```bash
go get -u github.com/micro/protoc-gen-micro
```
**安装 protoc-gen-go**
```bash
go get -u github.com/golang/protobuf/protoc-gen-go

cp protoc-gen-* /usr/local/bin/
```
至此你的$GOPATH/bin下有如下文件 复制一份到/usr/local/bin 下
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922222247686.png#pic_center)
**编写dome测试一把**
```golang
mkdir /www/go/live/proto
然后在 proto 目录下创建一个 Protobuf 格式的服务接口声明文件 live.proto：
```
```proto3
syntax = "proto3";

service Live {
rpc Call(LiveRequest) returns (LiveResponse) {}
}

message LiveRequest {
string name = 1; 
}

message liveResponse {
string result = 1;
}
```
```bash
protoc自动生成代码
protoc --proto_path=. --micro_out=. --go_out=. proto/live.proto
```
>此时proto文件下多出几个文件：
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922224029282.png#pic_center)

**编写GO服务实现代码 live 目录下 创建 mian.go**
```golang
package main

import (
	"context"
	"fmt"
	"github.com/micro/go-micro"
	proto "live/proto"
)

type LiveServiceHandler struct{}

func (g *LiveServiceHandler)Call(ctx context.Context, req *proto.LiveRequest, rsp *proto.LiveResponse) error {
	rsp.Result = "我的github：https://github.com/DOUBLE-Baller/" + req.Name
	return nil
}

func main()  {
	// 创建新的服务
	service := micro.NewService(
		micro.Name("go.micro.api.Live"), //go.micro.api 命名空间
	)

	// 初始化，会解析命令行参数
	service.Init()

	// 注册处理器，调用 Live 服务接口处理请求
	proto.RegisterLiveHandler(service.Server(), new(LiveServiceHandler))

	// 启动服务
	if err := service.Run(); err != nil {
		fmt.Println(err)
	}
}
```
```golang
go run main.go
```
```bash
如图表示成功启动服务 注意：添加环境变量 MICRO_REGISTRY=etcd 来统一设置 或者使用go run main.go --registry=etcd 命令手动注册服务
```

![在这里插入图片描述](https://img-blog.csdnimg.cn/2020092223034366.png#pic_center)
失败如下图
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922225010494.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)
```bash
如遇此错误：添加go.mod最后一行即可！
replace google.golang.org/grpc => google.golang.org/grpc v1.26.0
```
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923092941557.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


**利用 go micro 提供 HTTP 服务接口**
```bash
go get github.com/micro/micro/v2

安装完成后，会在 $GOPATH/bin 目录下创建一个 micro 可执行文件，cp 到/user/local/bin 下即可
```
```bash
micro api --handler=rpc
```
```bash
如下表示启动成功 默认端口8080 
```

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922233014118.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

访问IP:8080 如图 有防火墙的请添加规则放开8080端口即可访问！![在这里插入图片描述](https://img-blog.csdnimg.cn/20200922233410216.png#pic_center)
**ok 到此基础环境搭建完成 下面运行远程调用 api **
```bash
micro call go.micro.api.Live Live.Call '{"name": "momo"}'
```
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923111240482.png#pic_center)


## 编写代码
**项目目录：**
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923112617559.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

> server:       服务启动入口 
> config:       服务配置
> app:     每个服务私有代码 
> comm:   服务共有代码 
> sql:          项目sql文件
> test:         长连接测试脚本


**app服务介绍**
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923113249111.png#pic_center)

```bash
1.tcp_conn
维持与客户端的TCP长连接，心跳，以及TCP拆包粘包，消息编解码
2.ws_conn
维持与客户端的WebSocket长连接，心跳，消息编解码
3.logic
设备信息，好友信息，群组信息管理，消息转发逻辑
4.user
可以根据自己的业务需求，进行扩展,也可以替换成自己的业务服务器
```
**客户端接入流程**
```bash
1.调用LogicExt.RegisterDevice接口，完成设备注册，获取设备ID（device_id）,注意，一个设备只需完成一次注册即可，后续如果本地有device_id,就不需要注册了，举个例子，如果是APP第一次安装，就需要调用这个接口，后面即便是换账号登录，也不需要重新注册。
2.调用UserExt.SignIn接口，完成账户登录，获取账户登录的token。
3.建立长连接，使用步骤2拿到的token，完成长连接登录。
如果是web端,需要调用建立WebSocket时,将user_id,device_id,token，以URL参数的形式传递到服务器，完成长连接登录，例如：ws://localhost:8081/ws?user_id={user_id}&device_id={device_id}&token={token}
如果是APP端，就需要建立TCP长连接，在完成建立TCP长连接时，第一个包应该是长连接登录包（SignInInput），如果信息无误，客户端就会成功建立长连接。
4.使用长连接发送消息同步包（SyncInput），完成离线消息同步，注意：seq字段是客户端接收到消息的最大同步序列号，如果用户是换设备登录或者第一次登录，seq应该传0。
接下来，用户可以使用LogicExt.SendMessage接口来发送消息，消息接收方可以使用长连接接收到对应的消息。
```


----
未完待续
----

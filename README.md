# 概述
PHF框架 V1.0.1

此框架可用于PHP后端 `API` 快速开发，采用 `ADM （API-DOMAIN-MODEL）` 架构，快速上手，快速部署

# 目录结构

```
--
 |-- Api 业务类文件夹
 |-- Domain 逻辑类文件夹
 |-- Model 数据模型类文件夹
 |-- PHF 框架核心文件夹
 |-- Plugin 插件文件夹
 |-- config 配置文件夹
 |-- log 日志文件夹
 |-- api.php 业务访问入口文件
 |-- index.php 根index文件
```

# 路由
如果为 `nginx` 服务器，请将 `nginx.txt` 内的内容复制进 `nginx` 的配置文件中（如有特殊变动请自行更改）
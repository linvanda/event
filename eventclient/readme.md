事件系统客户端
=======

#### 依赖于eventlib

### 根命名空间：
- `member_eventclient`：对应到src目录
- `member_eventclient\tests`：对应到tests目录

### 约定：
- 所有的源程序写在`src`下；
- 测试用例写在`tests`下；
- 测试用例目录结构和`src`下的相同；
- 所有单测类以Test.php结尾；
- `doc`目录放文档相关文件；
- `vendor`放`composer`安装的外部依赖包；

### 单元测试：
1. 执行`composer install`安装phpunit依赖
2. 执行单测（先进入项目根目录）：`vendor/bin/phpunit -c phpunit.xml`

#### 关于事件系统详情，请[点击查看](https://git.mysoft.com.cn/cloudserver/mSDK/blob/master/event/readme.md)
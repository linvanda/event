会员事件池
该模块依赖于eventclient

事件池属于领域元素，应当每个项目维护自己的事件池，但由于会员老项目并没有按照领域划分，导致无法各项目自己维护自己的事件池，故临时将会员事件池放到mSDK中

根命名空间：
member_eventpool对应到src目录
member_eventpool\tests对应到tests目录

所有的源程序写在src下，测试用例写在tests下，测试用例目录结构和src下的相同

执行composer install安装依赖（如phpunit库）

执行单测：vendor\bin\phpunit -c phpunit.xml

所有单测类以Test.php结尾

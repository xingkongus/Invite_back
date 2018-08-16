## 基于Laravel5.5 + JWT『星邀请』小程序后台代码
>这是一款专属华广人的毕业照邀请函的小程序，它融华广特色建筑和人文情怀于一体，界面操作简单、方便、快捷，您只需要写下邀请、选择模板，便可一键生成您的专属毕业照邀请函，还可以保存小程序码分享到朋友圈邀请请亲朋好友一起来参与呢，赶快来体验吧~

## 小程序体验
![小程序码](/docs/qrcode.png)
>[前端代码](https://github.com/icharle/Invite_Vue)

## 特性
* 实现JWT token无痛刷新
* 实现微信小程序登录及[用户信息解密](https://developers.weixin.qq.com/miniprogram/dev/api/open.html#wxgetuserinfoobject)
* 实现微信小程序[带参场景值小程序码生成](https://developers.weixin.qq.com/miniprogram/dev/api/qrcode.html)

## 安装使用
```
# 从仓库中下载
$ git clone https://github.com/icharle/Invite_back.git

# 进入代码根目录安装依赖
$ composer install

# copy .env文件
$ cp .env.example .env

# 生成项目key
$ php artisan key:generate

# 生成jwt-auth key
$ php artisan jwt:secret

# 公开storage访问文件
$ php artisan storage:link

# 配置微信小程序appID && appSecret
$ WX_APPID = 您的小程序小程序ID
$ WX_SECRET = 您的小程序密钥

# 配置数据库并执行数据迁移
$ php artisan migrate
```

## 接口文档
[接口文档](https://github.com/icharle/Invite_back/wiki/%E6%8E%A5%E5%8F%A3%E6%96%87%E6%A1%A32.0%E7%89%88)
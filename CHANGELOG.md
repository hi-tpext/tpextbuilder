# CHANGELOG
---
## [1.9.121 / 3.9.105]
1.导出性能优化，真正支持`cursor()`查询返回的生成器。
2.支持全局设置[只导出已选列]只导出用户前端页面选择的列，也可在控制器中`$this->exportOnlyChoosedColumns=true|false`单独控制

---

## [1.9.120 / 3.9.104]
### 列表页可排除某些字段
```php
class Cmscontent extends Controller
{
    protected function initialize()
    {
            //其他初始化
          $this->indexFieldsExcept = 'content';//排除某字段，优化查询速度
    }
}
```
---

## [1.9.119 / 3.9.103]
### 修复：when里面的switchBtn问题
---

## [1.9.114 / 3.8.7]
### 取消上传文件分块的超时限制
---

## [1.9.110 / 3.8.3]
### HasOptions的optionsData方法textField支持变量
### 列表页checkbox样式调整
---

## [1.9.107 / 3.8.1]
### form的tab可以把按钮位置设置为左侧竖排（默认在顶部）。

```php
$from->getTab()->vertical();//设置竖排

$form->tab('基本信息');
//...其他字段
$form->tab('扩展信息');
//...其他字段
```

## 1.9.106/3.7.9
### 文件上传支持[占位图]，没有不论是否有文件，表单页面的布局都一样。

通过[占位图]里面的按钮操作[上传新文件]或[选择已上传文件]。

新增`showUploadBtn(bool)`方法，是否显示[上传新文件]，配合`showChooseBtn(bool)`可控制两个按钮。

新增`disableButtons()`方法，同时禁用[上传新文件]或[选择已上传文件]两个底部控制按钮。

可使用`cover('img_path')`设置占位图:

```php
$form->file('file','文件')->cover('/static/images/myupload.png')//默认会有一个占位图，可以设置自定义图片。
    ->disableButtons()->showInput(false);//同时禁用控制按钮和地址输入框，仅通过占位图控制
```
---

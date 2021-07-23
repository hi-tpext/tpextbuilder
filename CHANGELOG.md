# CHANGELOG

## Tpextbuilder

### 2021年7月

- 修正：特殊情况下图片无后缀就不可预览
- `$form`不使用`ajax`时自动出现加载提示
- `ActionBtn`的`label`支持使用变量，如`评论({comments_count})` => `评论12`
- `\tpext\builder\displayer\field`,`\tpext\builder\toolbar\Bar` 添加回调方法`rendering($callback)`，可以传入一个`Closure`，在渲染前调用

### 2021年6月25日

- 支持存储驱动扩展
- 表格翻页刷后，恢复到次的滚动条位置

### 2021年6月4日

- 梳理表格流程
- `buildDataList`等方法移从`HasBase`动到`HasIndex`中。
- 调整`buildDataList()`逻辑，增加方法参数，`buildDataList($where, $sortOrder, $page)`，新的方法仅需要专注于返回自定义数据即可，省略表格的设置；
- 新版本中将兼容旧的`buildDataList`写法，更新到新版本如果有问题需要调整一下；
- 增加全局变量`isEdit`，以供判断当前表单模式，和`protected function buildForm($isEdit, &$data = [])`中的`$isEdit`等效。
- 增加`getSortOrder`方法，如果需要增加一些额外的排序字段，可重写此方法。

### 2021年5月24日

增加表单联动[form]和[search]中可用。

#### 单选: radio / select

```php
// 单选，radio / select 
$form->radio('test1', '测试1')->options(['1' => '选项1', '2' => '选项2', '3' => '选项3'])->default(1)
    ->when(
        1, //选中值为1时
        $form->text('test_1_a', 'test_1_a')->required()
        //... 更多字段
    )->when(
        [2, 3],//选中值为[2 或 3]时，多个情况时传入参数为数组，数组各元素之间为[或]的关系
        $form->text('test_1_b', 'test_1_b')->required(),
        $form->textarea('test_1_c', 'test_1_c')->required()
        //... 更多字段
    );
```

#### 多选：checkbox / multipleSelect / dualListbox

```php
// 
$form->checkbox('test2', '测试2')->options(['1' => '选项1', '2' => '选项2', '3' => '选项3', '4' => '选项4'])->default(1)
    ->when(
        1,//只选中一个值，且这个值为1时
        $form->text('test_2_a', 'test_2_a')->required()
        //... 更多字段
    )->when(
        [2, 3],//只选中一个值，且这个值为[2 或 3]时，多个情况时传入参数为数组，数组各元素之间为[或]的关系
        $form->text('test_2_b', 'test_2_b')->required(),
        $form->textarea('test_2_c', 'test_2_c')->required()
        //... 更多字段
    )->when(
        [4, '3+4', '2+1+4'],//(只选中一个值，且这个值为4时) 或 (同时选中3,4两个值) 或 (同时选中1,2,4三个值)。
        //数组各元素之间为[或]的关系，单个元素用+号连接多个值表示同时选中（值之间不分先后顺序[2+1+4]和[1+2+4]和[4+1+2]等情况等效）
        $form->radio('test_2_d', 'test_2_d')->options([1 => '1', 2 => '2']),
        $form->textarea('test_2_e', 'test_2_e')->required()
        //... 更多字段
    );
```

### 2021年5月14日

`form`增加了4个语法糖：`left`,`middle`,`right`,`logs`.  

```php
$form->left(6, function () use ($form) {
    $form->text('name', '名称')->required()->maxlength(55);
    $form->select('category_id', '分类')->required()->dataUrl(url('/admin/shopcategory/selectPage'));
    $form->select('brand_id', '品牌')->dataUrl(url('/admin/shopbrand/selectPage'));
    $form->select('admin_group_id', '商家')->dataUrl(url('/admin/group/selectPage'));
});

$form->right(6)->with(
    $form->image('logo', '封面图')->required()->mediumSize(),
    $form->text('share_commission', '分销佣金')->default(0),
    $form->text('market_price', '市场价', 4),
    $form->text('cost_price', '成本价', 4)
);

//left/middle/right是对[fields]的封装,省略了 ->size(0, 12)->showLabel(false)等细节。

$form->logs('log_list', $logList, function () use ($form) {
    $form->show('action_note', '操作备注');
    $form->show('status_desc', '描述');
    $form->match('order_status', '订单状态')->options(OrderModel::$order_status_types);
    $form->match('pay_status', '支付状态')->options(OrderModel::$pay_status_types);
    $form->match('shipping_status', '物流状态')->options(OrderModel::$shipping_status_types);
    $form->show('create_time', '时间2');
});
//logs是对[items]的封装,省略了 ->canAdd(false)->cnaDelete(false)->size(0, 12)->showLabel(false)等细节。

```

### 2021年5月13日

[调整]视图模板路径加载方式  
[优化]`table`使用`fields`时，其中的字段支持`autopost`  .

```php
$table->fields('name_spu', '名称/spu')->with(
    $table->text('name', '名称')->autoPost(),
    $table->show('spu', 'spu')
);
```

[新增]`items`,`table`中对`image`图片等文件的上传支持，`table`中默认禁用上传，需要手动调用`canUpload`.  

```php
$table->image('logo', '封面图')->canUpload(true)->autoPost()->thumbSize(60, 60);
```

[提示]修改了`js`和`css`,`composer`升级后需要刷新一下资源.  

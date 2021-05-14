# CHANGELOG

## Tpextbuilder

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

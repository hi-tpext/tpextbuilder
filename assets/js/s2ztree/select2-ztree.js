/**
 * 基于select2和ztree的树形下拉框
 * 
 * @param options
 *            object类型的select2配置参数{...,ztree:{setting:{},zNodes:[]},valueField:值属性名称,textField:文本属性名称}
 * @author: 刘正勇
 * @version: v1.0.1
 */
!(function(factory) {
	if ('function' === typeof define && define.amd) {
		require(["jquery", "jquery.select2", "jquery.ztree"], factory);
	} else {
		factory(jQuery);
	}
}(function($) {
	'use strict';
	// 定义数据适配器
	$.fn.select2.amd.define('select2/data/ztree', ['./base', '../utils', 'jquery'], function(BaseAdapter, Utils, $) {
		function ZtreeSelectAdapter($element, options) {
			this.$element = $element;
			this.options = options || {};
			ZtreeSelectAdapter.__super__.constructor.call(this);
		}
		// 集成基础适配器
		Utils.Extend(ZtreeSelectAdapter, BaseAdapter);
		// 当前选中的选项
		ZtreeSelectAdapter.prototype.current = function(callback) {
			var data = [];
			// 找出选中的节点
			this.$element.find(':selected').each(function() {
				data.push($(this).attr('value'));
			});
			callback(data);
		};

		ZtreeSelectAdapter.prototype.select = function(data) {
			var self = this;
			if (this.options.get('multiple')) {
				this.current(function(currentData) {
					var val = [];
					if(!$.isArray(data)){
						data = [data];
					}
					data.push.apply(data, currentData);
					for (var d = 0; d < data.length; d++) {
						var id = data[d];
						if ($.inArray(id, val) === -1) {
							val.push(id);
						}
					}
					self.$element.val(val).trigger('change');
				});
			} else {
				this.$element.val(data).trigger('change');
			}
		};

		ZtreeSelectAdapter.prototype.unselect = function(data) {
			var self = this;
			if (!this.options.get('multiple')) {
				return;
			}
			this.current(function(currentData) {
				var val = [];
				var isArr = $.isArray(data);
				for (var d = 0; d < currentData.length; d++) {
					var id = currentData[d];
					if(isArr){
						if($.inArray(id, data)==-1 && $.inArray(id, val) === -1) {
							val.push(id);
						}
					}
					else if(id != data && $.inArray(id, val) === -1) {
						val.push(id);
					}
				}
				self.$element.val(val).trigger('change');
			});
		};

		ZtreeSelectAdapter.prototype.bind = function(container, $container) {
			var self = this;
			this.container = container;
			// 单选、多选 均绑定选择事件
			container.on('select', function(params) {
				self.select(params.data);
			});
			// 多选才绑定取消选择事件
			if (this.options.get('multiple')) {
				container.on('unselect', function(params) {
					self.unselect(params.data);
				});
			}
		};

		ZtreeSelectAdapter.prototype.destroy = function() {
			this.$element.find('*').each(function() {
				Utils.RemoveData(this);
			});
		};

		ZtreeSelectAdapter.prototype.query = function(params, callback) {
			var preText = this.$element.data('ztree-search');
			var text = $.trim(params.term);
			this.$element.data('ztree-search', text);
			// 首次搜索，且搜索文本为空，不作处理
			if (!preText && !text) {
				callback({
					results: []
				});
				return;
			}
			var data = [];
			var $ztree = this.container.results.$ztree;
			// 不存在ztree对象，不作处理
			if (!$ztree) {
				callback({
					results: []
				});
				return;
			}

			// ztree模糊搜索
			data = $ztree.fuzzySearch(text);

			callback({
				results: data
			});
		};
		// 不需要select2添加下拉选项
		ZtreeSelectAdapter.prototype.addOptions = function($options) {
		};
		ZtreeSelectAdapter.prototype.option = function(data) {
		};
		ZtreeSelectAdapter.prototype.item = function($option) {
		};
		ZtreeSelectAdapter.prototype._normalizeItem = function(item) {
		};
		ZtreeSelectAdapter.prototype.matches = function(params, data) {
		};

		return ZtreeSelectAdapter;
	});
	// 单选适配器
	$.fn.select2.amd.define('select2/selection/ztreeSingle', ['jquery', './base', '../utils'], function($, BaseSelection, Utils) {
		function ZtreeSingleSelection() {
			ZtreeSingleSelection.__super__.constructor.apply(this, arguments);
		}
		Utils.Extend(ZtreeSingleSelection, BaseSelection);
		ZtreeSingleSelection.prototype.render = function() {
			var $selection = ZtreeSingleSelection.__super__.render.call(this);
			$selection.addClass('select2-selection--single');
			$selection.html('<span class="select2-selection__rendered"></span>' + '<span class="select2-selection__arrow" role="presentation">' + '<b role="presentation"></b>'
				+ '</span>');
			return $selection;
		};
		ZtreeSingleSelection.prototype.bind = function(container, $container) {
			var self = this;
			ZtreeSingleSelection.__super__.bind.apply(this, arguments);
			var id = container.id + '-container';
			this.$selection.find('.select2-selection__rendered').attr('id', id).attr('role', 'textbox').attr('aria-readonly', 'true');
			this.$selection.attr('aria-labelledby', id);
			this.$selection.on('click', function(evt) {
				self.trigger('toggle', {
					originalEvent: evt
				});
			});
		};
		ZtreeSingleSelection.prototype.clear = function() {
			var $rendered = this.$selection.find('.select2-selection__rendered');
			$rendered.empty();
			$rendered.removeAttr('title');
			var $ztree = this.container.results.$ztree;
			//没有被选中的节点时，取消所有已选中的节点
			var checkedNodes = $ztree.getCheckedNodes(true);
			for(var i = 0 ;i<checkedNodes.length;i++){
				$ztree.checkNode(checkedNodes[i], false, false, false);
			}
		};

		ZtreeSingleSelection.prototype.display = function(data, container) {
			var template = this.options.get('templateSelection');
			var escapeMarkup = this.options.get('escapeMarkup');
			var str = template(data, container);
			return escapeMarkup(str);
		};

		ZtreeSingleSelection.prototype.selectionContainer = function() {
			return $('<span></span>');
		};

		ZtreeSingleSelection.prototype.update = function(data) {
			this.clear();
			if (data.length == 0) {
				return;
			}
			var $ztree = this.container.results.$ztree;
			var tId = this.$element.find('option[value='+data[0]+']').data('tId');
			var selection = $ztree.getNodeByTId(tId);
			var $rendered = this.$selection.find('.select2-selection__rendered');
			var formatted = this.display(selection, $rendered);
			$rendered.empty().append(formatted);
			$rendered.attr('title', selection[this.options.get('textField')]);
			$ztree.checkNode(selection, true, false, false);
			$ztree.expandNode(selection, true);
		};

		return ZtreeSingleSelection;
	});
	// 多选适配器
	$.fn.select2.amd.define('select2/selection/ztreeMulti', ['jquery', './base', '../utils'],
		function($, BaseSelection, Utils) {
			function ZtreeMultiSelection($element, options) {
				ZtreeMultiSelection.__super__.constructor.apply(this, arguments);
			}
			Utils.Extend(ZtreeMultiSelection, BaseSelection);
			ZtreeMultiSelection.prototype.render = function() {
				var $selection = ZtreeMultiSelection.__super__.render.call(this);
				$selection.addClass('select2-selection--multiple');
				$selection.html('<ul class="select2-selection__rendered"></ul>');
				return $selection;
			};
			ZtreeMultiSelection.prototype.bind = function(container, $container) {
				var self = this;
				ZtreeMultiSelection.__super__.bind.apply(this, arguments);
				this.$selection.on('click', function(evt) {
					self.trigger('open', {
						originalEvent: evt
					});
				});
				this.$selection.on('click', '.select2-selection__choice__remove', function(evt) {
					if (self.options.get('disabled')) {
						return;
					}
					var $remove = $(this);
					var $selection = $remove.parent();
					var data = Utils.GetData($selection[0], 'data');
					self.trigger('unselect', {
						originalEvent: evt,
						data: data.id
					});
				});
			};
			ZtreeMultiSelection.prototype.clear = function() {
				var $rendered = this.$selection.find('.select2-selection__rendered');
				$rendered.empty();
				$rendered.removeAttr('title');
			};
			ZtreeMultiSelection.prototype.display = function(data, container) {
				var template = this.options.get('templateSelection');
				var escapeMarkup = this.options.get('escapeMarkup');
				return escapeMarkup(template(data, container));
			};
			ZtreeMultiSelection.prototype.selectionContainer = function() {
				var $container = $('<li class="select2-selection__choice">' + '<span class="select2-selection__choice__remove" role="presentation">' + '&times;' + '</span>'
					+ '</li>');
				return $container;
			};

			ZtreeMultiSelection.prototype.update = function(data) {
				this.clear();
				var $ztree = this.container.results.$ztree;
				var checkedNodes = $ztree.getCheckedNodes(true);
				//取消全部
				for(var node of checkedNodes){
					$ztree.checkNode(node, false, false, false);
				}
				if (data.length === 0) {
					return;
				}
				checkedNodes = [];
				var $selections = [];
				//data有值，但ztree无选中的节点，进行初始化
				for(var id of data){
					var tId = this.$element.find('option[value='+id+']').data('tId');
					checkedNodes.push($ztree.getNodeByTId(tId));
				}
				for (var d = 0; d < checkedNodes.length; d++) {
					var selection = checkedNodes[d];
					if($.inArray(selection[this.options.get('valueField')]+'', data)==-1){
						$ztree.checkNode(selection, false, false, false);
						continue;
					}
					var $selection = this.selectionContainer();
					var formatted = this.display(selection, $selection);
					$selection.append(formatted);
					$selection.attr('title', selection[this.options.get('titleField')] || selection[this.options.get('textField')]);
					Utils.StoreData($selection[0], 'data', selection);
					$selections.push($selection);
					// 选中节点
					$ztree.checkNode(selection, true, false, false);
				}
				$ztree.expandNode(checkedNodes[0], true);
				var $rendered = this.$selection.find('.select2-selection__rendered');
				Utils.appendMany($rendered, $selections);
				//增加或者取消选项后，更新下拉面板的位置
				this.container.dropdown._positionDropdown();
			};
			return ZtreeMultiSelection;
		});
	// 定义结果展示适配器
	$.fn.select2.amd.define('select2/ztreeresults', ['jquery', './utils'], function($, Utils) {
		function ZtreeResults($element, options, dataAdapter) {
			this.$element = $element;
			this.data = dataAdapter;
			this.options = options;
			ZtreeResults.__super__.constructor.call(this);
		}
		Utils.Extend(ZtreeResults, Utils.Observable);
		ZtreeResults.prototype.render = function() {
			var config = this.options.get('ztree');
			var $results = $('<ul class="select2-results__options ztree" id="' + config.setting.treeId + '" role="tree"></ul>');
			if (this.options.get('multiple')) {
				$results.attr('aria-multiselectable', 'true');
			}
			this.$results = $results;
			return $results;
		};

		ZtreeResults.prototype.clear = function() {
		};

		ZtreeResults.prototype.displayMessage = function(params) {
			var escapeMarkup = this.options.get('escapeMarkup');
			this.clear();
			this.hideLoading();
			var $message = $('<li role="treeitem" aria-live="assertive"' + ' class="select2-results__option"></li>');
			var message = this.options.get('translations').get(params.message);
			$message.append(escapeMarkup(message(params.args)));
			$message[0].className += ' select2-results__message';
			this.$results.append($message);
		};
		ZtreeResults.prototype.hideMessages = function() {
			this.$results.find('.select2-results__message').remove();
		};
		// 展示下拉选项
		ZtreeResults.prototype.append = function(data) {
			this.hideLoading();
		};

		ZtreeResults.prototype.position = function($results, $dropdown) {
			$dropdown.find('.select2-results').append($results);
		};

		ZtreeResults.prototype.sort = function(data) {
			return data;
		};

		ZtreeResults.prototype.highlightFirstItem = function() {
		};

		ZtreeResults.prototype.setClasses = function() {
		};

		ZtreeResults.prototype.showLoading = function(params) {
			this.hideLoading();
			var loadingMore = this.options.get('translations').get('searching');
			var loading = {
				disabled: true,
				loading: true,
				text: loadingMore(params)
			};
			var $loading = this.option(loading);
			$loading.className += ' loading-results';
			this.$results.prepend($loading);
		};

		ZtreeResults.prototype.hideLoading = function() {
			this.$results.find('.loading-results').remove();
		};

		ZtreeResults.prototype.option = function(data) {
			var option = document.createElement('li');
			option.className = 'select2-results__option';
			if (data.title) {
				option.title = data.title;
			}
			return option;
		};

		ZtreeResults.prototype.bind = function(container, $container) {
			var self = this;
			var id = container.id + '-results';
			self.$results.attr('id', id);

			// 初始化ztree和下拉选项
			// 由于同一个页面存在多个ztree对象时，必须为ztree容器指定id；否则最终只有1个ztree对象存在，会导致交互异常
			if (!self.$ztree) {
				var config = self.options.get('ztree');
                // 清空select内容，避免对同一select反复销毁创建时，option持续增加
				self.$element.empty();
				// 节点创建完成后，向selct2中追加对应的选项，用于匹配选中的ztree节点
				var _onNodeCreated = config.setting.callback.onNodeCreated;
				config.setting.callback.onNodeCreated = function(event, treeId, treeNode) {
					var valueField = self.options.get('valueField');
					var textField = self.options.get('textField');
					var multiple = self.options.get('multiple');
					var checkedNodes = [];
					// 生成select-->option
					var id = treeNode[valueField];
					var $option = $('<option value="' + id + '" data-select2-id="' + id + '">' + treeNode[textField] + '</option>');
					$option.data('tId', treeNode.tId);
					self.$element.append($option);
					if(treeNode.checked){
						checkedNodes.push(id);
					}
					if(!multiple){
						self.$element.val(checkedNodes[0]).trigger('change');
					}else {
						self.data.current(function(currentData){
							checkedNodes.push(currentData);
						});
						self.$element.val(checkedNodes).trigger('change');
					}
					if(_onNodeCreated) {
						_onNodeCreated.apply(event, treeId, treeNode);
					}
				}
				self.$ztree = $.fn.zTree.init(self.$results, config.setting, config.zNodes);
				self.$element.data('select2ztree.ztree', self.$ztree);
			}

			container.on('results:all', function(params) {
				self.clear();
				self.append(params.data);
			});
			container.on('query', function(params) {
				self.hideMessages();
				self.showLoading(params);
			});
			container.on('open', function() {
				// When the dropdown is open, aria-expended="true"
				self.$results.attr('aria-expanded', 'true');
				self.$results.attr('aria-hidden', 'false');
				self.setClasses();
				self.ensureHighlightVisible();
				self.$results.find('li span').css('display','inline-block');
			});
			container.on('close', function() {
				// When the dropdown is closed,
				// aria-expended="false"
				self.$results.attr('aria-expanded', 'false');
				self.$results.attr('aria-hidden', 'true');
				self.$results.removeAttr('aria-activedescendant');
			});

			if ($.fn.mousewheel) {
				this.$results.on('mousewheel', function(e) {
					var top = self.$results.scrollTop();

					var bottom = self.$results.get(0).scrollHeight - top + e.deltaY;

					var isAtTop = e.deltaY > 0 && top - e.deltaY <= 0;
					var isAtBottom = e.deltaY < 0 && bottom <= self.$results.height();

					if (isAtTop) {
						self.$results.scrollTop(0);

						e.preventDefault();
						e.stopPropagation();
					} else if (isAtBottom) {
						self.$results.scrollTop(self.$results.get(0).scrollHeight - self.$results.height());

						e.preventDefault();
						e.stopPropagation();
					}
				});
			}

			this.$results.on('mouseup', '.select2-results__option[aria-selected]', function(evt) {
				var $this = $(this);
				var data = Utils.GetData(this, 'data');
				if ($this.attr('aria-selected') === 'true') {
					if (self.options.get('multiple')) {
						self.trigger('unselect', {
							originalEvent: evt,
							data: data.id
						});
					} else {
						self.trigger('close', {});
					}
					return;
				}
				self.trigger('select', {
					originalEvent: evt,
					data: data.id
				});
			});
		};

		ZtreeResults.prototype.getHighlightedResults = function() {
			var $highlighted = this.$results.find('.select2-results__option--highlighted');

			return $highlighted;
		};

		ZtreeResults.prototype.destroy = function() {
			this.$results.remove();
		};

		ZtreeResults.prototype.ensureHighlightVisible = function() {
			var $highlighted = this.getHighlightedResults();

			if ($highlighted.length === 0) {
				return;
			}

			var $options = this.$results.find('[aria-selected]');

			var currentIndex = $options.index($highlighted);

			var currentOffset = this.$results.offset().top;
			var nextTop = $highlighted.offset().top;
			var nextOffset = this.$results.scrollTop() + (nextTop - currentOffset);

			var offsetDelta = nextTop - currentOffset;
			nextOffset -= $highlighted.outerHeight(false) * 2;

			if (currentIndex <= 2) {
				this.$results.scrollTop(0);
			} else if (offsetDelta > this.$results.outerHeight() || offsetDelta < 0) {
				this.$results.scrollTop(nextOffset);
			}
		};

		ZtreeResults.prototype.template = function(result, container) {
			var template = this.options.get('templateResult');
			var escapeMarkup = this.options.get('escapeMarkup');
			var content = template(result, container);
			if (content == null) {
				container.style.display = 'none';
			} else if (typeof content === 'string') {
				container.innerHTML = escapeMarkup(content);
			} else {
				$(container).append(content);
			}
		};

		return ZtreeResults;
	});

	// 定义组件
	$.fn.select2.amd.define("jquery.select2.ztree", ['jquery', 'jquery-mousewheel', './select2/core', './select2/defaults', './select2/utils', "./select2/ztreeresults",
		"select2/selection/ztreeMulti", "select2/selection/ztreeSingle", "select2/data/ztree"], function($, _, Select2, Defaults, Utils, ZtreeResults, ZtreeMultiSelection,
			ZtreeSingleSelection, ZtreeSelectAdapter) {
		if ($.fn.select2ztree) {
			return $.fn.select2ztree;
		}
		// All methods that should return the element
		var thisMethods = ['open', 'close', 'destroy'];
		// 定义jquery对象名称
		$.fn.select2ztree = function(_options) {
			if (typeof _options === 'object') {
				let options = $.extend(true, {}, _options);
				// 初始化属性
				// 值属性名称
				options.valueField = options.valueField || 'id';
				// 文本属性名称
				options.textField = options.textField || 'text';
				options.resultsAdapter = ZtreeResults;
				options.dataAdapter = ZtreeSelectAdapter;
				options.templateSelection = function(selection) {
					// 模糊搜索控件会将节点名称的实际值保存在oldname属性中
					if (selection.oldname) {
						return selection.oldname;
					}
					return selection[options.textField];
				};
				if (!options.ztree || !options.ztree.setting) {
					throw new Error('缺少ztree配置: {ztree:{setting:{},zNodes:[]}}');
				}
				if (options.ztree.zNodes) {
					// 禁止节点进行url跳转
					$.each(options.ztree.zNodes, function(idx, ele) {
						ele.url = '';
					});
				}
				// 覆盖视图配置
				options.ztree.setting.view = {
					selectedMulti: false,
					dblClickExpand: false
				};
				var self = this;
				// 重写ztree点击事件：点击之后用于触发select2相关操作
				var callback = {};
				var ztreeSettingCheck = options.ztree.setting.check || {};
				options.ztree.setting.check = ztreeSettingCheck;
				// 强制开启单选/复选框
				ztreeSettingCheck.enable = true;
				var multiple = $(this).attr('multiple');
				// 多选
				if (multiple) {
					ztreeSettingCheck.chkStyle = "checkbox";
					ztreeSettingCheck.autoCheckTrigger = true;
					options.selectionAdapter = ZtreeMultiSelection;
					//多选在选择选项时不关闭下拉面板
					options.closeOnSelect = false;
				} else {
					ztreeSettingCheck.chkStyle = "radio";
					//单选，将整棵树当作一个radio分组
					ztreeSettingCheck.radioType = "all";
					options.selectionAdapter = ZtreeSingleSelection;
				}
				callback.beforeCheck = function(treeId, treeNode) {
					var $ztree = $(self).data('select2ztree.ztree');
					if (multiple) {
						return true
					}
					//单选，并且选择的是自己，不作处理
					var checkeds = $ztree.getCheckedNodes(true);
					if (checkeds.length > 0 && checkeds[0].tId == treeNode.tId) {
						self.select2ztree('trigger', 'close');
						return false;
					}
					return true;
				};
				callback.beforeClick = function(treeId, treeNode) {
					var $ztree = $(self).data('select2ztree.ztree');
					//单选，并且选择的是自己，不作处理
					if (!multiple) {
						var checkeds = $ztree.getCheckedNodes(true);
						if (checkeds.length > 0 && checkeds[0].tId == treeNode.tId) {
							self.select2ztree('trigger', 'close');
							return false;
						}
					}
					$ztree.checkNode(treeNode, !treeNode.checked, true, true);
					return false;
				}
				callback.onCheck = function(e, treeId, treeNode) {
					var $ztree = $.fn.zTree.getZTreeObj(treeId);
					var nodes = $ztree.getCheckedNodes(true);
					if(nodes.length==0){
						self.select2ztree('val',[[]]);
					}else {
						var vals = [];
						for(var i in nodes) {
							vals.push(nodes[i][options.valueField]);
						}
						self.select2ztree('val',vals);
						if(!multiple){
							self.select2ztree('toggleDropdown');
						}
					}
				};
				options.ztree.setting.callback = callback;
				this.each(function() {
					var instanceOptions = $.extend(true, {}, options);
					$(this).data('select2ztree', new Select2($(this), instanceOptions));
				});
				self.options = options;
				return self;
			} else if (typeof _options === 'string') {
				var ret;
				var args = Array.prototype.slice.call(arguments, 1);
				this.each(function() {
					var instance = Utils.GetData(this, 'select2');
					if (instance == null && window.console && console.error) {
						console.error('The Select2ztree(\'' + _options + '\') method was called on an ' + 'element that is not using Select2.');
					}
					//特殊处理：select2定义的val方法会将args当作数组处理
					if (_options == 'val' && $.isArray(args) && $.isArray(args[0]) && args[0].length > 1) {
						ret = instance[_options].call(instance, args);
					} else {
						ret = instance[_options].apply(instance, args);
					}
				});
				// Check if we should be returning `this`
				if ($.inArray(_options, thisMethods) > -1) {
					return this;
				}
				return ret;
			} else {
				throw new Error('Invalid arguments for Select2ztree: ' + _options);
			}
		};

		$.fn.select2ztree.defaults = Defaults;

		return $.fn.select2ztree;
	});
	return jQuery.fn.select2.amd.require('jquery.select2.ztree');
}));
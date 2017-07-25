;
/**
 *select控件默认
 */
function selectInit(){
	return "0:[请选择]";
}
//日期插件
function dataInit(element) {
	$(element).attr('readonly', true);
	if ($(element).val() == '') {
		$(element).attr('placeholder','[请选择时间]');
	}
	jQuery(element).click(function(){
		laydate({istime: true, format: 'YYYY-MM-DD',choose:function(dates){
			$(element).change();
		}});
	});
}
//jqGrid树形select控件
function treeBuildSelect(data) {
	data = $.parseJSON(data);
	var select = '<select>';
	$.each(data.result, function (key, val) {
		if (val.c) {
			select += '<optgroup label="' + val.title + '">';
			$.each(val.c, function (k, v) {
				select += '<option value="'+ val.id + "-" + v.id + '">' + v.title + '</option>';
			});
			select += '</optgroup>';
		} else {
			select += '<option value="'+ val.id + '">' + val.title + '</option>';
		}
	});
	select += '</select>';
	return select;
}
//jqGrid树形格式化formatter
function treeFormat(cellvalue, json) {
	var arr = [];
	var value = '';
	if (!json) {
		return value;
	}
	if (/^\d+\-\d+$/.test(cellvalue)) {
		arr = cellvalue.split('-');
	} else {
		arr.push(cellvalue);
	}
	$.each(json, function (key, val){
		if (val.id == arr[0]) {
			value += val.title;
			if (val.c && arr[1]) {
				$.each(val.c, function(k, v){
					if (arr[1] == v.id) {
						value += '-'+v.title;
					}
				});
			}
			return false;
		}
	});
	return value;
}
//树形反格式化
function treeUnformat(cellvalue, json){
	var arr = [];
	var value = '';
	if (!json) {
		return value;
	}
	if (/\W+\-\W+/.test(cellvalue)) {
		arr = cellvalue.split('-');
	} else {
		arr.push(cellvalue);
	}
	$.each(json, function (key, val){
		if (val.title == arr[0]) {
			value += val.id;
			if (val.c && arr[1]) {
				$.each(val.c, function(k, v){
					if (arr[1] == v.title) {
						value += '-'+v.id;
					}
				});
			}
			return false;
		}
	});
	return value;
}
/*jqGrid数组值的展示*/
function getArrVal(json, type, matches) {
	var title = '0:[请选择]';
	if (json) {
		if (type == 'single') {
			$.each(json, function(kay, val){
				title += ';' + kay + ':' + val;
			});
		}else if (type == 'earnumber') {
			$.each(json, function(key, val){
				title += ';' + val.id + ':' + val.ear_number;
			});
		}else {
			$.each(json, function(kay, val){
				if (matches && val.matches) {
					if (val.matches == matches) {
						title += ';' + val.id + ':' + val.title;
					}
				} else {
					title += ';' + val.id + ':' + val.title;
				}
			});
		}
	}
	return title;
}
/*jqGrid数组格式化*/
function arrFormat(cellvalue, json, type){
	var value = '';
	if (!json) return value;
	if (type =='single') {
		$.each(json, function (key, val){
			if (key == cellvalue) {
				value = val;
				return false;
			}
		});
	} else {
		$.each(json, function (key, val){
			if (val.id == cellvalue) {
				value = val.title;
				return false;
			}
		});
	}
	return value;
}
/*jqGrid数组反格式化*/
function arrUnformat(cellvalue, json, type){
	var value = '';
	if (!json) return value;
	if (type == 'single') {
		$.each(json, function (key, val){
			if (val == cellvalue) {
				value = key;
				return false;
			}
		});
	} else {
		$.each(json, function (key, val){
			if (val.title == cellvalue) {
				value = val.id;
				return false;
			}
		});
	}
	return value;
}
/**/
/*jqGrid添加编辑控件扩展 实例化*/
function createFreightEditElement(value, editOptions, json, element, type) {
	var html = '<div>';
	var name = editOptions.name;
	if (type == 'single') {
		$.each(json, function (key, val){
			if (value) {
				if (key == value) {
					html += '<div class="col-sm-4 '+element+' '+element+'-primary" style="margin-top:-5px"><input id="'+name+'-'+key+'" name="'+name+'" value="'+key+'" type="'+element+'" checked><label for="'+name+'-'+key+'">'+val+'</label></div>';
				} else {
					html += '<div class="col-sm-4 '+element+' '+element+'-primary" style="margin-top:-5px"><input id="'+name+'-'+key+'" name="'+name+'" value="'+key+'" type="'+element+'"><label for="'+name+'-'+key+'">'+val+'</label></div>';
				}
			} else {
				html += '<div class="col-sm-4 '+element+' '+element+'-primary" style="margin-top:-5px"><input id="'+name+'-'+key+'" name="'+name+'" value="'+key+'" type="'+element+'" checked><label for="'+name+'-'+key+'">'+val+'</label></div>';
			}
		});
	}
	html += '</div>';
	return html;
}
/*jqGrid添加编辑控件扩展 取值*/
function getFreightElementValue(elem, oper, value, element) {
	if (oper === "set") {
	}
	if (oper === "get") {
		return elem.find('input:'+element+':checked').val();
	}
}
/*用户绑定双击点击弹出页面效果*/
function postfixBindDblclick(sign, url, title, form) {
	$("#"+sign).attr('placeholder', '[双击选择]').attr('readonly', 'readonly').dblclick(function(e){
		var pigfarm_id = Number($(form).find('#pigfarm_id').val());
		if (pigfarm_id <= 0) {
			layer.msg('所属猪场不正确', {'icon':2});
		} else {
			popup(url + '?params[pigfarm_id]='+pigfarm_id, title)
		}
	});
}
/*用于选中后执行操作后置操作*/
function callBackRowData(sign, rowid, target){
	var rowData = jQuery(sign).getRowData(rowid);
	parent._postfixRowData(rowData, target);
}
function _postfixRowData(rowData, target){
	$("#"+target).val(rowData.ear_number);
}

/**
 *猪场联动数据
 */
function pigFarmChange(sign, _string) {
	$("#"+sign).change(function(){
		var value = this.value;
		if (value == '') value = 0;
		$.each(_string.split('|'), function(key, val){
			var fn = val.split(':');
			if (fn[2]) {
				eval('_ganged("'+fn[1]+'","'+fn[0]+'",'+value+',"'+fn[2]+'")');
			} else {
				eval('_ganged("'+fn[1]+'","'+fn[0]+'",'+value+')');
			}
			
		});
	});
}
/**
 *修改时默认数据
 */
function _init_ganged(pigFarmId, _string) {
	if (pigFarmId == '') pigFarmId = 0;
	$.each(_string.split('|'), function(key, val){
		var fn = val.split(':');
		if (fn[3] == '') fn[3] = 0;
		eval('_ganged("'+fn[1]+'","'+fn[0]+'",'+pigFarmId+',"'+fn[2]+'",'+fn[3]+')');
	});
}

/**
 *根据猪场id联动数据
 */
function _ganged(object, sign, pigFarmId, _string, defaults){
	if (_string == 'input') {
		$("#"+sign).attr('placeholder', '[双击选择]').val('');
	}
	if (pigFarmId == 0 || pigFarmId == '') {
		$("#"+sign).empty().append("<option value='0'>[请选择]</option>");
		return false;
	}
	$.post("/manage/ajax/ganged/"+object+"/json.html", {"pigfarm":pigFarmId, '_string':_string},function(data){
		if ($.isEmptyObject(data.result)) {
			data.result = [{"id":0,"title":"[无数据，请先添加]"}];
		}
		var select = '';
		$.each(data.result, function (key, val) {
			if (val.c) {
				select += '<optgroup label="' + val.title + '">';
				$.each(val.c, function (k, v) {
					if (defaults == (val.id + "-" + v.id)) {
						select += '<option value="'+ val.id + "-" + v.id + '" selected>' + v.title + '</option>';
					} else {
						select += '<option value="'+ val.id + "-" + v.id + '">' + v.title + '</option>';
					}
				});
				select += '</optgroup>';
			} else {
				if (defaults == val.id) {
					select += '<option value="'+ val.id + '" selected>' + val.title + '</option>';
				} else {
					select += '<option value="'+ val.id + '">' + val.title + '</option>';
				}
			}
		});
		$("#"+sign).empty().append(select);
	}, "json");
}

function fninitComplete(setting) {
    $(this).attr('inited', 1);
    $('.dataTables_filter').remove();
    var p=$('#dataTable_filter');
    if (p.length>0) {
        if (p.find("select").select2)
            p.find("select").each(function () {
                if (!$(this).attr('select2') || $(this).attr('select2') == 'true')
                    $(this).select2({minimumResultsForSearch: Infinity})
            });
        p.find("input,select").on('keyup change', function () {
            if (typeof event != 'undefined' && event.keyCode == 13)
                return false;
            if ($(this).attr('target')) {
                field = $(this).attr('target');
                if (setting.aoColumns) {
                    for (var i in setting.aoColumns) {
                        if (setting.aoColumns[i]['mData'] == field) {
                            $('#' + setting['sTableId']).DataTable().column(i).search($(this).val(), this.tagName.toLowerCase() == 'select' ? false : true).draw();
                            break;
                        }
                    }
                }
            }
        });
    }
    if ($(this).parents('.dataTables_wrapper').find(".dataTables_length select").size() > 0 && $(this).parents('.dataTables_wrapper').find(".dataTables_length select").select2) {
        $(this).parents('.dataTables_wrapper').find(".dataTables_length select").css({width: '50px'}).select2({minimumResultsForSearch: Infinity})
    }
    $(".dataTable th input[type='checkbox']").on('click', function () {
        $(this).parents('.dataTables_scroll').find("tbody input[type='checkbox']").prop("checked", this.checked ? true : false);
    });
//    window['datatable'] = obj;
}
function fncreatedRow(row, data, index) {
    row.id = data['_id'];
}

function fndrawCallback(setting) {
    //setting = obj.dataTableSettings[obj.dataTableExt._unique - 1];
    url = setting.oInit.sAjaxSource;
    api = this.api();
    var rtype=setting.oInit.rtype;

    api.on('click',".btn_del", function () {
        if (confirm('确定要删除该项吗?')) {
            $.ajax({
                'url': url + (url.indexOf('?')>-1?'&':'?') +'id=' + $(this).parents('tr').attr('id'),
                'type': 'DELETE',
                'dataType': 'json',
                'success': function (data) {
                    api.ajax.reload(null, false);
                },
                'error': function (data) {
                    alert(data.responseJSON || data.responseText);
                }
            });
        }
    });
    api.$(".status2").on('click', function () {
        var field = $(this).attr('field') || 'status';
        var data = {};
        data[field] = this.checked ? 1 : 0;
        var _this = this;
        $.ajax({
            'url': url + '?id=' + $(this).parents('tr').attr('id'),
            'type': 'PUT',
            'dataType': 'json',
            'data': data,
            'success': function (data) {
                $(_this).parents('td').attr('title', _this.checked ? '正常' : '锁定');
            },
            'error': function (data) {
                alert(data.responseJSON || data.responseText);
            }
        });
    });
    api.on('click',".btn_edit", function () {
        if (setting.oInit.editUrl) {
            loadHtmlByAjax(setting.oInit.editUrl + '?id=' + $(this).parents('tr').attr('id'));
           //window.location.href = setting.oInit.editUrl + '?id=' + $(this).parents('tr').attr('id');
        }
    });
    api.$(".btn_status").on('click', function () {
        $.ajax({
            'url': url + '?id=' + $(this).parents('tr').attr('id'),
            'type': 'PUT',
            'dataType': 'json',
            'data': {'status': $(this).attr('status')},
            'success': function (data) {
                api.ajax.reload(null, false);
            },
            'error': function (data) {
                alert(data.responseJSON || data.responseText);
            }
        });
    });
}
function formatAction(cell, cellData, rowData, rowIndex, colIndex) {
    var html = '';
    setting = this.dataTableSettings[this.dataTableExt._unique - 1];

    html += '<button class="btn m-b-xs btn-sm btn-primary btn-addon btn_edit"><i class="fa fa-edit"></i>修改</button>';
    html += '<button class="btn m-b-xs btn-sm btn-primary btn-addon btn_del"><i class="fa fa-edit"></i>删除</button>';
    cell.innerHTML = html;
}
function checkAllCheckBox(obj) {
    $this = $(obj);
    var table = $this.parents('.dataTable');
    var checked = $this.prop('checked');
    var checkboxes = table.find("td input[type='checkbox']");
    var checkedboxes = table.find("td input[type='checkbox']:checked");
    if ($this.parents('th').size() == 1) {
        table.find("td input[type='checkbox']").prop('checked', checked);
    } else {
        if (checkboxes.length == checkedboxes.length) {
            table.find('th :checkbox').prop('checked', true);
        } else {
            table.find('th :checkbox').prop('checked', false);
        }
    }
}
function formatCheckBox(cell, cellData, rowData, rowIndex, colIndex) {
    cell.innerHTML = '<span class="radio-input table-radio-input checkt-input"><input type="checkbox" value="' + cellData + '"  onclick="checkAllCheckBox(this)"><span></span></span>';
}
function deleteRow(cls){
    table = typeof cls == 'undefined' ? $('.dataTable') : $("." + cls);
    var len = table.find('td input:checked').length;
    if (len == 0) {
        alert('请选择你要修改的项');
        return;
    }
    var ids = [];
    table.find('td input:checked').each(function () {
        ids.push($(this).val());
    });
    data = 'ids='+ids.join(',');
    var url=table.dataTable().api().ajax.url();
    if(url.indexOf('?')>-1){
        url+='&'+data;
    }else{
        url+='?'+data;
    }
    if (confirm('确定要删除所选项吗?')) {
        $.ajax({
                'url': url,
                'method':'DELETE',
                'dataType': 'json',
                success: function (response) {
                    table.dataTable().api().ajax.reload(null, false);
                },
                error: function (data, status) {
                    alert(data.responseJSON || data.responseText);
                }
            });
    }
}
function formatImg(cell, cellData, rowData, rowIndex, colIndex) {
    var html = '';
    for(var key in cellData){
        html+='<img src="'+cellData[key]+'" style="width:100px;height:100px"/>'
    }
    cell.innerHTML = html;
}
function formatDate(cell, cellData, rowData, rowIndex, colIndex) {
    if (cellData) {
        var date = new Date(parseInt(cellData) * 1000);
        cellData = 'Y-m-d h:i:s'.replace(/Y+/, date.getFullYear())
                .replace(/m+/, _format2digit(date.getMonth() + 1))
                .replace(/d+/, _format2digit(date.getDate()))
                .replace(/h+/, _format2digit(date.getHours()))
                .replace(/i+/, _format2digit(date.getMinutes()))
                .replace(/s+/, _format2digit(date.getTime()/1000%60));
    } else
        cellData = '';
    cell.innerHTML = cellData;
}
function _format2digit(value) {
    value = '0' + value.toString();
    var len = value.length;
    return value.substr(len - 2);
}
function loadData(url, scope, fn) {
    $.ajax(url, {
        dataType: 'json',
        //complete: ajaxResult,
        success: function (data, status) {
            renderData(data, scope, fn);
        }
    });
}
/**
 * @description 加载用户数据
 * @returns 
 */
function renderData(data, scope, fn) {
    if (typeof scope == 'undefined')
        scope = $('body');
    scope.find("[item]").each(function (i) {
        name = $(this).attr('item');
        var value = null;
        if (name.indexOf('.') == -1) {
            if (typeof data[name] != 'undefined') {
                value = data[name];
            }
        } else {
            c = name.split('.');
            t = data;
            for (i in c) {
                if (typeof t == 'object' && typeof t[c[i]] != 'undefined') {
                    if (i == c.length - 1) {
                        value = t[c[i]];
                    } else {
                        t = t[c[i]];
                    }
                }
            }
        }
        if (value !== null) {
            if ($(this).attr('format')) {
                f = $(this).attr('format').split(',');
                format = f[0];
                if (f.length == 1) {
                    value = window[format].call(this, value)
                } else if (f.length == 2) {
                    value = window[format].call(this, value, f[1]);
                } else {
                    value = window[format].call(this, value, f[1], f[2]);
                }
            }
            if ('INPUT' == this.tagName) {
                if ($(this).attr('type') == 'text' || $(this).attr('type') == 'hidden') {
                    $(this).val(value);
                } else if ($(this).attr('type') == 'radio') {
                    this.checked = $(this).val() == value;
                } else if ($(this).attr('type') == 'checkbox') {
                    if (typeof value != 'object') {
                        this.checked = $(this).val() == value;
                    } else {
                        for (i in value) {
                            if (value[i] == $(this).val()) {
                                this.checked = true;
                                break;
                            }
                        }
                    }
                }
            } else if ('TEXTAREA' == this.tagName) {
                $(this).val(value);
            } else if ('SELECT' == this.tagName) {
                $(this).val(value).trigger("change");
            } else if ('IMG' == this.tagName) {
                $(this).attr('src', '/public/' + value);
            } else {
                $(this).html(value);
            }
        }
    });
    if (typeof fn == 'function') {
        fn.call(this, data);
    }
    $("dataLoaded").trigger('loaded');
}
function formatRepo(repo) {
    if (repo.loading)
        return repo.text;
    var markup =    '<div class="select2-result-repository clearfix">' +
                        '<div id="' + repo.id + '" class="col-sm-12">' + repo.title + '</div>' +
                    '</div>';
    return markup;
}
function formatRepoSelection(repo) {
    return repo.title || repo.text;
}
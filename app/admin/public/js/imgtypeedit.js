$(function(){
    $("#myform").Validform({
            ajaxPost:true,
            tiptype: 4,
            showAllError: true,
            callback:function(data){
                if (typeof data.status != 'undefined' && data.status != 200) {
                    alert('出错了');
                } else if (typeof data == 'string') {
                    alert(data);
                    loadHtmlByAjax(window.pagelast);
                } else {
                    alert(data.mem);
                    loadHtmlByAjax(window.pagelast);
                }
            }
    });
    function bindselect2(){
        $("#pid").select2({
            language: 'zh-CN',
            ajax: {
                url: "/admin/imgtype.json",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    params.page=params.page || 1;
                    if (typeof (params.term) == 'undefined' || params.term == '') {
                        return {
                            field:'title,pid',
                            forselect2:1,
                            status:1,
                            page:params.page,
                            row:'0'
                        };
                    }
                    return {
                        field:'title,pid',
                        forselect2:1,
                        status:1,
                        regsearch:'title',
                        title: params.term,
                        page:params.page,
                        row:'0'
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    temp=params.page===1?[{id:'',title:'无'}]:[];
                    if (data.aaData.length > 0) {
                        for (var key in data.aaData) {
                            var title=data.aaData[key].pid? (data.aaData[key].pid+'>'+data.aaData[key].title):data.aaData[key].title;
                            temp.push({id:data.aaData[key]._id,title:title});
                        }
                    }
                    return {
                        results: temp,
                        pagination: {
                            more: 10
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 0, //最小搜索长度
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
    }
    bindselect2();
    if(id)
        loadData('/admin/imgtype.json?id='+id, $('body'), function(rs){
            var pid=rs.pid;
            if(pid==''){
                $('#pid').html('<option value="">无</option>');
                bindselect2();
            }else{
            $.get('/admin/imgtype.json?getalltitle=1&id='+pid,function(rs2){
                $('#pid').html('<option value="'+pid+'">'+  (rs2.pid==''?'':(rs2.pid+'>')) +rs2.title+'</option>');
                bindselect2();
                });
            }
        });
    
})
    
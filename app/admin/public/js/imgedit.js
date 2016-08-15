$(function(){
    $("#myform").Validform({
            ajaxPost:true,
            tiptype: 4,
            showAllError: true,
            beforeSubmit: function (curform) {
                if ($(".img_list tr").size() < 1) {
                    alert('请上传图片');
                    return false;
                }
                imgs = [];
                size=[];
                width=[];
                height=[];
                $(".img_list tr").each(function () {
                    var size_str=$(this).attr('size');
                    var size_arr=size_str.split('x');
                    imgs.push($(this).attr('path'));
                    size.push(size_str);
                    width.push(size_arr[0]);
                    height.push(size_arr[1]);
                });
                $("[name='imgs']").val(imgs.join(','));
                $("[name='size']").val(size.join(','));
                $("[name='width']").val(height.join(','));
                $("[name='height']").val(width.join(','));
            },
            callback:function(data){
                if (typeof data.status != 'undefined' && data.status != 200) {
                    console.log(data);
                    alert(data.responseJSON.mem || data.responseText);
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
        $("#type_id").select2({
            language: 'zh-CN',
            ajax: {
                url: "/admin/imgtype.json?lowest=1",
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
        loadData('/admin/img.json?id='+id, $('body'), function(rs){
            console.log(rs);
            var type_id=rs.type_id;
            if(type_id==''){
                $('#type_id').html('<option value="">无</option>');
                bindselect2();
            }else{
                $.get('/admin/imgtype.json?getalltitle=1&id='+type_id,function(rs2){
                    $('#type_id').html('<option value="'+type_id+'">'+  (rs2.pid==''?'':(rs2.pid+'>')) +rs2.title+'</option>');
                    bindselect2();
                    });
            }
            showImg(rs);
        });
    $("#src").uploadifive({
            auto: true,
            removeCompleted:true,
            fileSizeLimit: '1MB',
            buttonText: '选择文件',
            uploadScript: '/admin/upload.json',
            fileType: 'image/gif,image/jpeg,image/png',
            fileObjName: 'file',
            formData: {
                from: 'admin',
            },
            onUploadComplete: function (file, data) {
                if (typeof data == 'string') {
                    data = $.parseJSON(data);
                }
                var tr= $(".img_list").append('<tr id="ad" path="' + data.path + '" size="' + data.width + 'x' + data.height + '"><td>' + data.filename + '</td><td>' + data.width + 'x' + data.height + '</td><td><span class="remove" >&#10006</span></td></tr>');
                var img='<img class="img_detail" style="width:200px;height:200px" src="'+data.path+'"/>'
                $('#img_show').append(img);
                if (data.error == 1) {
                    alert('上传失败');
                }
            },
            onError: uploadError
        });
    function uploadError(errorType, file) {
        alert(errorType);
    }
    function showImg(rs){
        var imgs=rs.imgs;
        var size=rs.size;
        for(var key in imgs){
            var filename=imgs[key].split('/');
            filename=filename[filename.length-1];
            var tr= $(".img_list").append('<tr id="ad" path="' + imgs[key] + '" size="' + size[key] + '"><td>' + filename + '</td><td>' + size[key] + '</td><td><span class="remove" >&#10006</span></td></tr>');
            var img='<img class="img_detail" style="width:200px;height:200px" src="'+imgs[key]+'"/>'
            $('#img_show').append(img);
        }
        
    }
    $('.img_list').on('click','.remove',function(){
        var path=$(this).parents('tr').attr('path');
        $(this).parents('tr').remove();
        $('.img_detail[src="'+path+'"]').remove();
    });
    
    
})
    
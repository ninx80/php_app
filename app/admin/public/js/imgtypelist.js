$(function() {
    var ajaxdata;
    $('.example').DataTable( {
            sAjaxSource: '/admin/imgtype.json?request=list',
            editUrl: '/admin/imgtype/edit.html',
            scrollCollapse: true,
            processing: true,
            serverSide: true,
            searching: true,
            rtype: 'img_type',
            minimumResultsForSearch: -1,
            order: [[3, 'asc']],
            initComplete: fninitComplete,
            createdRow: fncreatedRow,
            drawCallback: fndrawCallback,//createdCell:formatTime,defaultContent: '',orderable:false,visible:false
            aoColumns: [
                { 'mData': '_id', orderable: false,visible:false},
                { "mData": "pid",defaultContent: '',orderable:false,visible:false},
                { "mData": "lowest" ,visible:false},
                { "mData": "title",defaultContent: '',orderable:false,createdCell: formatTitle},
                { "mData": "ptitle",defaultContent: '',orderable:false,createdCell: formatParent},
                { "mData": "level" ,visible:false},
                { "mData": "en",defaultContent: ''},
                { "mData": "memo" },
                { "mData": "addtime",defaultContent: '',orderable:false,createdCell: formatDate},
                { "mData": "status" ,createdCell: formatStatus},
                { "mData": "status" ,defaultContent: '',orderable:false,createdCell:formatAction}
            ]
        });
        function formatParent(cell, cellData, rowData, rowIndex, colIndex) {
            if(!cellData){
                cell.innerHTML= '无';
            }
        }
        function formatStatus(cell, cellData, rowData, rowIndex, colIndex) {
            cell.innerHTML = cellData==1?'正常':'删除'
        }
        
        function formatStatus2(cellData) {
            return cellData==1?'正常':'删除'
        }
        
        function formatTitle(cell, cellData, rowData, rowIndex, colIndex) {
            //console.log(cell);
            var _='';
            for(var n=0; n<rowData['level'];n++){
                _+='&nbsp;&nbsp;';
            }
            if(rowData['lowest']==1){
                cell.innerHTML = _+'&nbsp;'+cellData;
            }else
                cell.innerHTML = _+'<a _id="'+rowData['_id']+'">+'+cellData+'</a>';
        }
        function formatTitle2(rowData) {
            console.log(rowData);
            //console.log(cell);
            var _='';
            for(var n=0; n<rowData['level'];n++){
                _+='&nbsp;&nbsp;';
            }
            if(rowData['lowest']==1){
                var str = _+'&nbsp;'+rowData['title'];
            }else
                str = _+'<a _id="'+rowData['_id']+'">+'+rowData['title']+'</a>';
            return str;
        }
        function formatDate2(cellData) {
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
            return cellData;
        }
        function formatAction2(cellData) {
            var html = '';
            html += '<button class="btn m-b-xs btn-sm btn-primary btn-addon btn_edit"><i class="fa fa-edit"></i>修改</button>';
            html += '<button class="btn m-b-xs btn-sm btn-primary btn-addon btn_del"><i class="fa fa-edit"></i>删除</button>';
            return html;
        }
        var ajaxjson={};
        var ajaxdata;
        var ajaxalldata={};
        function pushajaxalldata(rdata){
            for(var key in rdata){
                ajaxalldata[key]=rdata[key];
                if(  !(rdata[key]['children'] instanceof Array)   ){
                    pushajaxalldata(rdata[key]['children']);
                }
            }
        }
        $(".example tbody").on('click', 'td a', function(){
            var html=$(this).html();
            if(html.indexOf('+')==0){
                $(this).html('-'+html.substr(1));
                
            }else{
                $(this).html('+'+html.substr(1));
                //alert($(this).parents('tr').next('tr').length);
                $(this).parent().parent().next('tr').remove();
                return;
            }
            var _id =$(this).attr('_id');
            if(typeof ajaxdata=='undefined'){
                ajaxdata=$(this).parents('.example').dataTable().api().ajax.json().aaData;
                
                for(var i in ajaxdata){
                    ajaxjson[ajaxdata[i]['_id']]=ajaxdata[i];
                }
            }
            pushajaxalldata(ajaxjson);
            tt(this,ajaxalldata[_id]);
            
            });
        function tt(obj,data) {
            var str='<tr><td colspan="11"><table style="width:100%">';
            for(var key in data['children']){
                str+='<tr id="'+data['children'][key]._id+'">';
                str+='<td style="display:none ">'+data['children'][key]._id+
                '</td><td style="display:none">'+data['children'][key].pid+
                '</td><td style="display:none">'+data['children'][key].lewest+
                '</td><td style="width:10%">'+formatTitle2(data['children'][key])+
                '</td><td style="width:10%">'+   (typeof data['children'][key].ptitle=='undefined' || data['children'][key].ptitle==''?'无':data['children'][key].ptitle)           +
                '</td><td style="display:none">'+data['children'][key].lvel+
                '</td><td style="width:10%">'+data['children'][key].en+
                '</td><td style="width:10%">'+data['children'][key].memo+
                '</td><td style="width:10%">'+formatDate2(data['children'][key].addtime)+
                '</td><td style="width:10%">'+formatStatus2(data['children'][key].status)+
                '</td><td style="width:10%">'+formatAction2(data['children'][key].status)+
                '</td>';
                str+='</tr>';
                
            }
            str+='</table></td></tr>';
            var tr = $(obj).closest('tr');
            tr.after(str);
                       //var data =$(this).parents('.example').dataTable().api().ajax.json().aaData;
        }
} );
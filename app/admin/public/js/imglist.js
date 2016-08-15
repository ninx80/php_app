$(function() {
    $('.imglist').DataTable( {
            sAjaxSource: '/admin/img.json',
            editUrl: '/admin/img/edit.html',
            scrollCollapse: true,
            processing: true,
            serverSide: true,
            searching: true,
            rtype: 'img',
            minimumResultsForSearch: -1,
            order: [[0, 'desc']],
            initComplete: fninitComplete,
            createdRow: fncreatedRow,
            drawCallback: fndrawCallback,//createdCell:formatTime,defaultContent: '',orderable:false,visible:false
            aoColumns: [
                { 'mData': '_id', orderable: false, className: 'center', createdCell: formatCheckBox},
                { "mData": "title",defaultContent: '',orderable:false},
                { "mData": "type_id" },
                { "mData": "imgs",defaultContent: '',orderable:false,createdCell:formatImg},
                { "mData": "memo",defaultContent: '' },
                { "mData": "status" ,createdCell:formatStatus},
                { "mData": "addtime",defaultContent: '',createdCell:formatDate},
                { "mData": "status" ,defaultContent: '',orderable:false,createdCell:formatAction}
            ]
        });
        function formatStatus(cell, cellData, rowData, rowIndex, colIndex) {
            cell.innerHTML = cellData==1?'正常':'删除'
        }
} );
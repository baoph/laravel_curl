<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="{{ url('public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
	<script src="{{ url('public/js/jquery.min.js') }}"></script>
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	<script src="{{ url('public/js/bootstrap.min.js') }}"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
	<div class="container-fluid" style="padding: 0px 30px;">
	<h2 style="text-align: center;margin-bottom: 20px">Danh sách data </h2>
			<table class="table table-bordered" id="users-table">
				<input style="float: right;margin-bottom: 20px" class="search_data" type="text" placeholder="Tìm kiếm">
				<thead>
					<tr>
						<th>Địa chỉ</th>
						<th>Giá bán</th>
						<th>Diện tích</th>
						<th>Tầng</th>
						<th>Điện thoại</th>
						<th>Link</th>
						<!-- <th>Website</th> -->
						<th>Lần sửa</th>
						<th>Tình trạng</th>
						<th>Ngày sửa</th>
						<th>Chức năng</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
	</div>

<script>
$(function() {
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route("datatable.getdata") !!}',
        columns: [
            { data: 'address', name: 'address' },
            { data: 'price', name: 'price' },
            { data: 'dientich', name: 'dientich' },
            { data: 'tang', name: 'tang' },
            { data: 'phone', name: 'phone' },
            { data: 'link', name: 'link' },
            { data: 'lansua', name: 'lansua' },
            {data: 'trangthai', name: 'trangthai', orderable: false, searchable: false},
            { data: 'update_at', name: 'update_at' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
            
        ]
    });
});
</script>
</body>
</html>
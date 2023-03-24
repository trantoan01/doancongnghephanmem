<?php
	require "../db_connect.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Quản lý thư viện</title>
		<link rel="stylesheet" type="text/css" href="css/home_style.css" />
	</head>
	<body>
		<div id="allTheThings">
			
			<a href="insert_book.php">
				<input type="button" value="Thêm sách mới" />
			</a><br />

			<a href="update_copies.php">
				<input type="button" value="Cập nhật bản sao của cuốn sách" />
			</a><br />

			<a href="delete_book.php">
				<input type="button" value="Xóa sách" />
			</a><br />

			<a href="display_books.php">
				<input type="button" value="Hiển thị sách có sẵn" />
			</a><br />

			<a href="pending_book_requests.php">
				<input type="button" value="Quản lý sách đang yêu câu" />
			</a><br />

			<a href="pending_registrations.php">
				<input type="button" value="Quản lý đăng ký thành viên" />
			</a><br />

			<a href="update_balance.php">
				<input type="button" value="Cập nhật số dư của thành viên" />
			</a><br />

			<a href="due_handler.php">
				<input type="button" value="Nhắc nhở hôm nay" />
			</a><br /><br />

		</div>
	</body>
</html>
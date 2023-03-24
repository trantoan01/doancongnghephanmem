<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Quản lý thư viện</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/insert_book_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Thêm chi tiết sách mới</legend></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" id="b_isbn" type="number" name="b_isbn" placeholder="ISBN" required />
				</div>
				
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Tên sách" required />
				</div>
				
				<div class="icon">
					<input class="b-author" type="text" name="b_author" placeholder="Tác giả" required />
				</div>
				
				<div>
				<h4>Thể loại</h4>
				
					<p class="cd-select icon">
						<select class="b-category" name="b_category">
							<option>Lịch sử</option>
							<option>Truyện tranh</option>
							<option>Viễn tưởng</option>
							<option>Tiểu sử</option>
							<option>Y học</option>
							<option>Tưởng tượng</option>
							<option>Giáo dục</option>
							<option>Thể thao</option>
							<option>Công nghệ</option>
							<option>Văn học</option>
						</select>
					</p>
				</div>
				
				<div class="icon">
					<input class="b-price" type="number" name="b_price" placeholder="Giá" required />
				</div>
				
				<div class="icon">
					<input class="b-copies" type="number" name="b_copies" placeholder="Số bản sao" required />
				</div>
				
				<br />
				<input class="b-isbn" type="submit" name="b_add" value="Thêm sách" />
		</form>
	<body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT isbn FROM book WHERE isbn = ?;");
			$query->bind_param("s", $_POST['b_isbn']);
			$query->execute();
			
			if(mysqli_num_rows($query->get_result()) != 0)
				echo error_with_field("Sách có ISBN đó đã tồn tại", "b_isbn");
			else
			{
				$query = $con->prepare("INSERT INTO book VALUES(?, ?, ?, ?, ?, ?);");
				$query->bind_param("ssssdd", $_POST['b_isbn'], $_POST['b_title'], $_POST['b_author'], $_POST['b_category'], $_POST['b_price'], $_POST['b_copies']);
				
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn't add book"));
				echo success("Sách mới đã được thêm vào");
			}
		}
	?>
</html>
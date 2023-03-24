<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Quản lý thư viện</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_book_requests_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM pending_book_requests;");
			$query->execute();
			$result = $query->get_result();;
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Không có yêu cầu đang chờ xử lý</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>Yêu cầu sách chờ xử lý</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Tên tài khoản<hr></th>
							<th>Sách<hr></th>
							<th>Thời gian<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>";
					echo "<td>
							<label class='control control--checkbox'>
								<input type='checkbox' name='cb_".$i."' value='".$row[0]."' />
								<div class='control__indicator'></div>
							</label>
						</td>";
					for($j=1; $j<4; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><div style='float: right;'>";
				echo "<input type='submit' value='Từ chối yêu cầu' name='l_reject' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Cho phép' name='l_grant'/>";
				echo "</div>";
				echo "</form>";
			}
			
			$header = 'From: <toan@library.com>' . "\r\n";
			
			if(isset($_POST['l_grant']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$request_id =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$query = $con->prepare("INSERT INTO book_issue_log(member, book_isbn) VALUES(?, ?);");
						$query->bind_param("ss", $member, $isbn);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t issue book"));
						$requests++;
						
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Book has been issued";
						
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						
						$query = $con->prepare("SELECT due_date FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
						$query->bind_param("ss", $member, $isbn);
						$query->execute();
						$due_date = mysqli_fetch_array($query->get_result())[0];
						$message = "The book '".$title."' with ISBN ".$isbn." has been issued to your account. The due date to return the book is ".$due_date.".";
						
						mail($to, $subject, $message, $header);
					}
				}
				if($requests > 0)
					echo success("Cấp thành công!".$requests." Yêu cầu");
				else
					echo error_without_field("Không có yêu cầu nào được chọn");
			}
			
			if(isset($_POST['l_reject']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$requests++;
						$request_id =  $_POST['cb_'.$i];
						
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Book issue rejected";
						
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						$message = "Your request for issuing the book '".$title."' with ISBN ".$isbn." has been rejected. You can request the book again or visit a librarian for further information.";
						
						$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
						$query->bind_param("d", $request_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						
						mail($to, $subject, $message, $header);
					}
				}
				if($requests > 0)
					echo success("Đã xóa thành công ".$requests." Yêu cầu");
				else
					echo error_without_field("Không có yêu cầu được chọn");
			}
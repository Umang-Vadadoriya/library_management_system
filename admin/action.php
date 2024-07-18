<?php

//action.php

include '../database_connection.php';

if (isset($_POST["action"])) {
	if ($_POST["action"] == 'search_book_name') {
		$query = "
		SELECT book_isbn_number, book_name FROM lms_book 
		WHERE book_name LIKE '%" . $_POST["request"] . "%' 
		AND book_status = 'Enable'
		";

		$result = mysqli_query($conn, $query);

		$data = array();

		foreach ($result as $row) {
			$data[] = array(
				'isbn_no' => $row["book_isbn_number"],
				'book_name' => (str_ireplace($_POST["request"], '<b>' . $_POST["request"] . '</b>', $row['book_name']))
			);
		}
		echo json_encode($data);
	}

	if ($_POST["action"] == 'search_user_name') {
		$query = "
		SELECT user_unique_id, user_name FROM lms_user 
		WHERE user_name LIKE '%" . $_POST["request"] . "%' 
		AND user_status = 'Enable'
		";

		$result = mysqli_query($conn, $query);

		$data = array();

		foreach ($result as $row) {
			$data[] = array(
				'user_unique_id' => $row["user_unique_id"],
				'user_name' => str_ireplace($_POST["request"], '<b>' . $_POST["request"] . '</b>', $row["user_name"])
			);
		}

		echo json_encode($data);
	}
}

?>
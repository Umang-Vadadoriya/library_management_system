<?php

//function.php

function base_url()
{
	return 'http://localhost/123 Umang/library_management_system/';
}

function is_admin_login()
{
	if (isset($_SESSION['admin_id'])) {
		return true;
	}
	return false;
}

function is_user_login()
{
	if (isset($_SESSION['user_id'])) {
		return true;
	}
	return false;
}

function get_one_day_fines($conn)
{
	$output = 0;
	$query = "
	SELECT library_one_day_fine FROM lms_setting 
	LIMIT 1
	";
	$result = mysqli_query($conn, $query);
	foreach ($result as $row) {
		$output = $row["library_one_day_fine"];
	}
	return $output;
}

function get_book_issue_limit_per_user($conn)
{
	$output = '';
	$query = "
	SELECT library_issue_total_book_per_user FROM lms_setting 
	LIMIT 1
	";
	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$output = $row["library_issue_total_book_per_user"];
	}
	return $output;
}

function get_total_book_issue_per_user($conn, $user_unique_id)
{
	$output = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE user_id = '" . $user_unique_id . "' 
	AND book_issue_status = 'Issue'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$output = $row["Total"];
	}
	return $output;
}

function get_total_book_issue_day($conn)
{
	$output = 0;

	$query = "
	SELECT library_total_book_issue_day FROM lms_setting 
	LIMIT 1
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$output = $row["library_total_book_issue_day"];
	}
	return $output;
}

function convert_data($string, $action = 'encrypt')
{
	$encrypt_method = "AES-256-CBC";
	$secret_key = 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
	$secret_iv = '5fgf5HJ5g27'; // user define secret key
	$key = hash('sha256', $secret_key);
	$iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
	if ($action == 'encrypt') {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
	} else if ($action == 'decrypt') {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}
	return $output;
}

function fill_author($conn)
{
	$query = "
	SELECT author_name FROM lms_author 
	WHERE author_status = 'Enable' 
	ORDER BY author_name ASC
	";

	$result = mysqli_query($conn, $query);

	$output = '<option value="">Select Author</option>';

	foreach ($result as $row) {
		$output .= '<option value="' . $row["author_name"] . '">' . $row["author_name"] . '</option>';
	}

	return $output;
}

function fill_category($conn)
{
	$query = "
	SELECT category_name FROM lms_category 
	WHERE category_status = 'Enable' 
	ORDER BY category_name ASC
	";

	$result = mysqli_query($conn, $query);

	$output = '<option value="">Select Category</option>';

	foreach ($result as $row) {
		$output .= '<option value="' . $row["category_name"] . '">' . $row["category_name"] . '</option>';
	}

	return $output;
}

function fill_location_rack($conn)
{
	$query = "
	SELECT location_rack_name FROM lms_location_rack 
	WHERE location_rack_status = 'Enable' 
	ORDER BY location_rack_name ASC
	";

	$result = mysqli_query($conn, $query);

	$output = '<option value="">Select Location Rack</option>';

	foreach ($result as $row) {
		$output .= '<option value="' . $row["location_rack_name"] . '">' . $row["location_rack_name"] . '</option>';
	}

	return $output;
}

function Count_total_issue_book_number($conn)
{
	$total = 0;

	$query = "SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_returned_book_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE book_issue_status = 'Return'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_not_returned_book_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE book_issue_status = 'Not Return'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_fines_received($conn)
{
	$total = 0;

	$query = "
	SELECT SUM(book_fines) AS Total FROM lms_issue_book 
	WHERE book_issue_status = 'Return'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_book_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(book_id) AS Total FROM lms_book 
	WHERE book_status = 'Enable'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_author_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(author_id) AS Total FROM lms_author 
	WHERE author_status = 'Enable'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_category_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(category_id) AS Total FROM lms_category 
	WHERE category_status = 'Enable'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}
	return $total;
}

function Count_total_location_rack_number($conn)
{
	$total = 0;

	$query = "
	SELECT COUNT(location_rack_id) AS Total FROM lms_location_rack 
	WHERE location_rack_status = 'Enable'
	";

	$result = mysqli_query($conn, $query);

	foreach ($result as $row) {
		$total = $row["Total"];
	}

	return $total;
}

?>
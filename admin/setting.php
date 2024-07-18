<?php

//setting.php

include '../database_connection.php';

include '../function.php';

if (!is_admin_login()) {
	header('location:../admin_login.php');
}

$message = '';

if (isset($_POST['edit_setting'])) {
	$formdata = array();

	if (empty($_POST['library_name'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Library Name Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_name'] = trim($_POST['library_name']);
	}

	if (empty($_POST['library_address'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Library Address Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_address'] = trim($_POST['library_address']);
	}

	if (empty($_POST['library_contact_number'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Library Contact Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_contact_number'] = trim($_POST['library_contact_number']);
	}

	if (empty($_POST['library_email_address']) || !filter_var($_POST['library_email_address'], FILTER_VALIDATE_EMAIL)) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Valid Email Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_email_address'] = trim($_POST['library_email_address']);
	}

	if (empty($_POST['library_total_book_issue_day'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Issue Per Day Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_total_book_issue_day'] = trim($_POST['library_total_book_issue_day']);
	}

	if (empty($_POST['library_one_day_fine'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Fine Per Day Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_one_day_fine'] = trim($_POST['library_one_day_fine']);
	}

	if (empty($_POST['library_issue_total_book_per_user'])) {
		$message .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Limit Per User Required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$formdata['library_issue_total_book_per_user'] = trim($_POST['library_issue_total_book_per_user']);
	}

	if ($message == "") {

		$query = "
		UPDATE lms_setting 
        SET 
		library_name = '" . $formdata['library_name'] . "',
        library_address = '" . $formdata['library_address'] . "', 
        library_contact_number = '" . $formdata['library_contact_number'] . "', 
        library_email_address = '" . $formdata['library_email_address'] . "', 
        library_total_book_issue_day = " . $formdata['library_total_book_issue_day'] . ", 
        library_one_day_fine = " . $formdata['library_one_day_fine'] . ", 
        library_issue_total_book_per_user = " . $formdata['library_issue_total_book_per_user'] . "
		";

		$flag = mysqli_query($conn, $query);
		if ($flag) {
			$message = '
			<div class="alert alert-success alert-dismissible fade show" role="alert">Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			';
		} else {
			$message = '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">Something Went Wrong <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			';
		}
	}

}

$query = "
SELECT * FROM lms_setting 
LIMIT 1
";

$result = mysqli_query($conn, $query);

include '../header.php';

?>

<div class="container-fluid px-4">
	<h1 class="mt-4">Setting</h1>

	<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
		<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
		<li class="breadcrumb-item active">Setting</a></li>
	</ol>
	<?php

	if ($message != '') {
		echo $message;
	}

	?>
	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-user-edit"></i> Library Setting
		</div>
		<div class="card-body">

			<form method="post">
				<?php
				foreach ($result as $row) {
					?>
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">Library Name</label>
								<input type="text" name="library_name" id="library_name" class="form-control"
									value="<?php echo $row['library_name']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">Address</label>
								<textarea name="library_address" id="library_address"
									class="form-control"><?php echo $row["library_address"]; ?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Contact Number</label>
								<input type="text" name="library_contact_number" id="library_contact_number"
									class="form-control" value="<?php echo $row['library_contact_number']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Email Address</label>
								<input type="text" name="library_email_address" id="library_email_address"
									class="form-control" value="<?php echo $row['library_email_address']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Book Return Day Limit</label>
								<input type="number" name="library_total_book_issue_day" id="library_total_book_issue_day"
									class="form-control" value="<?php echo $row['library_total_book_issue_day']; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Book Late Return One Day Fine</label>
								<input type="number" name="library_one_day_fine" id="library_one_day_fine"
									class="form-control" value="<?php echo $row['library_one_day_fine']; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<label class="form-label">Per User Book Issue Limit</label>
							<input type="number" name="library_issue_total_book_per_user"
								id="library_issue_total_book_per_user" class="form-control"
								value="<?php echo $row['library_issue_total_book_per_user']; ?>" />
						</div>
					</div>
					<div class="mt-4 mb-0">
						<input type="submit" name="edit_setting" class="btn btn-primary" value="Save" />
					</div>
					<?php
				}
				?>
			</form>

		</div>
	</div>
</div>

<?php

include '../footer.php';

?>
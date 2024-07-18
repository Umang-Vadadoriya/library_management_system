<?php
//author.php

include '../database_connection.php';

include '../function.php';

if (!is_admin_login()) {
	header('location:../admin_login.php');
}

$message = '';

$error = '';

// Add Author
if (isset($_POST["add_author"])) {
	$formdata = array();

	if (empty($_POST["author_name"])) {
		$error .= '<li>Author Name is required</li>';
	} else {
		$formdata["author_name"] = trim($_POST["author_name"]);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM lms_author 
        WHERE author_name = '" . $formdata["author_name"] . "'";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Author Name Already Exists</li>';
		} else {
			$query = "
			INSERT INTO lms_author 
            (author_name, author_status, author_created_on) 
            VALUES ('" . $formdata["author_name"] . "', 'Enable', '" . date('Y-m-d H:i:s') . "')";

			mysqli_query($conn, $query);

			header('location:author.php?msg=add');
		}
	}
}

// Update Author
if (isset($_POST["edit_author"])) {
	$formdata = array();

	if (empty($_POST["author_name"])) {
		$error .= '<li>Author Name is required</li>';
	} else {
		$formdata['author_name'] = trim($_POST['author_name']);
	}

	if ($error == '') {
		$author_id = $_POST['author_id'];

		$query = "
		SELECT * FROM lms_author 
        WHERE author_name = '" . $formdata['author_name'] . "' 
        AND author_id != '" . $author_id . "'";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Author Name Already Exists</li>';
		} else {

			$query = "
			UPDATE lms_author 
            SET author_name = '" . $formdata['author_name'] . "', 
            author_updated_on = '" . date('Y-m-d H:i:s') . "'  
            WHERE author_id = " . $author_id . "
			";

			mysqli_query($conn, $query);

			header('location:author.php?msg=edit');
		}
	}
}

// Change Status
if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'switch') {
	$author_id = $_GET["code"];

	$status = $_GET["status"];

	$query = "
	 UPDATE lms_author 
    SET author_status = '" . $status . "', 
    author_updated_on = '" . date('Y-m-d H:i:s') . "'
    WHERE author_id = $author_id
	";

	mysqli_query($conn, $query);

	header('location:author.php?msg=' . strtolower($status) . '');
}

// Delete Record
if (isset($_GET["action"], $_GET["code"]) && $_GET["action"] == 'delete') {
	$author_id = $_GET["code"];

	$query = "delete from lms_author where author_id=" . $author_id;
	mysqli_query($conn, $query);
	header('location:author.php?msg=delete');
}

$query = "
	SELECT * FROM lms_author 
    ORDER BY author_name ASC
";

$result_show = mysqli_query($conn, $query);

include '../header.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Author Management</h1>

	<?php

	if (isset($_GET["action"])) {

		// Add Author Design
		if ($_GET["action"] == "add") {
			?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="author.php">Author Management</a></li>
				<li class="breadcrumb-item active">Add Author</li>
			</ol>

			<div class="row">
				<div class="col-md-6">
					<?php

					if ($error != '') {
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
					}

					?>
					<div class="card mb-4">
						<div class="card-header">
							<i class="fas fa-user-plus"></i> Add New Author
						</div>
						<div class="card-body">
							<form method="post">
								<div class="mb-3">
									<label class="form-label">Author Name</label>
									<input type="text" name="author_name" id="author_name" class="form-control" />
								</div>
								<div class="mt-4 mb-0">
									<input type="submit" name="add_author" class="btn btn-success" value="Add" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>


			<!-- Edit Author Design -->
			<?php
		} else if ($_GET["action"] == 'edit') {
			$author_id = $_GET["code"];

			if ($author_id > 0) {
				$query = "
				SELECT * FROM lms_author 
                WHERE author_id = '$author_id'
				";

				$author_result = mysqli_query($conn, $query);

				foreach ($author_result as $author_row) {
					?>

						<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
							<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="author.php">Author Management</a></li>
							<li class="breadcrumb-item active">Edit Author</li>
						</ol>

						<div class="row">
							<div class="col-md-6">
								<div class="card mb-4">
									<div class="card-header">
										<i class="fas fa-user-edit"></i> Edit Author Details
									</div>
									<div class="card-body">
										<form method="post">
											<div class="mb-3">
												<label class="form-label">Author Name</label>
												<input type="text" name="author_name" id="author_name" class="form-control"
													value="<?php echo $author_row['author_name']; ?>" />
											</div>
											<div class="mt-4 mb-0">
												<input type="hidden" name="author_id" value="<?php echo $_GET['code']; ?>" />
												<input type="submit" name="edit_author" class="btn btn-primary" value="Edit" />
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

					<?php
				}
			}
		}
	} else {

		?>
		<!-- Breadcrumb -->
		<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
			<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
			<li class="breadcrumb-item active">Author Management</li>
		</ol>

		<!-- Popup For Better UI -->
		<?php

		if (isset($_GET["msg"])) {
			if ($_GET["msg"] == 'add') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Author Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'edit') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Author Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'delete') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Book Data Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET["msg"] == 'disable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Author Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'enable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Author Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>

		<!-- Display All Author UI-->
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Author Management
					</div>
					<div class="col col-md-6" align="right">
						<a href="author.php?action=add" class="btn btn-success btn-sm">Add</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Author Name</th>
							<th>Status</th>
							<th>Created On</th>
							<th>Updated On</th>
							<th>Action</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Author Name</th>
							<th>Status</th>
							<th>Created On</th>
							<th>Updated On</th>
							<th>Action</th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if (mysqli_num_rows($result_show) > 0) {
							foreach ($result_show as $row) {
								$author_status = '';
								if ($row['author_status'] == 'Enable') {
									$author_status = '<div class="badge bg-success">Enable</div>';
									$author_status_btn = '<button type="button" name="switch_button" class="btn btn-warning btn-sm" onclick="switch_data(`' . $row["author_id"] . '`, `' . $row["author_status"] . '`)">Disable</button>';
								} else {
									$author_status = '<div class="badge bg-danger">Disable</div>';
									$author_status_btn = '<button type="button" name="switch_button" class="btn btn-success btn-sm" onclick="switch_data(`' . $row["author_id"] . '`, `' . $row["author_status"] . '`)">Enable</button>';
								}

								echo '
						<tr>
							<td>' . $row["author_name"] . '</td>
							<td>' . $author_status . '</td>
							<td>' . $row["author_created_on"] . '</td>
							<td>' . $row["author_updated_on"] . '</td>
							<td>
								<a href="author.php?action=edit&code=' . $row["author_id"] . '" class="btn btn-sm btn-primary">Edit</a>
								' . $author_status_btn . '
								<a class="btn btn-sm btn-danger" onclick="delete_data(`' . $row["author_id"] . '`)">Delete</a>
							</td>
						</tr>
						';
							}
						} else {
							echo '
					<tr>
						<td colspan="4" class="text-center">No Data Found</td>
					</tr>
					';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Script To Switch Status -->
		<script>

			function switch_data(code, status) {
				var new_status = 'Enable';

				if (status == 'Enable') {
					new_status = 'Disable';
				}

				if (confirm("Are you sure you want to " + new_status + " this Author?")) {
					window.location.href = "author.php?action=switch&code=" + code + "&status=" + new_status + "";
				}
			}

			function delete_data(code) {


				if (confirm("Are you sure you want to Delete this Author?")) {
					window.location.href = "author.php?action=delete&code=" + code;
				}
			}

		</script>

		<?php

	}

	?>
</div>

<?php

include '../footer.php';

?>
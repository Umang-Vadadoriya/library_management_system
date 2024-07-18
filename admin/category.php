<?php

//category.php

include '../database_connection.php';

include '../function.php';

if (!is_admin_login()) {
	header('location:../admin_login.php');
}

$message = '';

$error = '';

// Add New Category
if (isset($_POST['add_category'])) {
	$formdata = array();

	if (empty($_POST['category_name'])) {
		$error .= '<li>Category Name is required</li>';
	} else {
		$formdata['category_name'] = trim($_POST['category_name']);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM lms_category 
        WHERE category_name = '" . $formdata['category_name'] . "'
		";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Category Name Already Exists</li>';
		} else {

			$query = "
			INSERT INTO lms_category 
            (category_name, category_status, category_created_on) 
            VALUES ('" . $formdata['category_name'] . "', 'Enable', '" . date('Y-m-d H:i:s') . "')
			";

			mysqli_query($conn, $query);

			header('location:category.php?msg=add');
		}
	}
}

// Update Category
if (isset($_POST["edit_category"])) {
	$formdata = array();

	if (empty($_POST["category_name"])) {
		$error .= '<li>Category Name is required</li>';
	} else {
		$formdata['category_name'] = $_POST['category_name'];
	}

	if ($error == '') {
		$category_id = $_POST['category_id'];

		$query = "
		SELECT * FROM lms_category 
        WHERE category_name = '" . $formdata['category_name'] . "' 
        AND category_id != '" . $category_id . "'
		";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Category Name Already Exists</li>';
		} else {

			$query = "
			UPDATE lms_category 
            SET category_name = '" . $formdata['category_name'] . "', 
            category_updated_on = '" . date('Y-m-d H:i:s') . "'  
            WHERE category_id = " . $category_id;

			mysqli_query($conn, $query);

			header('location:category.php?msg=edit');
		}
	}
}

// Switch Category Status 
if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'switch') {
	$category_id = $_GET["code"];
	$status = $_GET["status"];

	$query = "
	UPDATE lms_category 
    SET category_status = '$status', 
    category_updated_on = '" . date('Y-m-d H:i:s') . "'
    WHERE category_id = " . $category_id;

	mysqli_query($conn, $query);

	header('location:category.php?msg=' . strtolower($status) . '');
}

// Delete Category
if (isset($_GET["action"], $_GET["code"]) && $_GET["action"] == 'delete') {
	$category_id = $_GET["code"];
	$query = "delete from lms_category where category_id=" . $category_id;
	mysqli_query($conn, $query);
	header('location:category.php?msg=delete');
}


// Show All Category
$query = "
SELECT * FROM lms_category 
    ORDER BY category_name ASC
";

$result_show = mysqli_query($conn, $query);

include '../header.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Category Management</h1>
	<?php

	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'add') {
			?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
				<li class="breadcrumb-item active">Add Category</li>
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
							<i class="fas fa-user-plus"></i> Add New Category
						</div>
						<div class="card-body">

							<form method="POST">

								<div class="mb-3">
									<label class="form-label">Category Name</label>
									<input type="text" name="category_name" id="category_name" class="form-control" />
								</div>

								<div class="mt-4 mb-0">
									<input type="submit" name="add_category" value="Add" class="btn btn-success" />
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>


			<?php
		} else if ($_GET["action"] == 'edit') {
			$category_id = $_GET["code"];

			if ($category_id > 0) {
				$query = "
				SELECT * FROM lms_category 
                WHERE category_id = '$category_id'
				";

				$category_result = mysqli_query($conn, $query);

				foreach ($category_result as $category_row) {
					?>

						<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
							<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
							<li class="breadcrumb-item active">Edit Category</li>
						</ol>
						<div class="row">
							<div class="col-md-6">
								<div class="card mb-4">
									<div class="card-header">
										<i class="fas fa-user-edit"></i> Edit Category Details
									</div>
									<div class="card-body">

										<form method="post">

											<div class="mb-3">
												<label class="form-label">Category Name</label>
												<input type="text" name="category_name" id="category_name" class="form-control"
													value="<?php echo $category_row['category_name']; ?>" />
											</div>

											<div class="mt-4 mb-0">
												<input type="hidden" name="category_id" value="<?php echo $_GET['code']; ?>" />
												<input type="submit" name="edit_category" class="btn btn-primary" value="Edit" />
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
		<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
			<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
			<li class="breadcrumb-item active">Category Management</li>
		</ol>

		<?php

		if (isset($_GET['msg'])) {
			if ($_GET['msg'] == 'add') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Category Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'edit') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET['msg'] == 'delete') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Data Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if ($_GET["msg"] == 'disable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET['msg'] == 'enable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Category Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>

		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Category Management
					</div>
					<div class="col col-md-6" align="right">
						<a href="category.php?action=add" class="btn btn-success btn-sm">Add</a>
					</div>
				</div>
			</div>
			<div class="card-body">

				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Category Name</th>
							<th>Status</th>
							<th>Created On</th>
							<th>Updated On</th>
							<th>Action</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Category Name</th>
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
								$category_status = '';
								if ($row['category_status'] == 'Enable') {
									$category_status = '<div class="badge bg-success">Enable</div>';
									$category_status_btn = '<button type="button" name="switch_button" class="btn btn-warning btn-sm" onclick="switch_data(`' . $row["category_id"] . '`, `' . $row["category_status"] . '`)">Disable</button>';
								} else {
									$category_status = '<div class="badge bg-danger">Disable</div>';
									$category_status_btn = '<button type="button" name="switch_button" class="btn btn-success btn-sm" onclick="switch_data(`' . $row["category_id"] . '`, `' . $row["category_status"] . '`)">Enable</button>';
								}

								echo '
						<tr>
							<td>' . $row["category_name"] . '</td>
							<td>' . $category_status . '</td>
							<td>' . $row["category_created_on"] . '</td>
							<td>' . $row["category_updated_on"] . '</td>
							<td>
								<a href="category.php?action=edit&code=' . $row["category_id"] . '" class="btn btn-sm btn-primary">Edit</a>
								' . $category_status_btn . '
								<a class="btn btn-sm btn-danger" onclick="delete_data(`' . $row["category_id"] . '`)">Delete</a>
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

				<script>

					function switch_data(code, status) {
						var new_status = 'Enable';

						if (status == 'Enable') {
							new_status = 'Disable';
						}

						if (confirm("Are you sure you want to " + new_status + " this Category?")) {
							window.location.href = "category.php?action=switch&code=" + code + "&status=" + new_status + "";
						}
					}

					function delete_data(code) {

						if (confirm("Are you sure you want to Delete this Category?")) {
							window.location.href = "category.php?action=delete&code=" + code;
						}
					}

				</script>

			</div>
		</div>
		<?php
	}
	?>

</div>

<?php

include '../footer.php';

?>
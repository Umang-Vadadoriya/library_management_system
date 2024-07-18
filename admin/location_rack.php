<?php

//location_rack.php

include '../database_connection.php';

include '../function.php';

if (!is_admin_login()) {
	header('location:../admin_login.php');
}

$message = '';

$error = '';

if (isset($_POST["add_location_rack"])) {
	$formdata = array();

	if (empty($_POST["location_rack_name"])) {
		$error .= '<li>Location Rack Name is required</li>';
	} else {
		$formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
	}

	if ($error == '') {
		$query = "
		SELECT * FROM lms_location_rack 
        WHERE location_rack_name = '" . $formdata['location_rack_name'] . "'
		";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Location Rack Name Already Exists</li>';
		} else {
			$query = "
			INSERT INTO lms_location_rack 
            (location_rack_name, location_rack_status, location_rack_created_on) 
            VALUES ('" . $formdata['location_rack_name'] . "', 'Enable', '" . date('Y-m-d H:i:s') . "')
			";

			mysqli_query($conn, $query);

			header('location:location_rack.php?msg=add');
		}
	}
}

if (isset($_POST["edit_location_rack"])) {
	$formdata = array();

	if (empty($_POST["location_rack_name"])) {
		$error .= '<li>Location Rack Name is required</li>';
	} else {
		$formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
	}

	if ($error == '') {
		$location_rack_id = $_POST["location_rack_id"];

		$query = "
		SELECT * FROM lms_location_rack 
	        WHERE location_rack_name = '" . $formdata['location_rack_name'] . "' 
	        AND location_rack_id != '" . $location_rack_id . "'
		";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$error = '<li>Location Rack Name Already Exists</li>';
		} else {
			$query = "
			UPDATE lms_location_rack 
	            SET location_rack_name = '" . $formdata['location_rack_name'] . "', 
	            location_rack_updated_on = '" . date('Y-m-d H:i:s') . "' 
	            WHERE location_rack_id = " . $location_rack_id;

			mysqli_query($conn, $query);

			header('location:location_rack.php?msg=edit');
		}
	}
}

if (isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'switch') {
	$location_rack_id = $_GET["code"];

	$status = $_GET["status"];

	$query = "
	UPDATE lms_location_rack 
    SET location_rack_status = '$status', 
    location_rack_updated_on = '" . date('Y-m-d H:i:s') . "'
    WHERE location_rack_id = " . $location_rack_id;

	mysqli_query($conn, $query);

	header('location:location_rack.php?msg=' . strtolower($status) . '');

}

// Delete Location Rack
if (isset($_GET["action"], $_GET["code"]) && $_GET["action"] == 'delete') {
	$location_rack_id = $_GET["code"];

	$query = "delete from lms_location_rack where location_rack_id=" . $location_rack_id;
	mysqli_query($conn, $query);
	header('location:location_rack.php?msg=delete');
}


$query = "
	SELECT * FROM lms_location_rack 
    ORDER BY location_rack_name ASC
";

$result_show_rack = mysqli_query($conn, $query);

include '../header.php';

?>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Location Rack Management</h1>
	<?php

	if (isset($_GET["action"])) {
		if ($_GET["action"] == 'add') {
			?>

			<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
				<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="category.php">Location Rack Management</a></li>
				<li class="breadcrumb-item active">Add Location Rack</li>
			</ol>

			<div class="row">
				<div class="col-md-6">
					<?php

					if ($error != '') {
						echo '
				<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
				';
					}

					?>
					<div class="card mb-4">
						<div class="card-header">
							<i class="fas fa-user-plus"></i> Add New Location Rack
						</div>
						<div class="card-body">
							<form method="post">
								<div class="mb-3">
									<label class="form-label">Location Rack Name</label>
									<input type="text" name="location_rack_name" id="location_rack_name" class="form-control" />
								</div>
								<div class="mt-4 mb-0">
									<input type="submit" name="add_location_rack" class="btn btn-success" value="Add" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php
		} else if ($_GET["action"] == 'edit') {
			$location_rack_id = $_GET["code"];

			if ($location_rack_id > 0) {
				$query = "
				SELECT * FROM lms_location_rack 
                WHERE location_rack_id = '$location_rack_id'
				";

				$location_rack_result = mysqli_query($conn, $query);

				foreach ($location_rack_result as $location_rack_row) {
					?>

						<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
							<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="location_rack.php">Location Rack Management</a></li>
							<li class="breadcrumb-item active">Edit Location Rack</li>
						</ol>
						<div class="row">
							<div class="col-md-6">
								<div class="card mb-4">
									<div class="card-header">
										<i class="fas fa-user-edit"></i> Edit Location Rack Details
									</div>
									<div class="card-body">
										<form method="post">
											<div class="mb-3">
												<label class="form-label">Location Rack Name</label>
												<input type="text" name="location_rack_name" id="location_rack_name" class="form-control"
													value="<?php echo $location_rack_row["location_rack_name"]; ?>" />
											</div>
											<div class="mt-4 mb-0">
												<input type="hidden" name="location_rack_id" value="<?php echo $_GET['code']; ?>" />
												<input type="submit" name="edit_location_rack" class="btn btn-primary" value="Edit" />
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
			<li class="breadcrumb-item active">Location Rack Management</li>
		</ol>
		<?php

		if (isset($_GET["msg"])) {
			if ($_GET["msg"] == 'add') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Location Rack Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'edit') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET['msg'] == 'delete') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Data Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'disable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}

			if ($_GET["msg"] == 'enable') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Location Rack Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}

		?>
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"></i> Location Rack Management
					</div>
					<div class="col col-md-6" align="right">
						<a href="location_rack.php?action=add" class="btn btn-success btn-sm">Add</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table id="datatablesSimple">
					<thead>
						<tr>
							<th>Location Rack Name</th>
							<th>Status</th>
							<th>Created On</th>
							<th>Updated On</th>
							<th>Action</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Location Rack Name</th>
							<th>Status</th>
							<th>Created On</th>
							<th>Updated On</th>
							<th>Action</th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if (mysqli_num_rows($result_show_rack) > 0) {
							foreach ($result_show_rack as $row) {
								$location_rack_status = '';
								if ($row['location_rack_status'] == 'Enable') {
									$location_rack_status = '<div class="badge bg-success">Enable</div>';
									$location_rack_status_btn = '<button type="button" name="switch_button" class="btn btn-warning btn-sm" onclick="switch_data(`' . $row["location_rack_id"] . '`, `' . $row["location_rack_status"] . '`)">Disable</button>';
								} else {
									$location_rack_status = '<div class="badge bg-danger">Disable</div>';
									$location_rack_status_btn = '<button type="button" name="switch_button" class="btn btn-success btn-sm" onclick="switch_data(`' . $row["location_rack_id"] . '`, `' . $row["location_rack_status"] . '`)">Enable</button>';
								}

								echo '
						<tr>
							<td>' . $row["location_rack_name"] . '</td>
							<td>' . $location_rack_status . '</td>
							<td>' . $row["location_rack_created_on"] . '</td>
							<td>' . $row["location_rack_updated_on"] . '</td>
							<td>
								<a href="location_rack.php?action=edit&code=' . $row["location_rack_id"] . '" class="btn btn-sm btn-primary">Edit</a>
								' . $location_rack_status_btn . '
								<a class="btn btn-sm btn-danger" onclick="delete_data(`' . $row["location_rack_id"] . '`)">Delete</a>
							</td>
						</tr>
						';

							}
						} else {
							echo '
					<tr>
						<td colspan="5" class="text-center">No Data Found</td>
					</tr>
					';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<script>

			function switch_data(code, status) {
				var new_status = 'Enable';

				if (status == 'Enable') {
					new_status = 'Disable';
				}

				if (confirm("Are you sure you want to " + new_status + " this Category?")) {
					window.location.href = "location_rack.php?action=switch&code=" + code + "&status=" + new_status + ""
				}
			}

			function delete_data(code) {

				if (confirm("Are you sure you want to Delete this Location Rack?")) {
					window.location.href = "location_rack.php?action=delete&code=" + code;
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
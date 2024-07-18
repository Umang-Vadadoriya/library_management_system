<?php

//user_registration.php

include 'database_connection.php';

include 'function.php';

if (is_user_login()) {
	header('location:issue_book_details.php');
}

$message = '';

$success = '';

if (isset($_POST["register_button"])) {
	$formdata = array();

	if (empty($_POST["user_email_address"])) {
		$message .= '<li>Email Address is required</li>';
	} else {
		if (!filter_var($_POST["user_email_address"], FILTER_VALIDATE_EMAIL)) {
			$message .= '<li>Invalid Email Address</li>';
		} else {
			$formdata['user_email_address'] = trim($_POST['user_email_address']);
		}
	}

	if (empty($_POST["user_password"])) {
		$message .= '<li>Password is required</li>';
	} else {
		$formdata['user_password'] = trim($_POST['user_password']);
	}

	if (empty($_POST['user_name'])) {
		$message .= '<li>User Name is required</li>';
	} else {
		$formdata['user_name'] = trim($_POST['user_name']);
	}

	if (empty($_POST['user_address'])) {
		$message .= '<li>User Address Detail is required</li>';
	} else {
		$formdata['user_address'] = trim($_POST['user_address']);
	}

	if (empty($_POST['user_contact_no'])) {
		$message .= '<li>User Contact Number Detail is required</li>';
	} else {
		$formdata['user_contact_no'] = trim($_POST['user_contact_no']);
	}

	if (!empty($_FILES['user_profile']['name'])) {
		$img_name = $_FILES['user_profile']['name'];
		$tmp_name = $_FILES['user_profile']['tmp_name'];
		$fileinfo = @getimagesize($tmp_name);


		$width = $fileinfo[0];
		$height = $fileinfo[1];

		$image_size = $_FILES['user_profile']['size'];

		$img_explode = explode(".", $img_name);

		$img_only_name = $img_explode[0];
		$img_only_ext = strtolower(end($img_explode));

		$extensions = ["jpeg", "png", "jpg"];

		if (in_array($img_only_ext, $extensions)) {
			if ($image_size <= 2000000) {
				if ($width == $height) {

					$new_img_name = md5($img_only_name) . '-' . rand(100000, 999999) . '-' . time() . '.' . $img_only_ext;
					if (move_uploaded_file($tmp_name, "upload/" . $new_img_name)) {
						$formdata['user_profile'] = $new_img_name;
					}

				} else {
					$message .= '<li>Image dimension should be same.</li>';
				}
			} else {
				$message .= '<li>Image size exceeds 2MB</li>';
			}
		} else {
			$message .= '<li>Invalid Image File</li>';
		}
	} else {
		$message .= '<li>Please Select Profile Image</li>';
	}

	if ($message == '') {

		$query = "
		SELECT * FROM lms_user 
        WHERE user_email_address = '" . $formdata['user_email_address'] . "'
		";

		$result_verify_email = mysqli_query($conn, $query);

		if (mysqli_num_rows($result_verify_email) > 0) {
			$message = '<li>Email Already Register</li>';
		} else {

			$user_unique_id = 'U' . rand(10000000, 99999999);

			$query = "
			INSERT INTO lms_user 
            (user_name, user_address, user_contact_no, user_profile, user_email_address, user_password, user_unique_id, user_status, user_created_on) 
            VALUES (
			'" . $formdata['user_name'] . "', 
			'" . $formdata['user_address'] . "', 
			'" . $formdata['user_contact_no'] . "', 
			'" . $formdata['user_profile'] . "', 
			'" . $formdata['user_email_address'] . "', 
			'" . $formdata['user_password'] . "', 
			'" . $user_unique_id . "', 
			'Enable', 
			'" . date('Y-m-d H:i:s') . "')
			";

			mysqli_query($conn, $query);

			$success = 'Registered as ' . $formdata['user_email_address'] . '...!!!';
		}

	}
}

include 'header.php';

?>


<div class="d-flex align-items-center justify-content-center mt-5 mb-5" style="min-height:700px;">
	<div class="col-md-6">
		<?php

		if ($message != '') {
			echo '<div class="alert alert-danger"><ul>' . $message . '</ul></div>';
		}

		if ($success != '') {
			echo '<div class="alert alert-success">' . $success . '</div>';
		}

		?>
		<div class="card">
			<div class="card-header">New User Registration</div>
			<div class="card-body">
				<form method="POST" enctype="multipart/form-data">
					<div class="mb-3">
						<label class="form-label">Email address</label>
						<input type="text" name="user_email_address" id="user_email_address" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Password</label>
						<input type="password" name="user_password" id="user_password" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">User Name</label>
						<input type="text" name="user_name" class="form-control" id="user_name" value="" />
					</div>
					<div class="mb-3">
						<label class="form-label">User Contact No.</label>
						<input type="text" name="user_contact_no" id="user_contact_no" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">User Address</label>
						<textarea name="user_address" id="user_address" class="form-control"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">User Photo</label><br />
						<input type="file" name="user_profile" id="user_profile" />
						<br />
						<span class="text-muted">Only .jpg & .png image allowed. Image height & width must be
							same.</span>
					</div>
					<div class="text-center mt-4 mb-2">
						<input type="submit" name="register_button" class="btn btn-primary" value="Register" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include 'footer.php';

?>
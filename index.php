<?php

include 'database_connection.php';
include 'function.php';

if (is_user_login()) {
	header('location:issue_book_details.php');
}

include 'header.php';

?>

<div class="p-5 mb-4 bg-light rounded-3">

	<div class="container-fluid py-5">

		<h1 class="display-5 fw-bold text-center">Library Management System</h1>

		<p class="fs-4 text-center">This is Library Management System which use for maintain the record of the library.
		</p>
		<p class="fs-5 text-end">~ Made By Umang 💻</p>

	</div>

</div>

<div class="row align-items-md-stretch">

	<div class="col-md-6">

		<div class="h-100 p-5 text-white bg-dark rounded-3">

			<h2>Admin Login</h2>

			<a href="admin_login.php" class="btn btn-outline-light">Admin Login</a>

		</div>

	</div>

	<div class="col-md-6">

		<div class="h-100 p-5 bg-light border rounded-3">

			<h2>User Login</h2>

			<a href="user_login.php" class="btn btn-outline-secondary">User Login</a>
			<a href="user_registration.php" class="btn btn-outline-primary">User Sign Up</a>

		</div>

	</div>

</div>

<?php

include 'footer.php';

?>
<?php

//issue_book.php

include '../database_connection.php';

include '../function.php';

if (!is_admin_login()) {
    header('location:../admin_login.php');
}

$error = '';

// Add Issue Record
if (isset($_POST["issue_book_button"])) {
    $formdata = array();

    if (empty($_POST["book_id"])) {
        $error .= '<li>Book ISBN Number is required</li>';
    } else {
        $formdata['book_id'] = trim($_POST['book_id']);
    }

    if (empty($_POST["user_id"])) {
        $error .= '<li>User Unique Number is required</li>';
    } else {
        $formdata['user_id'] = trim($_POST['user_id']);
    }

    if ($error == '') {

        //Check Book Available or Not
        $query = "
        SELECT * FROM lms_book 
        WHERE book_isbn_number = '" . $formdata['book_id'] . "'
        ";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            foreach ($result as $book_row) {

                //check book is available or not
                if ($book_row['book_status'] == 'Enable' && $book_row['book_no_of_copy'] > 0) {

                    //Check User is exist
                    $query = "
                    SELECT user_id, user_status FROM lms_user 
                    WHERE user_unique_id = '" . $formdata['user_id'] . "'
                    ";

                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        foreach ($result as $user_row) {
                            if ($user_row['user_status'] == 'Enable') {

                                //Check User Total issue of Book
                                $book_issue_limit = get_book_issue_limit_per_user($conn);

                                $total_book_issue = get_total_book_issue_per_user($conn, $formdata['user_id']);

                                if ($total_book_issue < $book_issue_limit) {
                                    $total_book_issue_day = get_total_book_issue_day($conn);

                                    $today_date = date('Y-m-d H:i:s');

                                    $expected_return_date = date('Y-m-d H:i:s', strtotime($today_date . ' + ' . $total_book_issue_day . ' days'));

                                    $query = "
                                    INSERT INTO lms_issue_book 
                                    (book_id, user_id, issue_date_time, expected_return_date, return_date_time, book_fines, book_issue_status) 
                                    VALUES (
                                        '" . $formdata['book_id'] . "', 
                                        '" . $formdata['user_id'] . "', 
                                        '$today_date', 
                                        '$expected_return_date', 
                                        '', 
                                        0, 
                                        'Issue')";

                                    mysqli_query($conn, $query);

                                    $query = "
                                    UPDATE lms_book 
                                    SET book_no_of_copy = book_no_of_copy - 1, 
                                    book_updated_on = '" . $today_date . "' 
                                    WHERE book_isbn_number = '" . $formdata['book_id'] . "' 
                                    ";

                                    mysqli_query($conn, $query);

                                    header('location:issue_book.php?msg=add');
                                } else {
                                    $error .= 'User has already reached Book Issue Limit, First return pending book';
                                }
                            } else {
                                $error .= '<li>User Account is Disable, Contact Admin</li>';
                            }
                        }
                    } else {
                        $error .= '<li>User not Found</li>';
                    }
                } else {
                    $error .= '<li>Book not Available</li>';
                }
            }
        } else {
            $error .= '<li>Book not Found</li>';
        }
    }
}

// Return Book Record
if (isset($_POST["book_return_button"])) {
    if (isset($_POST["book_return_confirmation"])) {

        $query = "
        UPDATE lms_issue_book 
        SET return_date_time = '" . date('Y-m-d H:i:s') . "', 
        book_issue_status = 'Return' 
        WHERE issue_book_id = " . $_POST['issue_book_id'];

        mysqli_query($conn, $query);

        $query = "
        UPDATE lms_book 
        SET book_no_of_copy = book_no_of_copy + 1 
        WHERE book_isbn_number = " . $_POST["book_isbn_number"];

        mysqli_query($conn, $query) or die('Error At Return Inc..!!');

        header("location:issue_book.php?msg=return");
    } else {
        $error = 'Please first confirm return book received by click on checkbox';
    }
}

$query = "
	SELECT * FROM lms_issue_book 
    ORDER BY issue_book_id DESC
    ";

$result_show = mysqli_query($conn, $query);

include '../header.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>Issue Book Management</h1>
    <?php

    if (isset($_GET["action"])) {
        if ($_GET["action"] == 'add') {
            ?>
            <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="issue_book.php">Issue Book Management</a></li>
                <li class="breadcrumb-item active">Issue New Book</li>
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
                            <i class="fas fa-user-plus"></i> Issue New Book
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Book Name</label>
                                    <input type="text" name="book_id" id="book_id" class="form-control" />
                                    <span id="book_isbn_result"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" name="user_id" id="user_id" class="form-control" />
                                    <span id="user_unique_id_result"></span>
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="issue_book_button" class="btn btn-success" value="Issue" />
                                </div>
                            </form>
                            <script>
                                var book_id = document.getElementById('book_id');

                                book_id.onkeyup = function () {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'search_book_name');

                                        form_data.append('request', this.value);

                                        fetch('action.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function (response) {
                                            return response.json();
                                        }).then(function (responseData) {
                                            var html = '<div class="list-group" style="position:absolute; width:93%">';

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    html += '<a href="#" class="list-group-item list-group-item-action"><span class="text-muted" onclick="get_text(this)">' + responseData[count].isbn_no + '</span> - <span class="">' + responseData[count].book_name + '</span></a>';
                                                }
                                            }
                                            else {
                                                html += '<a href="#" class="list-group-item list-group-item-action">No Book Found</a>';
                                            }

                                            html += '</div>';

                                            document.getElementById('book_isbn_result').innerHTML = html;
                                        });
                                    }
                                    else {
                                        document.getElementById('book_isbn_result').innerHTML = '';
                                    }
                                }

                                function get_text(event) {
                                    document.getElementById('book_isbn_result').innerHTML = '';

                                    document.getElementById('book_id').value = event.textContent;
                                }

                                var user_id = document.getElementById('user_id');

                                user_id.onkeyup = function () {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'search_user_name');

                                        form_data.append('request', this.value);

                                        fetch('action.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function (response) {
                                            return response.json();
                                        }).then(function (responseData) {
                                            var html = '<div class="list-group" style="position:absolute;width:93%">';

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    html += '<a href="#" class="list-group-item list-group-item-action" ><span class="text-muted" onclick="get_text1(this)">' + responseData[count].user_unique_id + '</span> - <span>' + responseData[count].user_name + '</span></a>';
                                                }
                                            }
                                            else {
                                                html += '<a href="#" class="list-group-item list-group-item-action">No User Found</a>';
                                            }
                                            html += '</div>';

                                            document.getElementById('user_unique_id_result').innerHTML = html;
                                        });
                                    }
                                    else {
                                        document.getElementById('user_unique_id_result').innerHTML = '';
                                    }
                                }

                                function get_text1(event) {
                                    document.getElementById('user_unique_id_result').innerHTML = '';

                                    document.getElementById('user_id').value = event.textContent;
                                }

                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else if ($_GET["action"] == 'view') {
            $issue_book_id = $_GET["code"];

            if ($issue_book_id > 0) {
                $query = "
                SELECT * FROM lms_issue_book 
                WHERE issue_book_id = '$issue_book_id'
                ";

                $result = mysqli_query($conn, $query);

                foreach ($result as $row) {
                    $query = "
                    SELECT * FROM lms_book 
                    WHERE book_isbn_number = '" . $row["book_id"] . "'
                    ";

                    $book_result = mysqli_query($conn, $query);

                    $query = "
                    SELECT * FROM lms_user 
                    WHERE user_unique_id = '" . $row["user_id"] . "'
                    ";

                    $user_result = mysqli_query($conn, $query);

                    echo '
                    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="issue_book.php">Issue Book Management</a></li>
                        <li class="breadcrumb-item active">View Issue Book Details</li>
                    </ol>
                    ';

                    if ($error != '') {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }

                    foreach ($book_result as $book_data) {
                        echo '
                        <h2>Book Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Book ISBN Number</th>
                                <td width="70%">' . $book_data["book_isbn_number"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Book Title</th>
                                <td width="70%">' . $book_data["book_name"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Author</th>
                                <td width="70%">' . $book_data["book_author"] . '</td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    foreach ($user_result as $user_data) {
                        echo '
                        <h2>User Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">User Unique ID</th>
                                <td width="70%">' . $user_data["user_unique_id"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">User Name</th>
                                <td width="70%">' . $user_data["user_name"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">User Address</th>
                                <td width="70%">' . $user_data["user_address"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">User Contact No.</th>
                                <td width="70%">' . $user_data["user_contact_no"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">User Email Address</th>
                                <td width="70%">' . $user_data["user_email_address"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">User Image</th>
                                <td width="70%"><img src="' . base_url() . 'upload/' . $user_data["user_profile"] . '" class="img-thumbnail" width="100" /></td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    $status = $row["book_issue_status"];

                    $form_item = '';

                    if ($status == "Issue") {
                        $status = '<span class="badge bg-warning">Issue</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> I aknowledge that I have received Issued Book</label>
                        <br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Book Return" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($status == 'Not Return') {
                        $status = '<span class="badge bg-danger">Not Return</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> I aknowledge that I have received Issued Book</label><br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Book Return" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($status == 'Return') {
                        $status = '<span class="badge bg-primary">Return</span>';
                    }

                    echo '
                    <h2>Issue Book Details</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Book Issue Date</th>
                            <td width="70%">' . $row["issue_date_time"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Book Return Date</th>
                            <td width="70%">' . $row["return_date_time"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Book Issue Status</th>
                            <td width="70%">' . $status . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Total Fines</th>
                            <td width="70%">' . '<span style="font-family: DejaVu Sans;">&#8377;</span>' . ' ' . $row["book_fines"] . '</td>
                        </tr>
                    </table>
                    <form method="POST">
                        <input type="hidden" name="issue_book_id" value="' . $issue_book_id . '" />
                        <input type="hidden" name="book_isbn_number" value="' . $row["book_id"] . '" />
                        ' . $form_item . '
                    </form>
                    <br />
                    ';

                }
            }
        }
    } else {
        ?>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Issue Book Management</li>
        </ol>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'add') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Book Issue Successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if ($_GET["msg"] == 'return') {
                echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">Issued Book Successfully Return into Library <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            ';
            }
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Issue Book Management
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="issue_book.php?action=add" class="btn btn-success btn-sm">Add</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Book ISBN Number</th>
                            <th>User Unique ID</th>
                            <th>Issue Date</th>
                            <th>Expectd Date</th>
                            <th>Return Date</th>
                            <th>Late Return Fines</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Book ISBN Number</th>
                            <th>User Unique ID</th>
                            <th>Issue Date</th>
                            <th>Expectd Date</th>
                            <th>Return Date</th>
                            <th>Late Return Fines</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result_show) > 0) {
                            $one_day_fine = get_one_day_fines($conn);



                            foreach ($result_show as $row) {
                                $status = $row["book_issue_status"];

                                $book_fines = $row["book_fines"];

                                if ($row["book_issue_status"] != "Return") {
                                    $current_date_time = new DateTime(date('Y-m-d H:i:s'));
                                    $expected_return_date = new DateTime($row["expected_return_date"]);

                                    if ($current_date_time > $expected_return_date) {
                                        $interval = $current_date_time->diff($expected_return_date);

                                        $total_day = $interval->d;

                                        $book_fines = $total_day * $one_day_fine;

                                        $status = 'Not Return';

                                        $query = "
        						UPDATE lms_issue_book 
													SET book_fines = '" . $book_fines . "', 
													book_issue_status = '" . $status . "' 
													WHERE issue_book_id = '" . $row["issue_book_id"] . "'
        						";

                                        mysqli_query($conn, $query);
                                    }
                                }

                                if ($status == 'Issue') {
                                    $status = '<span class="badge bg-warning">Issue</span>';
                                }

                                if ($status == 'Not Return') {
                                    $status = '<span class="badge bg-danger">Not Return</span>';
                                }

                                if ($status == 'Return') {
                                    $status = '<span class="badge bg-primary">Return</span>';
                                }

                                echo '
        				<tr>
        					<td>' . $row["book_id"] . '</td>
        					<td>' . $row["user_id"] . '</td>
        					<td>' . $row["issue_date_time"] . '</td>
        					<td>' . $row["expected_return_date"] . '</td>
        					<td>' . $row["return_date_time"] . '</td>
        					<td>' . '<span style="font-family: DejaVu Sans;">&#8377;</span>' . $book_fines . '</td>
        					<td>' . $status . '</td>
        					<td>
                                <a href="issue_book.php?action=view&code=' . $row["issue_book_id"] . '" class="btn btn-info btn-sm">View</a>
                            </td>
        				</tr>
        				';
                            }
                        } else {
                            echo '
        			<tr>
        				<td colspan="7" class="text-center">No Data Found</td>
        			</tr>
        			';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php

include '../footer.php';

?>
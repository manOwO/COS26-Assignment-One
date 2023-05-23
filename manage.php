<!--
filename: manage.php
authors: Xuan Tuan Minh Nguyen, Nathan Wijaya, Mai An Nguyen, Nhat Minh Tran, Amiru Manthrige
created: 21-Mar-2023
description: Manager form
-->

<!DOCTYPE html>
<html lang="en" class="manage-class">
<head>
	<meta charset="utf-8"/>
	<meta name="description" content="Creating Web Applications Lab 10" />
	<meta name="keywords"    content="PHP, MySql" />
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" />
    <title>Manage - CloudLabs</title>
</head>
<body class="index-body">
<?php
$activePage = "manage";
include_once("header.inc"); ?>
<main id="manage-body">
    <h1>Management page</h1>
    <h2>List all EOIs</h2>

<?php
    session_start();

    require_once("settings.php");

    if (isset ($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["pw"];

        $_SESSION["username"] = $username;
        $_SESSION["pw"] = $password;

    }
    elseif (isset ($_SESSION["username"])) {

    }
    else {
        header ("location: loginmanager.php");
    }



    function sanitise_data($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $connection = @mysqli_connect($host_name, $user_name, $password, $database);

    // checks if connection's successful
    if (!$connection) {
        // display an error message
        echo "<p>Database connection failure</p>"; // not in production script
    }
    else {
        // upon successful connection
        if (check_table_existence($connection, $table)) {
            // set up the sql command to query/add data into the table
            $query_all = "select * from $table"; //command in terminal, can be fixed, select others from table in db include data we need

            // execute the query & store result into the result pointer
            $result_all = mysqli_query($connection, $query_all);

            // checks if the execution was successful
            if (!$result_all) {
                echo "<>Something is wrong with ", $query_all, "</p>";
            }
            else {
                // display the retrieved records
                echo "<table border=\"1px\">\n";
                echo "<tr>\n"
                    ."<th scope=\"col\">EOI Number</th>\n"
                    ."<th scope=\"col\">Job Reference Number</th>\n"
                    ."<th scope=\"col\">Name</th>\n"
                    ."<th scope=\"col\">Date of Birth</th>\n"
                    ."<th scope=\"col\">Gender</th>\n"
                    ."<th scope=\"col\">Address</th>\n"
                    ."<th scope=\"col\">Email</th>\n"
                    ."<th scope=\"col\">Phone Number</th>\n"
                    ."<th scope=\"col\">Skills</th>\n"
                    ."<th scope=\"col\">Status</th>\n"
                    ."</tr>\n";
                // retrieve current record pointed by the result pointer

                while ($row = mysqli_fetch_assoc($result_all)) {
                    echo "<tr>\n";
                    echo "<td>", $row["EOINumber"], "</td>\n";
                    echo "<td>", $row["job_reference_number"], "</td>\n";
                    echo "<td>", $row["first_name"], " ", $row["last_name"], "</td>\n";
                    echo "<td>", $row["date_of_birth"], "</td>\n";
                    echo "<td>", $row["gender"], "</td>\n";
                    echo "<td>", $row["street_address"], ", ", $row["suburb"], ", ", $row["state"], ", ", $row["postcode"], "</td>\n";
                    echo "<td>", $row["email"], "</td>\n";
                    echo "<td>", $row["phone"], "</td>\n";
                    $skills_all = "";
                    if ($row["skill_communication"]==1) {
                        $skills_all.= "communication";
                    }
                    if ($row["skill_teamwork"]==1) {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= "communication";
                    }
                    if ($row["skill_detail_oriented"]==1) {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= "detail oriented";
                    }
                    if ($row["skill_initiative"]==1) {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= "initiative";
                    }
                    if ($row["skill_time_management"]==1) {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= "time management";
                    }
                    if ($row["skill_risk_management"]==1) {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= "risk management";
                    }
                    if ($row["other_skills"]=="") {
                        if ($skills_all != "") {
                            $skills_all.= ", ";
                        }
                        $skills_all.= $row["other_skills"];
                    }
                    echo "<td>", $skills_all, "</td>\n";
                    echo "<td>", $row["status"], "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                // frees up the memory, after using the result pointer
                mysqli_free_result($result_all);
            }

        }
        else {
            echo "<p>Table not exist</p>";
        }


        // close the database connection
        mysqli_close($connection);
    } // if successful database connection

?>
    <br>
    <button onClick="window.location.reload();">Reload all EOIs</button>
    <br>

    <h2>List all EOIs based on particular position given reference number</h2>
    <form action="<?php $_PHP_SELF ?>" method="post">
        <label for="jobref">Reference number</label>
        <input type="text" name="jobref" id="jobref">
        <input type="submit" name="submit_job" value="Find based on reference number">
        <input type="submit" name="submit_job" value="Delete base on reference number">
    </form>

<?php

    if (isset ($_POST["submit_job"])) {
        if (isset ($_POST["jobref"])) {
            $jobref = sanitise_data($_POST["jobref"]);


            // require_once("setting.php"); //chua co db de lm

            $connection = @mysqli_connect($host_name, $user_name, $password, $database);

            // checks if connection's successful
            if (!$connection) {
                // display an error message
                echo "<p>Database connection failure</p>"; // not in production script
            }
            else {
                if (check_table_existence($connection, $table)) {
                    if (($jobref == "")) {
                        echo "<p>No values have been entered for reference number</p>";
                    }
                    else {
                        $row_jobref_ex = mysqli_fetch_assoc(mysqli_query($connection, "select exists(select * from $table where job_reference_number='$jobref')"));

                        if ($row_jobref_ex["exists(select * from $table where job_reference_number='$jobref')"] == 0) {
                            echo "<p>Cannot find this job reference number in the database</p>";
                        }
                        else {


                            if ($_POST["submit_job"] == "Delete base on reference num") {
                                $query_del_jobref = "delete from $table where job_reference_number='$jobref'";

                                $result_job = mysqli_query($connection, $query_del_jobref);
                            }
                            else {
                                $query_job = "select * from $table where job_reference_number='$jobref'"; //ko co position field in db?

                                $result_job = mysqli_query($connection, $query_job);

                                if (!$result_job) {
                                    echo "<>Something is wrong with ", $query_job, "</p>";
                                }
                                else {
                                    // display the retrieved records
                                    // display the retrieved records
                                    echo "<table border=\"1px\">\n";
                                    echo "<tr>\n"
                                        ."<th scope=\"col\">EOI Number</th>\n"
                                        ."<th scope=\"col\">Job Reference Number</th>\n"
                                        ."<th scope=\"col\">Name</th>\n"
                                        ."<th scope=\"col\">Date of Birth</th>\n"
                                        ."<th scope=\"col\">Gender</th>\n"
                                        ."<th scope=\"col\">Address</th>\n"
                                        ."<th scope=\"col\">Email</th>\n"
                                        ."<th scope=\"col\">Phone Number</th>\n"
                                        ."<th scope=\"col\">Skills</th>\n"
                                        ."<th scope=\"col\">Status</th>\n"
                                        ."</tr>\n";
                                    // retrieve current record pointed by the result pointer

                                    while ($row = mysqli_fetch_assoc($result_job)) {
                                        echo "<tr>\n";
                                        echo "<td>", $row["EOINumber"], "</td>\n";
                                        echo "<td>", $row["job_reference_number"], "</td>\n";
                                        echo "<td>", $row["first_name"], " ", $row["last_name"], "</td>\n";
                                        echo "<td>", $row["date_of_birth"], "</td>\n";
                                        echo "<td>", $row["gender"], "</td>\n";
                                        echo "<td>", $row["street_address"], ", ", $row["suburb"], ", ", $row["state"], ", ", $row["postcode"], "</td>\n";
                                        echo "<td>", $row["email"], "</td>\n";
                                        echo "<td>", $row["phone"], "</td>\n";
                                        $skills_all = "";
                                        if ($row["skill_communication"]==1) {
                                            $skills_all.= "communication";
                                        }
                                        if ($row["skill_teamwork"]==1) {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= "communication";
                                        }
                                        if ($row["skill_detail_oriented"]==1) {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= "detail oriented";
                                        }
                                        if ($row["skill_initiative"]==1) {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= "initiative";
                                        }
                                        if ($row["skill_time_management"]==1) {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= "time management";
                                        }
                                        if ($row["skill_risk_management"]==1) {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= "risk management";
                                        }
                                        if ($row["other_skills"]=="") {
                                            if ($skills_all != "") {
                                                $skills_all.= ", ";
                                            }
                                            $skills_all.= $row["other_skills"];
                                        }
                                        echo "<td>", $skills_all, "</td>\n";
                                        echo "<td>", $row["status"], "</td>\n";
                                        echo "</tr>\n";
                                    }
                                    echo "</table>\n";
                            // frees up the memory, after using the result pointer
                                    mysqli_free_result($result_job);
                                }
                            }
                        }

                    }
                }
                else {
                    echo "<p>Table not exist</p>";
                }

                mysqli_close($connection);
            }
        }
    }


?>
    <!-- table of all eoi based on ref num -->
    <!-- delete all eois w a specified job ref num using button -->

    <h2>List all EOIs for a particular participant based on their name</h2>
    <form action="<?php $_PHP_SELF ?>" method="post">
        <label for="fname">First Name</label>
        <input type="text" name="fname" id="fname">
        <label for="lname">Last Name</label>
        <input type="text" name="lname" id="lname">
        <input type="submit" name="submit_name" value="Find Name">
    </form>

<?php
    if (isset ($_POST["submit_name"]) and isset ($_POST["fname"]) and isset ($_POST["lname"])) {
        $fname = sanitise_data($_POST["fname"]);
        $lname = sanitise_data($_POST["lname"]);

        // require_once("setting.php"); //chua co db de lm

        $connection = @mysqli_connect($host_name, $user_name, $password, $database);

        // checks if connection's successful
        if (!$connection) {
            // display an error message
            echo "<p>Database connection failure</p>"; // not in production script
        }
        else {
            if (check_table_existence($connection, $table)) {
                if (($fname == "") and ($lname == "")) {
                    echo "<p>No first name or last name has been entered for searching</p>";
                }
                else {
                    if (($fname == "") or ($lname == "")) {
                        $row_name_ex = mysqli_fetch_assoc(mysqli_query($connection, "select exists(select * from $table where first_name='$fname' or last_name='$lname')"));
                    }
                    if (($fname != "") and ($lname != "")) {
                        $row_name_ex = mysqli_fetch_assoc(mysqli_query($connection, "select exists(select * from $table where first_name='$fname' and last_name='$lname')"));
                    }

                    if ($row_name_ex["exists(select * from $table where first_name='$fname' or last_name='$lname')"] == 0) {
                        echo "<p>Cannot find this name in the database</p>";
                    }
                    elseif ($row_name_ex["exists(select * from $table where first_name='$fname' and last_name='$lname')"] == 0) {
                        echo "<p>Cannot find this name in the database</p>";
                    }
                    else {

                        $query_name = "select * from $table where";
                        if (($fname == "") or ($lname == "")) {
                            $query_name.= "first_name like '$fname' or last_name like '$lname'";
                        }
                        if (($fname != "") and ($lname != "")) {
                            $query_name.= "first_name like '$fname' and last_name like '$lname'";
                        }


                        $result_name = mysqli_query($connection, $query_name);

                        if (!$result_name) {
                            echo "<>Something is wrong with ", $query_name, "</p>";
                        }
                        else {
                            // display the retrieved records
                           // display the retrieved records
                            echo "<table border=\"1px\">\n";
                            echo "<tr>\n"
                                ."<th scope=\"col\">EOI Number</th>\n"
                                ."<th scope=\"col\">Job Reference Number</th>\n"
                                ."<th scope=\"col\">Name</th>\n"
                                ."<th scope=\"col\">Date of Birth</th>\n"
                                ."<th scope=\"col\">Gender</th>\n"
                                ."<th scope=\"col\">Address</th>\n"
                                ."<th scope=\"col\">Email</th>\n"
                                ."<th scope=\"col\">Phone Number</th>\n"
                                ."<th scope=\"col\">Skills</th>\n"
                                ."<th scope=\"col\">Status</th>\n"
                                ."</tr>\n";
                            // retrieve current record pointed by the result pointer

                            while ($row = mysqli_fetch_assoc($result_name)) {
                                echo "<tr>\n";
                                echo "<td>", $row["EOINumber"], "</td>\n";
                                echo "<td>", $row["job_reference_number"], "</td>\n";
                                echo "<td>", $row["first_name"], " ", $row["last_name"], "</td>\n";
                                echo "<td>", $row["date_of_birth"], "</td>\n";
                                echo "<td>", $row["gender"], "</td>\n";
                                echo "<td>", $row["street_address"], ", ", $row["suburb"], ", ", $row["state"], ", ", $row["postcode"], "</td>\n";
                                echo "<td>", $row["email"], "</td>\n";
                                echo "<td>", $row["phone"], "</td>\n";
                                $skills_all = "";
                                if ($row["skill_communication"]==1) {
                                    $skills_all.= "communication";
                                }
                                if ($row["skill_teamwork"]==1) {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= "communication";
                                }
                                if ($row["skill_detail_oriented"]==1) {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= "detail oriented";
                                }
                                if ($row["skill_initiative"]==1) {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= "initiative";
                                }
                                if ($row["skill_time_management"]==1) {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= "time management";
                                }
                                if ($row["skill_risk_management"]==1) {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= "risk management";
                                }
                                if ($row["other_skills"]=="") {
                                    if ($skills_all != "") {
                                        $skills_all.= ", ";
                                    }
                                    $skills_all.= $row["other_skills"];
                                }
                                echo "<td>", $skills_all, "</td>\n";
                                echo "<td>", $row["status"], "</td>\n";
                                echo "</tr>\n";
                            }
                            echo "</table>\n";
                            // frees up the memory, after using the result pointer
                            mysqli_free_result($result_name);
                        }
                    }
            }

                mysqli_close($connection);
            }
            else {
                echo "<p>Table not exist</p>";
            }
        }

    }


?>
    <h2>Change the status of an EOI</h2>
    <!-- insert (add new data), delete (rmv existing data), update (modify existing data) -->
    <form action="<?php $_PHP_SELF ?>" method="post">
        <!-- <label for="status">status of eoi</label>
        <input type="text" name="status" id="status"> -->
        <!-- status is a new field/column in the table -->

        <label for="status">Status</label>
        <input type="text" name="status" id="status" required>
        <label for="eoinum">EOI Number</label>
        <input type="text" name="eoinum" id="eoinum" required>

        <input type="submit" name="submit_change_stat" value="Change Status">
    </form>
<?php
    if (isset ($_POST["submit_change_stat"])) {

        $status = sanitise_data($_POST["status"]);

        $eoinum = sanitise_data($_POST["eoinum"]);

        $connection = @mysqli_connect($host_name, $user_name, $password, $database);


        // checks if connection's successful
        if (!$connection) {
            // display an error message
            echo "<p>Database connection failure</p>"; // not in production script
        }
        else {
            if (check_table_existence($connection, $table)) {
                $row_change_stat_ex = mysqli_fetch_assoc(mysqli_query($connection, "select exists(select * from $table where EOINumber='$eoinum')"));

                if ($row_change_stat_ex["exists(select * from $table where where EOINumber='$eoinum')"] == 0) {
                    echo "<p>Cannot find this EOI number in the database</p>";
                }
                else {
                    $query_change = "update $table set status='$status' where EOINumber='$eoinum'";

                    $result_change = mysqli_query($connection, $query_change);

                    if (!$result_change) {
                        echo "<>Something is wrong with ", $query_change, "</p>";
                    }
                    else {
                        echo "<p>Successfully change EOI's status.</p>";
                        // mysqli_free_result($result_change);
                    }
                }
            }
            else {
                echo "<p>Table not exist</p>";
            }

        }


        mysqli_close($connection);
    }



?>
</main>
<?php
  // include footer
  include_once "footer.inc";
?>
</body>
</html>

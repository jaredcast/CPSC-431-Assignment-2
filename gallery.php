<?php
$tmp_name = "temp"; //Temporary filename
$name = $_POST['name'];
$date = $_POST['date'];
$photographer = $_POST['photographer'];
$location = $_POST['location'];
$choice = $_GET['choice']; //The sorting choice we want to work with - name, date, pg, loc. Anything else is default. 
$filename = $_FILES["imgUpload"]["name"];
$imgType = $_FILES["imgUpload"]["type"];
$errorImg = False;

@$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
if (mysqli_connect_errno()) {
    echo "<p>Error: Cannot connect to database!</p>";
    exit;
 }
	
	//Move the uploaded file into the text file, separated by strings
	if (move_uploaded_file($_FILES["imgUpload"]["tmp_name"],"uploads/".$filename)) {
		//$outputStr = $filename."\t".$name."\t".$date."\t".$photographer."\t".$location."\n";
        // if (!$name || !$date || !$photographer || !$location) {
		// 	echo "ERROR: Missing data.";
		// 	exit;
		// }

        // if ($imgType && $imgType != 'image/png' && $imgType != 'image/jpg' && $imgType != 'image/jpeg' && $imgType != 'image/gif') {
        //     echo 'ERROR: NOT A PNG, JPEG, GIF, or JPG.';
        //     exit;
        // }

        $query = "INSERT INTO Images VALUES (?, ?, ?, ?, ?)";
        $statement = $db->prepare($query);
        $statement->bind_param('sssss', $filename, $name, $date, $photographer, $location);
        $statement->execute();

        if ($statement->affected_rows > 0) {
            echo  "<p>Image successfully added.</p>";
        } else {
            echo "<p>An error has occurred. Image failed to upload.</p>";
        }

        // @$fp = fopen("/home/titan0/cs431s/cs431s26/homepage/assignment1/photodata.txt", 'ab');
        // if (!$fp) {
        //     echo "<p><strong> ERROR: Writing to text file did not work. Please try again.</strong></p>";
        //     exit;
        // }
        $db->close(); 
	}

    //Sorting functions for later on. User defined, will compare each value.
    //Array positions: 0 - Filename. 1 - Name. 2 - Date. 3 - Photographer. 4 - Location.

    function compareName($x, $y) {
        if (strtolower($x[1]) == strtolower($y[1])) {
            return 0;
        } else if (strtolower($x[1]) < strtolower($y[1])) {
            return -1;
        } else {
            return 1;
        }
    }
    function compareDate($x, $y) {
        if (strtolower($x[2]) == strtolower($y[2])) {
            return 0;
        } else if (strtolower($x[2]) < strtolower($y[2])) {
            return -1;
        } else {
            return 1;
        }
    }
    function comparePG($x, $y) {
        if (strtolower($x[3]) == strtolower($y[3])) {
            return 0;
        } else if (strtolower($x[3]) < strtolower($y[3])) {
            return -1;
        } else {
            return 1;
        }
    }
    function compareLocation($x, $y) {
        if (strtolower($x[4]) == strtolower($y[4])) {
            return 0;
        } else if (strtolower($x[4]) < strtolower($y[4])) {
            return -1;
        } else {
            return 1;
        }
    }
?>

<!DOCTYPE html>
<html>
    <?php
        @$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
        if (mysqli_connect_errno()) {
            echo "<p>Error: Cannot connect to database!</p>";
            exit;
        }

        $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->store_result();

        $statement->bind_result($filename, $name, $date, $photographer, $location);
        
        echo "<p>Number of images found: ".$statement->num_rows."</p>";

        while($statement->fetch()) {
            echo "<h3><img src=\"uploads/" . $filename . "\"/><br>";
            echo "Name: " . $name . "<br>";
            echo "Date: " . $date . "<br>";
            echo "Photographer: " . $photographer . "<br>";
            echo "Location: " . $location . "<br><br></h3>";
        }
        $statement->free_result();
        $db->close();
    ?>
</html>
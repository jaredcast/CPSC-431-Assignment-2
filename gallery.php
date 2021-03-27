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
$uploadStatus = "temp";

    @$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
    if (mysqli_connect_errno()) {
        echo "<p>Error: Cannot connect to database!</p>";
        exit;
    }

    if ($imgType && $imgType != 'image/png' && $imgType != 'image/jpg' && $imgType != 'image/jpeg' && $imgType != 'image/gif') {
        echo 'ERROR: NOT A PNG, JPEG, GIF, or JPG.';
        exit;
    }
	
	if (move_uploaded_file($_FILES["imgUpload"]["tmp_name"],"uploads/".$filename)) {
		//$outputStr = $filename."\t".$name."\t".$date."\t".$photographer."\t".$location."\n";
        if (!$name || !$date || !$photographer || !$location) {
			echo "ERROR: Missing data.";
			exit;
		}

        $query = "INSERT INTO Images (Filename, Name, Date, Photographer, Location) VALUES (?, ?, ?, ?, ?)";
        $statement = $db->prepare($query);
        $statement->bind_param('sssss', $filename, $name, $date, $photographer, $location);
        $statement->execute();

        if ($statement->affected_rows > 0) {
            $uploadStatus = "<p>Image successfully added.</p>";
        } else {
            $uploadStatus = "<p>An error has occurred. Image failed to upload.</p>";
        }
        $db->close(); 
	}

    //Sorting functions for later on. User defined, will compare each value.
    //Array positions: 0 - Filename. 1 - Name. 2 - Date. 3 - Photographer. 4 - Location.

?>

<!DOCTYPE html>
<html>
    <head>
		<meta charset="utf-8">
		<title> My Gallery </title>
		<meta name = "viewport" content = "width = device-width, initial-scale = 1.0">
		<link rel="stylesheet" href="styles.css">
	</head>

    <section id = "title">
      <p>View All Photos</p>
    </section>
    <br>
    <div class = "gallery-upload">
        <form action = "gallery.php" method = "GET">
            <td><select name="choice" onchange="this.form.submit();">
                <option>Sort By</option>
                    <option value = "name">Name</option>
                    <option value = "date">Date</option>
                    <option value = "pg">Photographer</option>
                    <option value = "loc">Location</option>
                    <option value = "">Default</option>
                </select>
            </td>
        </form>


        <form action = "index.html" method = "post">
            <button type = "submit" name = "submit"> Upload Photo </button>
        </form>
    </div>	
    <div class = "gallery-container">
        <?php
            if ($uploadStatus != "temp") {
                echo $uploadStatus;
            }
            @$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
            if (mysqli_connect_errno()) {
                echo "<p>Error: Cannot connect to database!</p>";
                exit;
            }
            $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images";
            if ($choice == "name") {
                echo ' <p> Sorting by name </p>';
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Name";
            }
            else if ($choice == "date") {
                echo ' <p> Sorting by date </p>';
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Date";
            }
            else if ($choice == "pg") {
                echo ' <p> Sorting by photographer </p>';
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Photographer";
            }
            else if ($choice == "loc") {
                echo ' <p> Sorting by location </p>';
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Location";
            }
            else {
                echo ' <p>Sorting by default</p>';           
            }

            #$query = "SELECT Filename, Name, Date, Photographer, Location FROM Images";
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
    </div>
    <footer style = "text-align: center; padding-top: 5px">
		<p>Created by:</p> 
		<p>Jared Castaneda and Efrain Lozada Trujillo</p>
	</footer>
</html>
<?php
// Jared Castaneda 
// Efrain Lozada Trujillo
// CPSC 431 Assignment 2
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

    //Connect to mariadb
    @$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
    if (mysqli_connect_errno()) {
        echo "<p>Error: Cannot connect to database!</p>";
        exit;
    }

    //Check image filetype
    if ($imgType && $imgType != 'image/png' && $imgType != 'image/jpg' && $imgType != 'image/jpeg' && $imgType != 'image/gif') {
        echo 'ERROR: NOT A PNG, JPEG, GIF, or JPG.';
        exit;
    }
	
    //Moving file to folder
	if (move_uploaded_file($_FILES["imgUpload"]["tmp_name"],"uploads/".$filename)) {
		//Check if there is missing data
        if (!$name || !$date || !$photographer || !$location) {
			echo "ERROR: Missing data.";
			exit;
		}

        //Prepare the SQL query, then store it in a statement. Bind the parameters that are passed in from the html page, then execute.
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
            //Print out status of upload if something was uploaded
            if ($uploadStatus != "temp") {
                echo $uploadStatus;
            }

            //Connect to database
            @$db = new mysqli('mariadb', 'cs431s26', 'Uo3io9ve', 'cs431s26');
            if (mysqli_connect_errno()) {
                echo "<p>Error: Cannot connect to database!</p>";
                exit;
            }

            //Prepare default query and change it based on the sorting option $choice. This is from the dropdown box
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

            //Execute the statement
            $statement = $db->prepare($query);
            $statement->execute();
            $statement->store_result();
            
            $statement->bind_result($filename, $name, $date, $photographer, $location);
            
            echo "<p>Number of images found: ".$statement->num_rows."</p>";

            //While the statement fetches different queries in the database, keep printing out the information.
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
    <footer class="footer">
        <!-- Had a lot of trouble getting this to work D: -->
		<p>Created by:</p> 
		<p>Jared Castaneda and Efrain Lozada Trujillo</p>
    </footer>

</html>
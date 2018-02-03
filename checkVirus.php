<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<?php
	// Open connection
	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$db = 'virustotal';
	$conn = mysqli_connect($host, $user, $pass, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	
	
	if (isset($_POST['adminLogin'])) {
        # adminLogin-button was clicked
		header('Location: http://localhost/PHP.php');
    }
    elseif (isset($_POST['uploadButton']) ) {
        # uploadButton was clicked
		$byteFromDB = "select VirusBite from virus";
		
		
		// Array of virus bites
		$viruses = array();		
		$result = $conn->query($byteFromDB);
		if(!$result){
			die($conn->connect_error);
			return;
		}
		$rows = $result->num_rows;    
		
		for($i=0; $i < $rows; $i++){
			$result->data_seek($i);
			$obj = $result->fetch_array(MYSQLI_ASSOC);
			$viruses[] = $obj['VirusBite'];
		}
		$result->close();
		$conn-> close();
		//echo('output'.$viruses[0]);
		
		if($_FILES['uploadButtonCheck']['tmp_name']){
			//echo "Hello <br>";
			$content = file_get_contents($_FILES['uploadButtonCheck']['tmp_name']);
			$asciiInput = stringToAscii($content);
			//$byteFromDB = "select VirusByte from virus";
			$first20Bite = getFirstTwentyBytes($asciiInput);	
			
			// Check if the file bytes is in the array viruses
			$check = 0;
			for($i = 0; $i < count($viruses); $i++){
				if($first20Bite === $viruses[$i]){
					echo "Infested<br>";
					$check = 1;
					break;
				}
				else {
					$check = 0;
				}
				
			}
			if($check == 0){
				echo "File is clean<br>";
			}
			// Compare with database
				
				// If the virus is already in database, not insert
				//$query = "INSERT IGNORE INTO virus (virusName, virusBite) values (?,?)";
				//$object = $conn->prepare($query);
				//$object->bind_param('ss', $virusName, $first20Bite);
				//$object->execute();
				
			
			//$conn->close();		
			
			
		}
		
    }	
	
	function getFirstTwentyBytes($input){     
		$ascciArray = explode(' ', $input);
		
		$str = $ascciArray[0];
		for($i=1; $i < 20 ; $i++){
			$str .= ' ' . $ascciArray[$i]; 
		}
		return $str;
	}
	
	function stringToAscii($input){
		$ascci = '';
		for($i=0; $i < strlen($input); $i++){
			$s =  ord(substr($input, $i, $i+1));
			$ascci .= $s . ' ';
		}
		return $ascci;
	}
	
	function checkVirusName($virusName){
		if(strlen($virusName) < 6){
			return "Name is too short<br>";
		}
		else{
			return "";
		}
	}
	
?>

<form class="form-horizontal" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
<fieldset>

<!-- Form Name -->
<legend>Upload File To Check</legend>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="adminLogin"></label>
  <div class="col-md-4">
    <button id="adminLogin" type = "submit" name="adminLogin" class="btn btn-primary">Admin Login</button>
  </div>
</div>

<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="uploadButtonCheck"></label>
  <div class="col-md-4">
		
    <input id="uploadButtonCheck" name="uploadButtonCheck" class="element file" type="file">
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="uploadButton"></label>
  <div class="col-md-4">
    <button id="uploadButton" type = "submit" name="uploadButton" class="btn btn-default">Upload</button>
  </div>
</div>

</fieldset>
</form>
</body>
</html>
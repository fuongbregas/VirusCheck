<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Welcome to Virus Total</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<style>
.error {color: #FF0000;}
</style>

<script >
	//alert("running\n")
	// If the input is empty or too short
	function validateVirusName(input){
		if(input.length < 6){
			return "Name is too short\n"
		}
		return "";
	}
	
	function validate(form){
		//alert("It runs inside\n");
		var fail = ""
		fail = validateVirusName(form.virusName.value)
		if(fail == ""){
			return true
		}
		else{
			alert(fail)
			return false
		}
	}
</script>

</head>

<?php
	//require_once "../virusPage.php";
	
	session_start();
	if(!validateSession()){
		header('Location: http://localhost/PHP.php');
	}	
	
	// define empty string
	$virusName = '';
	$size = 0;
	// Open connection
	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$db = 'virustotal';
	$conn = mysqli_connect($host, $user, $pass, $db);
	
	if ($conn->connect_error) die($conn->connect_error);
	// check input text
	$virusName = stripcslashes($virusName);
	$virusName = mysqli_real_escape_string($conn, $virusName);
	// Get user input
	if(isset($_POST["virusName"]) ){
		$virusName = $_POST['virusName'];			
	}
	//Logout
	if(isset($_POST["Logout"])){
		// Destroy session and go back to first page
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, "/");
		session_destroy();
		header('Location: http://localhost/checkVirus.php');
	}
		
			
	if($_FILES){
		if($_FILES['virusFile']['tmp_name']){
			$content = file_get_contents($_FILES['virusFile']['tmp_name']);
			$asciiInput = stringToAscii($content);
			$fail = checkVirusName($virusName);
			//echo "$fail<br>";
			if($fail == ""){
				//echo "Not fail<br>";
				$first20Bite = getFirstTwentyBytes($asciiInput);
				
				
				// Add to database
				// Make primary key
				$unique = "alter table virus add primary key (virusName,virusBite)";
				$unique = mysqli_query($conn, $unique);
				// If the virus is already in database, not insert
				$query = "INSERT IGNORE INTO virus (virusName, virusBite) values (?,?)";
				$object = $conn->prepare($query);
				$object->bind_param('ss', $virusName, $first20Bite);
				$object->execute();
				$object->close();
				$conn->close();
					
			}
			else{
				echo "$fail<br>";
			}
			
		}

	}	
	
	//$query = mysqli_query($conn, "Insert INTO virus (virusName, virusBite) values ($virusName, $size)");
	
	function validateSession(){
		if($_SESSION['check'] == hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_COOKIE['token'])){
			return true;
		}
		else{
			return false;
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
	// If user enter short name
	function checkVirusName($virusName){
		if(strlen($virusName) < 6){
			return "Name is too short<br>";
		}
		else{
			return "";
		}
	}
	
	
// action="../virusPage.php"

?>
<body id="main_body" >

	
	
	<img id="top" src="top.png" alt="">
	<div id="form_container">
	
		<h1><a>Welcome to Virus Total</a></h1>
		<form id="form_62685" class="appnitro" enctype="multipart/form-data" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  onSubmit = "return validate(this)">
					<div class="form_description">
			<h2>Welcome to Virus Total</h2>
			<p></p>
		</div>						
			<ul >
			
					<li id="li_2" >
		<label class="description" for="virusName">Virus Name </label>
		<div>
			<input id="virusName" name="virusName"required class="element text medium" type="text" maxlength="255" value="" /> 
		</div><p class="guidelines" id="guide_2"><small>Enter a name for the virus</small></p> 
		</li>		<li id="li_1" >
		<label class="description" for="virusFile">Upload a virus </label>
		<div>
			<input id="virusFile" name="virusFile" required class="element file" type="file"/> 
		</div>  
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="62685" />
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
			<form method = "post"> 
				<input type="submit" name="Logout" value="Logout" action = "../PHP.php"> 
			</form>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
	</body>
</html>
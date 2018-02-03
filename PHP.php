<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
// define variables and set to empty values
session_start();
$nameErr = $emailErr ="";
$name = $password = "";

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'virustotal';

if($_POST){
// Get user input
if (isset($_POST["name"]) && isset($_POST["password"])){
	$name = $_POST['name'];
	$password = $_POST['password'];

//

$conn = mysqli_connect($host, $user, $pass, $db);

/*
$sql = "insert into user (admin, password) values ('admin', 'password')";
$query = mysqli_query($conn,$sql);
if($query){
	echo "Insert succeeded";
}
else{
	echo "Failed";
}
*/

// Sanitize input
$name = stripcslashes($name);
$password = stripcslashes($password);
$name = mysqli_real_escape_string($conn, $name);
$password = mysqli_real_escape_string($conn, $password);

// Check input to login
$result = mysqli_query($conn, "Select * from user where admin = '$name' and password = '$password'")
			or die ("Failed to query db ".mysqli_error($conn));
$row = mysqli_fetch_array( $result);
if($row['admin'] == $name && $row['password'] == $password &&(""!== $name || ""!==$password)){
	//echo "Login success ".$row['admin'];
	startSession($name);
	header('Location: http://localhost/form/form.php');
	
	$conn->close();
	//include();
}
else{
	//echo "Failed to login<br>";
}
}
}
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $password = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    //if (!filter_var($password, FILTER_VALIDATE_REGEXP)) {
    //  $emailErr = "Invalid password format"; 
    //}
  }
    
  
  
}
*/
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function startSession($admin){
	$sessionTime = time() + 86400*30;
	$cookie_name = 'token';
	$cookie_value = time();
	
	setcookie($cookie_name, $cookie_value, $sessionTime, "/");
	
	$_SESSION['admin'] = $admin ;
	$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $cookie_value);
}
?>
 
<h2>Virus Total</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Admin: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Password: <input type="password" name="password" value="<?php echo $password;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  
  <input type="submit" name="submit" value="Submit">  
</form>


</body>
</html>
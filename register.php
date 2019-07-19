  <?php
ob_start();
session_start(); // start a new session or continues the previous
if( isset($_SESSION['user'])!="" ){
 header("Location: home.php" ); // redirects to home.php
}
include_once 'dbconnect.php';
$error = false;
if ( isset($_POST['btn-signup']) ) {
 
 // sanitize user input to prevent sql injection
 $name = trim($_POST['name']);

  //trim - strips whitespace (or other characters) from the beginning and end of a string
  $name = strip_tags($name);

  // strip_tags — strips HTML and PHP tags from a string

  $name = htmlspecialchars($name);
 // htmlspecialchars converts special characters to HTML entities
 $email = trim($_POST[ 'email']);
 $email = strip_tags($email);
 $email = htmlspecialchars($email);

 $pass = trim($_POST['pass']);
 $pass = strip_tags($pass);
 $pass = htmlspecialchars($pass);

 $pass2 = trim($_POST['pass2']);
 $pass2 = strip_tags($pass2);
 $pass2 = htmlspecialchars($pass2);

 $image = trim($_POST['image']);
 $image = strip_tags($image);
 $image = htmlspecialchars($image);

  // basic name validation
 if (empty($name)) {
  $error = true ;
  $nameError = "Please enter your full name.";
 } else if (strlen($name) < 3) {
  $error = true;
  $nameError = "Name must have at least 3 characters.";
 } else if (!preg_match("/^[a-zA-Z ]+$/",$name)) {
  $error = true ;
  $nameError = "Name must contain alphabets and space.";
 }

 //basic email validation
  if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
  $error = true;
  $emailError = "Please enter valid email address." ;
 } else {
  // checks whether the email exists or not
  $query = "SELECT userEmail FROM users WHERE userEmail='$email'";
  $result = mysqli_query($conn, $query);
  $count = mysqli_num_rows($result);
  if($count!=0){
   $error = true;
   $emailError = "Provided Email is already in use.";
  }
 }
 // password validation
  if (empty($pass)){
  $error = true;
  $passError = "Please enter password.";
 } else if(strlen($pass) < 6) {
  $error = true;
  $passError = "Password must have atleast 6 characters." ;
 }
  if($pass != $pass2) {
        echo 'Die Passwörter müssen übereinstimmen<br>';
        $error = true;
    }


 // password hashing for security
$message = "";
   // check to see if ok button was pressed
       $safeDir = __DIR__.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR;
       $filename = basename($_FILES['image']['name']);
       $ext = substr($filename, strrpos($filename, '.') + 1);
       //check to see if upload parameter specified

       if(($_FILES["image"]["error"]==UPLOAD_ERR_OK) && (($ext == "jpg") || ($ext = "png")) && (($_FILES["image"]["type"] == "image/jpeg") || ($_FILES["image"]["type"] == "image/png")) && ($_FILES["image"]["size"] < 70000000)){
           //check to make sure file uploaded by upload process
           if(is_uploaded_file($_FILES["image"]["tmp_name"])){
               // capture filename and strip out any directory path info
               $fn = basename($_FILES["image"]["name"]);
               //Build now filename with safty measures in place
               $copyfile = $safeDir."safe_prefix_secure_info".strip_tags($fn);
               $image="safe_prefix_secure_info".strip_tags($fn);
               //copy file to safe directory
               if(move_uploaded_file($_FILES["image"]["tmp_name"], $copyfile)){
                   $message .= "<br>Successfully uploaded file $copyfile\n";
               } else {
                   // trap upload file handle errors
                   $message.="Unable to upload file ".$_FILES["image"]["name"];
               }
           } else {
               $message .= "<br>File not uploaded";
           }
       }

/* $target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} */

    if (empty($image)) {
  $error = true ;
  $imageError = "Choose Image";
 }


 // if there's no error, continue to signup
 if( !$error ) {
  $password = hash('sha256', $pass);
  $query = "INSERT INTO users(userName,userEmail,userPass, userImage) VALUES('$name','$email','$password', '$image')";
  $res = mysqli_query($conn, $query);
  
  if ($res) {
   $errTyp = "success";
   $errMSG = "Successfully registered, you may login now";
   unset($name);
    unset($email);
   unset($pass);
  } else  {
   $errTyp = "danger";
   $errMSG = "Something went wrong, try again later..." ;
  }
  
 }


}
?>
<!DOCTYPE html> 
<html>
<head>
<title>Login & Registration System</title>
<link rel="stylesheet"  href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"  crossorigin="anonymous">
</head>
<body>
   <form method="post"  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"  autocomplete="off" enctype="multipart/form-data">
  
      
            <h2>Sign Up.</h2>
            <hr />
          
            <?php
   if ( isset($errMSG) ) {
  
   ?> 
           <div  class="alert alert-<?php echo $errTyp ?>" >
                         <?php echo  $errMSG; ?>
       </div>

<?php 
  }
  ?> 
          
      
          

            <input type ="text"  name="name"  class ="form-control"  placeholder ="Enter Name"  maxlength ="50"   value = "<?php echo $name ?>"  />
      
               <span   class = "text-danger" > <?php   echo  $nameError; ?> </span >
          
    

            <input   type = "email"   name = "email"   class = "form-control"   placeholder = "Enter Your Email"   maxlength = "40"   value = "<?php echo $email ?>"  />
    
               <span   class = "text-danger" > <?php   echo  $emailError; ?> </span >
      
          
      
            
        
            <input   type = "password"   name = "pass"   class = "form-control"   placeholder = "Enter Password"   maxlength = "15"  />

            <input   type = "password"   name = "pass2"   class = "form-control"   placeholder = "Enter Password Again to compare"   maxlength = "15"  />
            
               <span   class = "text-danger" > <?php   echo  $passError; ?> </span >

            <input type="file" name="image" size="50" maxlength="255" value="">
            
              
      
            <hr />

          
            <button   type = "submit"   class = "btn btn-block btn-primary"   name = "btn-signup" >Sign Up</button >
            <hr  />
          
            <a   href = "index.php" >Sign in Here...</a>
    
  
   </form>
</body >
</html >
<?php  ob_end_flush(); ?>
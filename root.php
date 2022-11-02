<?php
//Check the type of the form posted
switch ($_POST["FormType"]) {
        case "otp":
            Inventory::Fetch(''.$_POST['email'].'');
            break;
        case "otp_validation":
            Inventory::Crud(''.$_POST['email'].'', ''.$_POST['otp'].'');
            break;
        case "registration":
            Inventory::Registration(''.$_POST['fname'].'',''.$_POST['lname'].'',''.$_POST['email'].'',''.$_POST['country'].'');
            break;
        default:
            require_once '../404.html';
            break;
}

//Class Inventory
class Inventory{
    public function Fetch($email){
        try {
            if (! @include_once( 'db.php' ))
                throw new Exception ('Database connection error');
            if (!file_exists('db.php' )){
                throw new Exception ('Database connection error');
            }
            else{
                require_once('db.php' ); 
                //VALIDATE EMAIL
                $domains = array('gmail.com', 'outlook.com', 'yahoo.in', 'yahoo.com', 'hotmail.com');
                $pattern = "/^[a-z0-9._%+-]+@[a-z0-9.-]*(" . implode('|', $domains) . ")$/i";
                if ($email == ""){
                    print("failure: Please provide your email");
                 }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    print("failure: Invalid email address");
                } else if (!preg_match($pattern, $email)) {
                    print("failure: Temporary emails not allowed");
                } else{
                    //Check whether user is already registered or not
                    if ($conn->query("SELECT * FROM Registered_users WHERE email = '$email'")->num_rows > 0) {
                        print("failure: Email is already in use");
                        return 1;
                    }
                    //Generate otp
                    function GenerateOTP($n) {
	                    $generator = "4698320157";
	                    $result = "";
	                    for ($i = 1; $i <= $n; $i++) {
		                    $result .= substr($generator, (rand()%(strlen($generator))), 1);
	                    }
	                    return $result;
                    }
                    $n = 4;
                    $otp = GenerateOTP($n);
                    //Check for email
                    if ($conn->query("SELECT email FROM RegForm WHERE email = '$email'")->num_rows > 0){
                        $conn->query("DELETE FROM RegForm WHERE email='$email'") === TRUE;
                    }
                    //Insert into DB
                    $sql = "INSERT INTO RegForm (email, otp) VALUES ('$email', '$otp')";
                    mysqli_query($conn, $sql);
                    //Mail OTP to the user
                    $headers = "From: noreplay@bboysdreamsfell.tech" . "\r\n" . "CC: noreplay@bboysdreamsfell.tech";
                    mail($email,"Verification code","$otp",$headers) ? print('verify_otp: <span class="flex"><input type="tel" name="otp" id="otp" placeholder="OTP*" maxlength="4" oninput="Validate(1)"> <button class="btn btn2 none" id="otp_btn" onclick="event.preventDefault();Validate(2)">Resend</button></span>') : print_r("failure: Please try again");
                }
            }
        }
        catch(Exception $e) {    
            print($e->getMessage());
            return 1;
        }  
    }
    
    public function Crud($email, $otp){
        try {
            if (! @include_once( 'db.php' ))
                throw new Exception ('Database connection error');
            if (!file_exists('db.php' )){
                throw new Exception ('Database connection error');
            }
            else{
                require_once('db.php' ); 
                //Validate OTP
                if (strlen($otp)<=3){
                    print("OTP is not valid");
                    return 1;
                } else {
                    //Fetch for otp in DB
                    if ($conn->query("SELECT * FROM RegForm WHERE email = '$email' AND otp = '$otp' ORDER BY id DESC")->num_rows > 0) {
                        $conn->query("UPDATE RegForm SET otp='1' WHERE email='$email'") === TRUE;
                        print('verify_otp: <button class="btn btn2 none"><i class="fa fa-check"></i> Verified</button>');
                    } else {
                        print("failure: Invalid OTP");
                    }
                }
            }
        }
        catch(Exception $e) {    
            print($e->getMessage());
            return 1;
        }  
    }
    
    public function Registration($fname, $lname, $email, $country){
        try {
            if (! @include_once( 'db.php' ))
                throw new Exception ('Database connection error');
            if (!file_exists('db.php' )){
                throw new Exception ('Database connection error');
            }
            else{
                require_once('db.php' ); 
                //Fetch for otp in DB
                if ($conn->query("SELECT * FROM RegForm WHERE email = '$email' AND otp = 1 ORDER BY id DESC")->num_rows > 0) {
                    if ($conn->query("SELECT * FROM Registered_users WHERE email = '$email'")->num_rows > 0) {
                        print("failure: Email is already registered");
                        return 1;
                    }
                    if ($fname == "" || $lname == "" || $email == "" || $country == "0"){
                        print("All fields are mandatory");
                         return 1;
                    }
                    $fname = trim($fname,'\'"');
                    $lname = trim($lname,'\'"');
                    //Upload images
                    $dir = "uploads/";
                    $file_1 = $dir . basename($_FILES["id_proof"]["name"]);
                    $file_2 = $dir . basename($_FILES["addr_proof"]["name"]);
                    $FileType_1 = strtolower(pathinfo($file_1,PATHINFO_EXTENSION));
                    $$FileType_2 = strtolower(pathinfo($file_2,PATHINFO_EXTENSION));
                    //
                    //
                    //               Validate &
                    //      Upload your file here
                    //
                    //
                    //Insert into Registered users DB
                    $sql = "INSERT INTO Registered_users (email, fname,lname) VALUES ('$email', '$fname', '$lname')";
                    mysqli_query($conn, $sql);
                    print("success: Registration successfull");
                    return 1;
                    } else {
                        print("failure: Verify your email to proceed");
                        return 1;
                    }
            }
        }
        catch(Exception $e) {    
            print($e->getMessage());
            return 1;
        }
    }
}
?>

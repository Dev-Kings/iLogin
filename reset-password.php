<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !==true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$new_password=$confirm_password="";
$new_password_err=$confirm_password_err="";

//processing form data on submitting
if($_SERVER["REQUEST_METHOD"]=="POST"){
    //validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err="Enter new password.";
    }elseif(strlen(trim($_POST["new_password"]))<8){
        $new_password_err="Password must be atleast 8 characters.";
    }else{
        $new_password=trim($_POST["new_password"]);
    }
    //validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err="Confirm Password.";
    }else{
        $confirm_password=trim($_POST["confirm_password"]);
        if(empty($new_password_err)&&($new_password!=$confirm_password)){
            $confirm_password_err="Password mismacth!";
        }
    }
    //check input for errors before udating DB
    if(empty($new_password_err)&&empty($confirm_password_err)){
        //Prepare update statement

        $sql = "UPDATE users SET password=? WHERE id=?";
        if($stmt=mysqli_prepare($conn, $sql)){
            //Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt,"si",$param_password,$param_id);

            //Set Parameters
            $param_password=password_hash($new_password, PASSWORD_DEFAULT);
            $param_id=$_SESSION["id"];

            //Execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //Password updated succesfully.Destroy session then redirect back to login.
                session_destroy();
                header("location: login.php");
                exit();
            }else{
                echo "Something went wrong. Retry later.";
            }
            //Close statement
            mysqli_stmt_close($stmt);
        }
    }
    //Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Reset Password</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body{font: 14px sans-serif;}
            .wrapper{width: 360px; padding: 30px; margin: auto;}
        </style>
    </head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Fill out to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group">
            <label>New password</label>
            <input type="password" name="new_password" class="form-control <?php echo(!empty($new_password_err))
            ? 'is-invalid': ''; ?>" value ="<?php echo $new_password; ?>">
            <span class="invalid-feedback"><?php echo $new_password_err; ?>
            </span>
        </div>

        <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="confirm_password" class="form-control <?php echo(!empty($confirm_password_err))
            ? 'is-invalid': ''; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?>
            </span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a class="btn btn-link ml-2" href="welcome.php">Cancel</a>
        </div>
        </form>
    </div>
</body>
</html>
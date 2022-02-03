<?php
require_once "config.php";

$username=$password=$confirm_password="";
$username_err=$password_err=$confirm_password_err="";

//processing data submitted via form
if($_SERVER["REQUEST_METHOD"]=="POST"){
    //Validate username
    if(empty(trim($_POST["username"]))){
        $username_err="Please enter username.";
    }elseif(!preg_match('/^[a-zA-Z0-9_]+$/',trim($_POST["username"]))){
        $username_err="Username should only contain letters, numbers and underscores.";
    }else{
        //prepare select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt=mysqli_prepare($conn, $sql)){
            //Binding variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt,"s",$param_username);

            //Set paramaters
            $param_username = trim($_POST["username"]);

            //Execute the prepared statement
            if(mysqli_stmt_execute($stmt)){

                //Store result
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt)==1){
                    $username_err="Username taken!";
                }else{
                    $username = trim($_POST["username"]);
                }
                }else{
                    echo "Something went wrong. Retry later again.";
                }
                
            //Close statement
            mysqli_stmt_close($stmt);
        }
    }

    //Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter password.";
    }elseif(strlen(trim($_POST["password"]))<8){
        $password_err = "Password should not be less than 8 characters.";
    }else{
        $password = trim($_POST["password"]);
    }

//Validate confirm password
if(empty(trim($_POST["confirm_password"]))){
    $confirm_password_err = "Please confirm password.";
}else{
    $confirm_password = trim($_POST["confirm_password"]);
    if(empty($password_err) && ($password != $confirm_password)){
        $confirm_password_err = "Password mismacth!";
    }
}
//confirm input prior to inserting in DB
if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
    //Prepare insert statement

    $sql = "INSERT INTO users(username,password) VALUES(?,?)";

    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt,"ss",$param_username,$param_password);

        //Set parameters and create a password hash

        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);

        //Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            //Redirect to login page
            header("location: login.php");
        }else{
            echo "Something went wrong. Retry later.";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body{font: 14px sans-serif;}
            .wrapper{width: 350px; padding: 20px; margin: auto;}
        </style>
    </head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Fill the form to register.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control <?php echo(!empty($username_err))
            ? 'is-invalid' : '';?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control <?php echo(!empty($password_err))
            ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control <?php echo(!empty($confirm_password_err))
            ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary ml-2"  value="Reset">
        </div>
        <p>Already have an account?<a href="login.php">Login Here</a>.</p>
</form>
</div>
</body>
</html>

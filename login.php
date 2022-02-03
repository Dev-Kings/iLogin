<?php
//start session
session_start();

//check if user is logged in, or else redirect home
if(isset($_GET["loggedin"]) && $_GET["loggedin"]===true){
    header("location: welcome.php");
    exit();
}

//include config file
require_once "config.php";

$username=$password="";
$username_err=$password_err=$login_err="";

//processing form data on submitting
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(empty(trim($_POST["username"]))){
        $username_err="Enter username";
    }else{
        $username=trim($_POST["username"]);
    }

    //check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err="Enter password";
    }else{
        $password=trim($_POST["password"]);
    }

    //Validate credentials
    if(empty($username_err) && empty($password_err)){

        //Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($conn, $sql)){
            //Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt,"s",$param_username
        );

            //Set parameters
            $param_username=$username;

            //Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //Store result
                mysqli_stmt_store_result($stmt);

                //check if username exists, if yes, verify password
                if(mysqli_stmt_num_rows($stmt) == 1){
                    //Bind result variables
                    mysqli_stmt_bind_result($stmt,$id,$username,$hashed_password);

                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            //Password is correct, then start a new session
                            session_start();

                            //store data in session variables
                            $_SESSION["loggedin"]=true;
                            $_SESSION["id"]=$id;
                            $_SESSION["username"]=$username;

                            //Redirect user to welcome page
                            header("location: welcome.php");
                        }else{
                            //password invalid
//  ERROR, INVALID USERNAME OR PASSWORD
                            $login_err="Invalid username or password!";
                        }
                    }
                }else{
                    //username doesn't exist
                    $login_err="Invalid username or password.";
                }
            }else{
                echo "Error occured. Retry later.";
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
        <title>Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body{font: 14px sans-serif; body-align: center;}
            .wrapper{width: 360px; padding: 20px; margin: auto;}
        </style>
    </head>
        <body>
            <div class="wrapper">
                <h2>Login</h2>
                <p>Please fill your credentials to login.</p>

                <?php
                if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' .$login_err. '</div>';
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo(!empty($username_err))
                    ? 'is-invalid':''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo(!empty($password_err))
                    ? 'is-invalid': ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?>
                    </span>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                    <p>Not Registered?<a href="register.php">
                        Sign Up</a>.
                    </p>
            </form>
            </div>
        </body>
</html>


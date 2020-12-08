<?php
    session_start();
    require_once "pdo.php";
    include_once "util.php";

    $salt = 'XyZzy12*_';

    // Check to see if we have some POST data, if we do process it
    if ( isset($_POST['email']) && isset($_POST['pass']) ) {
        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
            $_SESSION['error'] = "Both fields are required";
            header("Location: login.php");
            return;
        }            
        else{
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users
                            WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ( $row !== false ) 
            {
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                error_log("Login success :".$_SESSION['name']);
                header("Location: index.php");
                return;
            } else {
                $_SESSION['error'] = "Incorrect password";
                error_log("Login fail ".$_POST['email']." $check");
                header("Location: login.php");
                return;
            }
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alejandro Garcia Muñoz (8c7a1f68) - users DB</title>
  <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?php
            flashMessages();
        ?>
        <form method="post">
            <p><b>Email:</b>
                <input type="text" size="40" name="email" id="id_email"></p>
            <p><b>Password:</b>
                <input type="password" size="40" name="pass"></p>
            <p>
                <input type="submit" onclick="return doValidate();" value="Log In">
                <a href="index.php">Cancel</a>
            </p>
        </form>
        <p>
        For a password hint, view source and find a password hint
        in the HTML comments.
        <!-- Pro-gamer move: The account is umsi@umich.edu, and the password is php (all lower case) followed by 123. -->
        </p>
        <script>
            function doValidate() {
                console.log('Validating...');
                try {
                    em = document.getElementById('id_email').value;
                    console.log("Validating email="+em);
                    if (!em.includes("@")) {
                        alert("Email address must contain @");
                        return false;
                    }
                    return true;
                } catch(e) {
                    return false;
                }
                    return false;
            }
        </script>
    </div>
</body>
</html>
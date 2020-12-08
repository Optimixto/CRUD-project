<?php
    session_start();
    require_once "pdo.php";
    include_once "util.php";
    
    // Logged in check
    if ( ! isset($_SESSION['name']) ) {
        die('Not logged in');
    }

    //Cancel handler
    if ( isset($_POST['cancel']) ) {
        header('Location: index.php');
        return;
    }

    // Input handler
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if(empty($_POST["first_name"]) || empty($_POST["last_name"]) 
            || empty($_POST["email"]) || empty($_POST["headline"]) || empty($_POST["summary"]))
        {
            $_SESSION['error'] = "All fields are required";
            header("Location: add.php");
            return;
        }
        elseif(strpos(($_POST['email']), '@') == false)
        {
            $_SESSION['error'] = "Email address must contain @";
            header("Location: add.php");
            return;
        }
        else
        {
            $stmt = $pdo->prepare('INSERT INTO Profile
                (user_id, first_name, last_name, email, headline, summary)
                VALUES ( :uid, :fn, :ln, :em, :he, :su)');

            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            $_SESSION['success'] = "Record added";
            header("Location: index.php");
            return;
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - users DB</title>
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Adding profile for <?php echo($_SESSION['name']);?></h1>
        
        <?php
            flashMessages();
        ?>
        <form method="post">
            <p>First Name:
            <input type="text" name="first_name" size="40"></p>
            <p>Last Name:
            <input type="text" name="last_name" size="40"></p>
            <p>Email:
            <input type="text" name="email" size="30"></p>
            <p>Headline:</p>
            <p><input type="text" name="headline" size="40"></p>
            <p>Summary:</p>
            <p><textarea name="summary" rows="8" cols="60"></textarea>
            <p>
                <input type="submit" value="Add"/>
                <input type="submit" name="cancel" value="Cancel"/>
            </p>
        </form>
    </div>
</body>
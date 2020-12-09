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
    
    //Makes sure profile_id is present
    if ( !isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }

    //Grabs data to fill form
    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false )
    {
        $_SESSION['error'] = 'Bad value for profile_id';
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
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }
        elseif(strpos(($_POST['email']), '@') == false)
        {
            $_SESSION['error'] = "Email address must contain @";
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }
        else
        {
            $sql = "UPDATE profile SET first_name = :fn,
                    last_name =:ln, email =:em, headline =:he, summary =:su WHERE profile_id = :pid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'],
                ':pid' => $_GET['profile_id'])
            );
            $_SESSION['success'] = "Record edited";
            header("Location: index.php");
            return;
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - profiles DB</title>
    <?php include "headImports.php" ?>
</head>
<body>
    <div class="container">
        <h1>Editing profile for <?php echo($_SESSION['name']);?></h1>
        
        <h2>Editing usermobile</h2>
        <?php
            flashMessages();

            //Placeholder vars for current values
            $fn = htmlentities($row['first_name']);
            $ln = htmlentities($row['last_name']);
            $em = htmlentities($row['email']);
            $he = htmlentities($row['headline']);
            $su = htmlentities($row['summary']);
            $user_id = $row['user_id'];
            $profile_id = $_GET['profile_id'];
        ?>

        <form method="post">
            <p><b>First Name:</b>
            <input type="text" name="first_name" size="40" value="<?= $fn ?>"></p>
            <p><b>Last Name:</b>
            <input type="text" name="last_name" size="40" value="<?= $ln ?>"></p>
            <p><b>Email:</b>
            <input type="text" name="email" size="30" value="<?= $em ?>"></p>
            <p><b>Headline:</b></p>
            <p><input type="text" name="headline" size="40" value="<?= $he ?>"></p>
            <p><b>Summary:</b></p>
            <p><textarea name="summary" rows="8" cols="60"><?= $su ?></textarea>
            <p>
                <input type="submit" value="Save"/>
                <input type="submit" name="cancel" value="Cancel"/>
            </p>
        </form>
    </div>
</body>
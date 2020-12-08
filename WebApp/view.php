<?php
    session_start();
    require_once "pdo.php";
    include_once "util.php";
    
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - profiles DB</title>
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Viewing profile</h1>
        
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
            <p><b>First Name:</b> <?= $fn ?></p>
            <p><b>Last Name:</b><?= $ln ?></p>
            <p><b>Email:</b> <?= $em ?></p>
            <p><b>Headline:</b></p>
            <p><?= $he ?></p>
            <p><b>Summary:</b></p>
            <p><?= $su ?></p>
            <p>
                <a href="index.php">Back</a>
            </p>
        </form>
    </div>
</body>
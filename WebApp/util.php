<?php

    function flashMessages(){
        if ( isset($_SESSION['error']) ) {
            echo ('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
        }
        if ( isset($_SESSION['success']) ) {
            echo ('<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }
    }

    //returns a string if there is an error
    function validatePos() {
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;
      
          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];
      
          if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
          }
      
          if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
          }
        }
        return true;
    }

    function validateProfile() {
        if(empty($_POST["first_name"]) || empty($_POST["last_name"]) 
            || empty($_POST["email"]) || empty($_POST["headline"]) || empty($_POST["summary"]))
        {
            return "All fields are required";
        }
        elseif(strpos(($_POST['email']), '@') == false)
        {
            return "Email address must contain @";
        }
        return true;
    }

    function loadPos($pdo, $profile_id)
    {
        $stmt = $pdo->prepare('SELECT * FROM Position
            WHERE profile_id = :prof ORDER BY rank');
        $stmt->execute(array(':prof' => $profile_id));
        $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $positions;
    }

    function loadProfile($pdo, $profile_id){
        $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
        $stmt->execute(array(":xyz" => $profile_id));
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        return $profile;
    }

    function loadEducation($pdo, $profile_id)
    {
        $stmt = $pdo->prepare("SELECT year, name FROM education
                                JOIN Institution ON Education.institution_id = Institution.institution_id
                                where profile_id = :xyz");
        $stmt->execute(array(":xyz" => $profile_id));
        $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $education;
    }
?>
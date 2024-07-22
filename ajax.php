<?php
// Database credentials
$host = 'host';
$dbname = 'Database';
$user = 'user';
$pass = 'password';

// Function to establish database connection
function getDBConnection() {
    global $host, $dbname, $user, $pass;
    try {
        $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $DBH;
    } catch(PDOException $e) {
        echo $e->getMessage();
        return null;
    }
}

// Function to print the current time from the database
function timej() {
    $DBH = getDBConnection();
    if ($DBH) {
        $qdrtt = $DBH->prepare("DELETE FROM Time_t");
        $qdrtt->execute();
        $qdtt = $DBH->prepare("INSERT INTO Time_t (Time_id) VALUES ('1')");
        $qdtt->execute();
        
        $qjk = $DBH->prepare("SELECT joined_on FROM Time_t WHERE Time_id = '1'");
        $qjk->execute();
        
        $rowt = $qjk->fetch(PDO::FETCH_ASSOC);
        echo $rowt['joined_on'];
    }
}

// Function to log out a user
function login_out() {
    $idtlogin = $_POST['id'];
    $DBH = getDBConnection();
    if ($DBH) {
        $qhaqe = $DBH->prepare("SELECT status, login_id, password FROM Users_td WHERE login_id = ?");
        $qhaqe->execute([$idtlogin]);
        $rowsdqwk = $qhaqe->fetch(PDO::FETCH_ASSOC);
        
        $qdrhu = $DBH->prepare("DELETE FROM Users_td WHERE login_id = ?");
        $qdrhu->execute([$idtlogin]);
        
        $qdrhuj = $DBH->prepare("INSERT INTO Users_td (login_id, password, Status) VALUES (?, ?, '0')");
        $qdrhuj->execute([$idtlogin, $rowsdqwk['password']]);
        
        $qwwwwy = $DBH->prepare("SELECT status FROM Users_td WHERE login_id = ?");
        $qwwwwy->execute([$idtlogin]);
        
        $rowasui = $qwwwwy->fetch(PDO::FETCH_ASSOC);
        echo $rowasui['status'];
    }
}

// Function to log in a user
function loginpage() {
    $login = $_POST['login'];
    $passwordfg = $_POST['passwordfg'];
    $DBH = getDBConnection();
    if ($DBH && strlen($login) > 0 && strlen($passwordfg) > 0) {
        $sql = "SELECT * FROM Users_t WHERE login_id = ? AND password = ?";
        $items = $DBH->prepare($sql);
        $items->execute([$login, $passwordfg]);
        
        $df = 0;
        foreach ($items as $row) {
            $df = 1;
        }
        
        if ($df == 1) {
            echo "1";
            $qdr = $DBH->prepare("DELETE FROM Users_td WHERE login_id = ?");
            $qdr->execute([$login]);
            $qd = $DBH->prepare("INSERT INTO Users_td (login_id, password, Status) VALUES (?, ?, '1')");
            $qd->execute([$login, $passwordfg]);
            
            $qwwww = $DBH->prepare("SELECT status, login_id FROM Users_td WHERE login_id = ?");
            $qwwww->execute([$login]);
            
            $rowas = $qwwww->fetch(PDO::FETCH_ASSOC);
            echo $rowas['status'];
            echo $rowas['login_id'];
        } else {
            echo '<font color="#E2000D"> <br> Password or login wrong please try again !!! </font>';
            echo "0";
        }
    }
}

// Function to check the status by name and question
function checkStatusname() {
    $name = $_POST['app_name'];
    $question = $_POST['app_ques'];
    $DBH = getDBConnection();
    if ($DBH) {
        $qs = "SELECT * FROM iwa2016 WHERE name LIKE ? AND question LIKE ?";
        $items = $DBH->prepare($qs);
        $items->execute(['%' . $name . '%', '%' . $question . '%']);
        
        echo "<table>";
        echo "<tr><td>id</td><td>name</td><td>question</td><td>answer</td><td>Status</td><td>Time</td></tr>";
        foreach ($items as $row) {
            echo "<tr>";
            for ($i = 0; $i <= 5; $i++) {
                echo "<td>" . $row[$i] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Function to check the status of a question
function checkQuestionStatus() {
    $DBH = getDBConnection();
    if ($DBH) {
        $qh = $DBH->prepare("SELECT status FROM Status_tb WHERE id = 1");
        $qh->execute();
        $rowsd = $qh->fetch(PDO::FETCH_ASSOC);
        $rtyu = implode(" ", $rowsd);
        
        if ($rtyu != "0") {
            $q = $DBH->prepare("SELECT answer FROM iwa2016 WHERE id = ?");
            $q->execute([$rtyu]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            $rtyudd = implode(" ", $row);
            
            if ($rtyudd != "0") {
                echo "<br>You got an answer for your last Question! <br><br>";
                $qa = "SELECT * FROM iwa2016 WHERE id = ?";
                $items = $DBH->prepare($qa);
                $items->execute([$rtyu]);
                
                echo "<table>";
                echo "<tr><td>id</td><td>name</td><td>question</td><td>answer</td><td>Status</td><td>Time</td></tr>";
                foreach ($items as $rowf) {
                    echo "<tr>";
                    for ($i = 0; $i <= 4; $i++) {
                        echo "<td>" . $rowf[$i] . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br> answer: " . $row['answer'];
            } else {
                echo "No answer yet";
            }
        }
    }
}

// Function to submit a new question
function submitNewQuestion() {
    $name = $_POST['app_name'];
    $question = $_POST['app_ques'];
    $DBH = getDBConnection();
    if ($DBH) {
        $qs = "SELECT * FROM iwa2016 WHERE name LIKE ? AND question LIKE ?";
        $items = $DBH->prepare($qs);
        $items->execute(['%' . $name . '%', '%' . $question . '%']);
        
        $df = 0;
        foreach ($items as $row) {
            $df = 1;
        }
        
        if ($df == 0) {
            $q = $DBH->prepare("INSERT INTO iwa2016 (name, question) VALUES (?, ?)");
            $q->execute([$name, $question]);
            $insert = $DBH->lastInsertId();
            
            $qdr = $DBH->prepare("DELETE FROM Status_tb WHERE id = 1");
            $qdr->execute();
            $qd = $DBH->prepare("INSERT INTO Status_tb (id, Name_status, Status) VALUES (1, 'Last_Status', ?)");
            $qd->execute([$insert]);
            
            $qh = $DBH->prepare("SELECT name, question, joined_on FROM iwa2016 WHERE name LIKE ? AND question LIKE ?");
            $qh->execute(['%' . $name . '%', '%' . $question . '%']);
            $rowsd = $qh->fetch(PDO::FETCH_ASSOC);
            echo $rowsd['name'] . " " . $rowsd['question'] . " " . $rowsd['joined_on'];
        }
    }
}

// Function to answer a question
function answerq() {
    $name = $_POST['app_namei'];
    $question = $_POST['app_quesi'];
    $answerq = $_POST['app_answer'];
    $supps = $_POST['id'];
    $DBH = getDBConnection();
    if ($DBH) {
        $qs = "SELECT * FROM iwa2016 WHERE name LIKE ? AND question LIKE ?";
        $items = $DBH->prepare($qs);
        $items->execute(['%' . $name . '%', '%' . $question . '%']);
        
        $df = 0;
        foreach ($items as $row) {
            $df = 1;
        }
        
        if ($df == 1 && $supps == "29999999") {
            $qh = $DBH->prepare("SELECT name, question, joined_on FROM iwa2016 WHERE name LIKE ? AND question LIKE ?");
            
            $qh->execute([$name, $question]);
            $rowsd = $qh->fetch(PDO::FETCH_ASSOC);
            echo $rowsd['name'] . " " . $rowsd['question'] . " " . $rowsd['joined_on'] . "<br>";

            // Delete the old record
            $qdr = $DBH->prepare("DELETE FROM iwa2016 WHERE name LIKE ? AND question LIKE ?");
            $qdr->execute([$name, $question]);
            
            // Insert the new record with the answer
            $q = $DBH->prepare("INSERT INTO iwa2016 (name, question, answer) VALUES (?, ?, ?)");
            $q->execute([$name, $question, $answerq]);
            $insert = $DBH->lastInsertId();
            
            // Update status
            $qdr = $DBH->prepare("DELETE FROM Status_tb WHERE id = 1");
            $qdr->execute();
            $qd = $DBH->prepare("INSERT INTO Status_tb (id, Name_status, Status) VALUES (1, 'Last_Status', ?)");
            $qd->execute([$insert]);
            
            // Fetch and display the answer
            $qh = $DBH->prepare("SELECT name, answer, joined_on FROM iwa2016 WHERE name LIKE ? AND question LIKE ?");
            $qh->execute([$name, $question]);
            $rowsd = $qh->fetch(PDO::FETCH_ASSOC);
            echo "Support Center " . $rowsd['answer'] . " " . $rowsd['joined_on'];
        }
    }
}

// Route the request to the appropriate function
$type = $_POST['type'];
switch ($type) {
    case 'submitquestion':
        submitNewQuestion();
        break;
    case 'checkstatus':
        $DBH = getDBConnection();
        if ($DBH) {
            $qa = "SELECT status FROM Status_tb WHERE id = 1";
            $items = $DBH->prepare($qa);
            $items->execute();
            
            $df = 0;
            foreach ($items as $rowd) {
                $df = (strlen($rowd[0]) == 1) ? 1 : 0;
            }
            if ($df == 1) {
                echo "1";
                checkQuestionStatus();
            } else {
                $qd = $DBH->prepare("INSERT INTO Status_tb (id, Name_status, Status) VALUES (1, 'Last_Status', '0')");
                $qd->execute();
            }
        }
        break;
    case 'checkstatusname':
        checkStatusname();
        break;
    case 'answerq':
        answerq();
        break;
    case 'login':
        loginpage();
        break;
    case 'login_out':
        login_out();
        break;
    case 'time':
        timej();
        break;
    default:
        echo "Invalid request type.";
        break;
}
?>
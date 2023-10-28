<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<form action="m5-01.php" method="post">
<input type="text" id="name" name="name" placeholder="名前" value="<?php echo isset($newname) ? htmlspecialchars($newname) : ''; ?>">
<input type="text" id="comment" name="comment" placeholder="コメント" value="<?php echo isset($newcomment) ? htmlspecialchars($newcomment) : ''; ?>">
<input type="text" id="pass" name="pass" placeholder="パスワード" value="<?php echo isset($pass) ? htmlspecialchars($pass) : ''; ?>">
<input type="submit" name="add" value="送信"><br>
<input type="hidden" id="editcheck" name="editcheck" value="<?php echo isset($editcheck) ? htmlspecialchars($editcheck) : 0; ?>">
<input type="hidden" id="editline" name="editline" value="<?php echo isset($editline) ? htmlspecialchars($editline) : ''; ?>">
<input type="text" id="rcode" name="rcode" placeholder="削除番号">
<input type="text" id="rpass" name="rpass" placeholder="パスワード">
<input type="submit" name="remove" value="削除"><br>
<input type="text" id="ecode" name="ecode" placeholder="編集番号">
<input type="text" id="epass" name="epass" placeholder="パスワード">
<input type="submit" name="edit" value="編集"><br>
</form>

<?php
#データベースへの接続とテーブル作成
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS mtb"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "pass TEXT,"
    . "date TEXT"
    .");";
    $stmt = $pdo->query($sql);
#$filename = "mission_5-1.txt";
#$lines = file($filename, FILE_IGNORE_NEW_LINES);
#変数の初期化
if (isset($_POST["name"])){
$name = $_POST["name"];
}
if (isset($_POST["comment"])){
$comment = $_POST["comment"];
}
if (isset($_POST["pass"])){
$pass = $_POST["pass"];
}
$date = date("Y/m/d H:i:s");
if (isset($_POST["add"])&&!empty($_POST["name"]) && !empty($_POST["comment"])&&!empty($_POST["pass"])) {
    if ($_POST["editcheck"]==0) { #送信
        $sql = "INSERT INTO mtb (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        #$fp = fopen($filename, "a");
        #if ($lines !== false) { #行数カウント
        #    $lastLine = $lines[count($lines) - 1];
        #   $lastLine = explode("<>", $lastLine);
        #    $last = intval($lastLine[0]);
        #    $lineCount = $last + 1;
        #} else {
        #    $lineCount = 1;
        #}
        #fwrite($fp, $lineCount . "<>" . $name . "<>" . $comment . "<>" . $date . "<>" . $pass . PHP_EOL);
        #fclose($fp);
    }elseif($_POST["editcheck"]==1) { #編集
        $_POST["editcheck"]=0;
        $id = $_POST['editline']; //変更する投稿番号
        $sql = 'UPDATE mtb SET name=:name,comment=:comment,pass=:pass ,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // foreach ($lines as $check) { #新しい内容
        //     $check = explode("<>", $check);
        //     if ($check[0]==$_POST["editline"]) {
        //         $newtext=$check[0] . "<>" . $name . "<>" . $comment . "<>" . $date."<>" . $pass . PHP_EOL;
        //     }
        // }
        // $fp = fopen($filename, 'r');
        // if ($fp) { #編集
        //     $tempFile = tempnam(sys_get_temp_dir(), 'temp_file');
        //     while (($line = fgets($fp)) !== false) {
        //         $memo = explode("<>", $line);
        //         $n = $memo[0];
        //     if ($n == $_POST['editline']) {
        //         file_put_contents($tempFile, $newtext, FILE_APPEND);
        //     }else {
        //         file_put_contents($tempFile, $line, FILE_APPEND);
        //     }
        //     }
        // fclose($fp);
        // unlink($filename);
        // rename($tempFile, $filename);
        // }
    }
} elseif (isset($_POST["remove"])) { #削除
    if (!empty($_POST["rcode"])&&!empty($_POST["rpass"])) {
    $sql = 'SELECT * FROM mtb';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if ($row['id']==$_POST["rcode"]){
            if ($row['pass']==$_POST["rpass"]){
        $id = $row['id'];
        $sql = 'delete from mtb where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
            }
            else{
            echo "パスワードが違います"."<br>";
            }
        }
    }
    // if (!empty($_POST["rcode"])&&!empty($_POST["rpass"])) {
    //     $rcode = $_POST["rcode"];
    //     foreach ($lines as $sen) {
    //         $sen = explode("<>", $sen);
    //         $n=$sen[0];
    //         if (!empty($n)) {
    //             if ($n == $_POST["rcode"]) {
    //                 $checkpass = $sen[4];
    //                 $file = fopen($filename, 'r');
    //                 if($_POST["rpass"]==$checkpass){
    //                     if ($file) {
    //                         $tempFile = tempnam(sys_get_temp_dir(), 'temp_file');
    //                         while (($line = fgets($file)) !== false) {
    //                             $memo = explode("<>", $line);
    //                             $n = $memo[0];
    //                             if ($n != $rcode) {
    //                                 file_put_contents($tempFile, $line, FILE_APPEND);
    //                             }
    //                         }
    //                         fclose($file);
    //                         unlink($filename);
    //                         rename($tempFile, $filename);
    //                     }
    //                 }
    //                 else{
    //                     echo "パスワードが違います"."<br>";
    //                 }
    //             }
    //         }
    //     }
    // }
}
}elseif (isset($_POST["edit"])){ # 編集準備
    if (!empty($_POST["ecode"])&&!empty($_POST["epass"])) {
    $sql = 'SELECT * FROM mtb';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if ($row['id']==$_POST["ecode"]){
            if ($row['pass']==$_POST["epass"]){
            $editcheck=1;
            $editline=$_POST["ecode"];
                echo '<script>';
                echo 'document.getElementById("name").value = "' . htmlspecialchars($row['name']) . '";';
                echo 'document.getElementById("comment").value = "' . htmlspecialchars($row['comment']) . '";';
                echo 'document.getElementById("pass").value = "' . htmlspecialchars($row['pass']) . '";';
                echo 'document.getElementById("editcheck").value = "' . htmlspecialchars($editcheck) . '";';
                echo 'document.getElementById("editline").value = "' . htmlspecialchars($editline) . '";';
                echo '</script>';
            }
            else{
            echo "パスワードが違います"."<br>";
            }
        }
    }
    }
}
// }elseif (isset($_POST["edit"])){ # 編集準備
//     if (!empty($_POST["ecode"])&&!empty($_POST["epass"])) {
//         $ecode = $_POST["ecode"];
//         foreach ($lines as $sen) {
//             $sen = explode("<>", $sen);
//             if (!empty($sen)) {
//                 $n = $sen[0];
//                 if ($n == $ecode) {
//                     echo $sen[4];
//                     $newname = $sen[1];
//                     $newcomment = $sen[2];
//                     $checkpass = $sen[4];
//                     if($_POST["epass"]==$checkpass){
//                     echo"OK"."<br>";
//                     $editcheck=1;
//                     $editline=$ecode;
//                     echo '<script>';
//                     echo 'document.getElementById("name").value = "' . htmlspecialchars($newname) . '";';
//                     echo 'document.getElementById("comment").value = "' . htmlspecialchars($newcomment) . '";';
//                     echo 'document.getElementById("pass").value = "' . htmlspecialchars($checkpass) . '";';
//                     echo 'document.getElementById("editcheck").value = "' . htmlspecialchars($editcheck) . '";';
//                     echo 'document.getElementById("editline").value = "' . htmlspecialchars($editline) . '";';
//                     echo '</script>';
//                     }
//                     else{
//                         echo "パスワードが違います"."<br>";
//                     }
//                 }
//             }
//         }
//     }
// }
// $lines = file($filename, FILE_IGNORE_NEW_LINES);
// if (file_exists($filename)) {
//     foreach ($lines as $sen) {
//         $sen = explode("<>", $sen);
//         if (!empty($sen)) {
//             echo $sen[0] . " " . $sen[1] . " " . $sen[2] . " " . $sen[3] . "<br>";
//         }
//     }
// }
$sql = 'SELECT * FROM mtb';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
//
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].' ';
    echo $row['name'].' ';
    echo $row['comment'].' ';
    echo $row['date'].'<br>';
echo "<hr>";
}
?>
</body>
</html>
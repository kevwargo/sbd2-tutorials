<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title></title>
      </head>
      <body>
<?php


      // ZADANIE 1
      
      $dsn = "localhost:9999/dblab11g";
$username = "inf98835";
$passwd = "inf98835";
$opt = array(PDO::ATTR_CASE=>PDO::CASE_UPPER);
try
{
    //        $conn = new PDO("oci:dbname=".$dsn, $username, $passwd, $opt);
    $conn = new PDO("oci:dbname=".
                    "admlab2-main.cs.put.poznan.pl:1521/dblab01.cs.put.poznan.pl",
                    "inf109789","inf109789");
    
    echo "Połączono z bazą danych! <br/>";
}
catch(PDOException $e)
    {
        echo ($e->getMessage());
    }


//ZADANIE 2

//        $stmt = $conn->prepare("SELECT nazwisko FROM pracownicy WHERE etat=?");
//        $stmt->bindParam(1,$_GET['etat']);
//        $stmt->execute();
//        
//        while($row = $stmt->fetch()) {
//            echo "Pracownik $row[0] <br/>";
//        }
//        $stmt = null;
//          




//ZADANIE 3

//            try{
//                $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//                $id_zesp = 150;
//                $stmt = $conn->prepare("SELECT nazwisko, etat,zatrudniony FROM pracownicy WHERE id_zesp=?");
//                $stmt->bindParam(1, $_GET['nazwa']);
//                $stmt->execute();
//                $i = 0;
//                while($row = $stmt->fetch()) {
//                   echo "Nazwisko: $row[0]        Etat: $row[1]        Zatrudniony: $row[2]<br/>";
//                   $i++;
//                }
//                if($i == 0){
//                  echo "Dla podanego parametru nie znaleziono żadnych obiektów."  ;    
//                }
//            } catch (PDOException $e){
//                echo "Błąd wykonania: ".$e->getMessage();
//            }




//ZADANIE 4

//            $sql = "SELECT nazwisko, placa_pod FROM pracownicy where etat='ASYSTENT' ORDER BY placa_pod DESC";
//            try {
//                $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR =>
//                    PDO::CURSOR_SCROLL));
//                $stmt->execute();
//                $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_LAST);
//                $data = $row[0] . "\t" . $row[1] . "<br/>";
//                print $data;
//                
//                $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_REL, -3);
//                $data = $row[0] . "\t" . $row[1] . "<br/>";
//                print $data;
//                
//                $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_FIRST, 1);
//                $data = $row[0] . "\t" . $row[1] . "<br/>";
//                print $data;
//                
//                $stmt = null;
//            } catch (PDOException $e) {
//                print $e->getMessage();
//            }








//ZADANIE 5

//        $stmt = $conn->prepare("update pracownicy set placa_pod=placa_pod*1.2 where etat = 'ASYSTENT'");
//        $stmt->execute();
//        echo "Liczba zmodyfikowanych wierszy : " . $stmt->rowCount();
//        $stmt = null;




//ZADANIE 6

//        $conn->beginTransaction();
//        $stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
//        $stmt->execute();
//        while($row = $stmt->fetch()) {
//            echo "Etat $row[0] <br/>";
//        }
//        //$conn->commit();
//        $stmt = $conn->prepare("insert into etaty(nazwa, placa_min, placa_max) values('YYYYY', 100, 200)");
//        $stmt->execute();
//        echo "<br/><br/><br/>";
//        $stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
//        $stmt->execute();
//        while($row = $stmt->fetch()) {
//            echo "Etat $row[0] <br/>";
//        }
//        
//        $conn->rollBack();
//
//        $conn->beginTransaction();
//        echo "<br/><br/><br/>";
//        $stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
//        $stmt->execute();
//        while($row = $stmt->fetch()) {
//            echo "Etat $row[0] <br/>";
//        }
//        
//        $stmt = $conn->prepare("insert into etaty(nazwa, placa_min, placa_max) values('YYYYY', 100, 200)");
//        $stmt->execute();
//        
//        $conn->commit();
//        
//        echo "<br/><br/><br/>";
//        $stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
//        $stmt->execute();
//        while($row = $stmt->fetch()) {
//            echo "Etat $row[0] <br/>";
//        }
//        
//        $stmt = null;






//ZADANIE 7

//        $stmt = $conn->prepare("SELECT NAZWISKO, ETAT, PLACA_POD FROM PRACOWNICY WHERE ETAT = ?");
//
//        $etat = 'ASYSTENT';
//        $proc = 50;
//
//        $stmt->bindParam(1,$etat);
//
//        $stmt->execute(); 
//
//            while($row = $stmt->fetch()) {
//                print "Pracownik" . $row[0] . "\t" . "Etat :" . "\t" . $row[1] . "Placa :". "\t" . $row[2] ."<br/>";
//            }
//        
//        $stmt = null;
//        $stmt = $conn->prepare("CALL PPodwyzka(?,?)");
//
//        $stmt->bindParam(1, $etat);
//        $stmt->bindParam(2, $proc);
//        $stmt->execute();
//
//        $stmt = null;
//        $stmt = $conn->prepare("SELECT NAZWISKO, ETAT, PLACA_POD FROM PRACOWNICY WHERE ETAT = ?");
//        $stmt->bindParam(1,$etat);
//        $stmt->execute(); 
//        echo "<br/><br/><br/>";
//        while ($row = $stmt->fetch()) {
//            print "Pracownik" . $row[0] . "\t" . "Etat :" . "\t" . $row[1] . "Placa :" . "\t" . $row[2] . "<br/>";
//        }
//        
//        $stmt = null;






//ZADANIE 8

$sql1 = "select id_zesp, nazwa from ZESPOLY order by nazwa";
$sql2 = "select distinct etat from pracownicy where etat is not null order by etat";
$sql3 = "select count(*) from pracownicy where etat = ? and id_zesp = ?";
$stmt1 = $conn->prepare($sql1);
$stmt1->execute();
        
$stmt2 = $conn->prepare($sql2);
$stmt3 = $conn->prepare($sql3, array(PDO::ATTR_CURSOR =>PDO::CURSOR_SCROLL));
        
echo "<table border=\"2\"> 
            <tr>";
echo "<td></td>"  ;    
while ($row1 = $stmt1->fetch()){
    echo "<td> $row1[1] </td>";
}
echo "</tr>";
     
$stmt2->execute();
while($row2 = $stmt2->fetch(PDO::FETCH_NUM)){
         
    echo "<tr>";
    echo "<td> $row2[0] </td>";
         
    $stmt1->execute();
    while($row1 = $stmt1->fetch()){
        $stmt3->bindParam(1, $row2[0], PDO::PARAM_STR);
        $stmt3->bindParam(2, $row1[0], PDO::PARAM_INT);
        $stmt3->execute();
             
        $row3 = $stmt3->fetch(PDO::FETCH_NUM);
        echo "<td> $row3[0] </td>";
    }
    echo "</tr>";
         
}

echo "</table>";
    
$stmt1 = null;
$stmt2 = null;
$stmt3 = null;
?>
</body>
</html>

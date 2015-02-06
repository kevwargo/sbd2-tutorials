<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     <title></title>
     </head>
     <body>
<?php


     /* ZADANIE 1 */

     $dsn = "admlab2-main.cs.put.poznan.pl:1521/dblab01.cs.put.poznan.pl";
$username = "inf98835";
$passwd = "inf98835";
$opt = array(PDO::ATTR_CASE=>PDO::CASE_UPPER);
try
{
   $conn = new PDO("oci:dbname=".$dsn, $username, $passwd, $opt);
   echo "Połączono z bazą danych! <br/>";
}
catch(PDOException $e)
{
   echo ($e->getMessage());
}


/* ZADANIE 2 */

$stmt = $conn->prepare("SELECT nazwisko FROM pracownicy WHERE etat=?");
$stmt->bindParam(1,$_GET['etat']);
$stmt->execute();

while($row = $stmt->fetch()) {
   echo "Pracownik $row[0] <br/>";
}
$stmt = null;




/* ZADANIE 3 */

try{
   $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
   $stmt = $conn->prepare("SELECT nazwisko, etat, zatrudniony FROM pracownicy p join zespoly z on p.id_zesp = z.id_zesp WHERE nazwa = ?");
   $stmt->bindParam(1, $_GET['nazwa']);
   $stmt->execute();
   $i = 0;
   while($row = $stmt->fetch()) {
      echo "Nazwisko: $row[0]        Etat: $row[1]        Zatrudniony: $row[2]<br/>";
      $i++;
   }
   if($i == 0){
      echo "Dla podanego parametru nie znaleziono żadnych obiektów.";
   }
} catch (PDOException $e){
   echo "Błąd wykonania: ".$e->getMessage();
}
$stmt = null;



/* ZADANIE 4 */

$sql = "SELECT nazwisko, placa_pod FROM pracownicy where etat='ASYSTENT' ORDER BY placa_pod DESC";
try {
   $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR =>
                                      PDO::CURSOR_SCROLL));
   $stmt->execute();
   $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_LAST);
   $data = $row[0] . "\t" . $row[1] . "<br/>";
   echo $data;

   $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_ABS, -3);
   $data = $row[0] . "\t" . $row[1] . "<br/>";
   echo $data;

   $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_ABS, 2);
   $data = $row[0] . "\t" . $row[1] . "<br/>";
   echo $data;

} catch (PDOException $e) {
   echo $e->getMessage();
}
   $stmt = null;







/* ZADANIE 5 */

$stmt = $conn->prepare("update pracownicy set placa_pod=placa_pod*1.2 where etat = 'ASYSTENT'");
$stmt->execute();
echo "Liczba zmodyfikowanych wierszy: " . $stmt->rowCount();
$stmt = null;




/* ZADANIE 6 */

$conn->beginTransaction();
$stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
$stmt->execute();
while($row = $stmt->fetch()) {
   echo "Etat $row[0] <br/>";
}
//$conn->commit();
$stmt = $conn->prepare("insert into etaty(nazwa, placa_min, placa_max) values('KTOS', 100, 200)");
$stmt->execute();
echo "<br/><br/>";
$stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
$stmt->execute();
while($row = $stmt->fetch()) {
   echo "Etat $row[0] <br/>";
}

$conn->rollBack();

$conn->beginTransaction();
echo "<br/><br/>";
$stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
$stmt->execute();
while($row = $stmt->fetch()) {
   echo "Etat $row[0] <br/>";
}

$stmt = $conn->prepare("insert into etaty(nazwa, placa_min, placa_max) values('YYYYY', 100, 200)");
$stmt->execute();

$conn->commit();

echo "<br/><br/><br/>";
$stmt = $conn->prepare("SELECT NAZWA FROM ETATY");
$stmt->execute();
while($row = $stmt->fetch()) {
   echo "Etat $row[0] <br/>";
}

$stmt = null;






/* ZADANIE 7 */

$stmt = $conn->prepare("SELECT NAZWISKO, ETAT, PLACA_POD FROM PRACOWNICY WHERE ETAT = ?");

$etat = 'ASYSTENT';
$proc = 50;

$stmt->bindParam(1,$etat);

$stmt->execute();

while($row = $stmt->fetch()) {
   echo "Pracownik" . $row[0] . "\t" . "Etat :" . "\t" . $row[1] . "Placa :". "\t" . $row[2] ."<br/>";
}

$stmt = null;
$stmt = $conn->prepare("CALL Podwyzka(?,?)");

$stmt->bindParam(1, $proc);
$stmt->bindParam(2, $etat);
$stmt->execute();

$stmt = null;
echo "<br/><br/>";
$stmt = $conn->prepare("SELECT NAZWISKO, ETAT, PLACA_POD FROM PRACOWNICY WHERE ETAT = ?");
$stmt->bindParam(1,$etat);
$stmt->execute();
while ($row = $stmt->fetch()) {
   echo "Pracownik" . $row[0] . "\t" . "Etat :" . "\t" . $row[1] . "Placa :" . "\t" . $row[2] . "<br/>";
}

$stmt = null;






/* ZADANIE 8 */

$sql_zespoly = "select id_zesp, nazwa from ZESPOLY order by nazwa";
$sql_etaty = "select distinct etat from pracownicy where etat is not null order by etat";
$sql_wartosc = "select count(*) from pracownicy where etat = ? and id_zesp = ?";
$stmt_zespoly = $conn->prepare($sql_zespoly);
$stmt_zespoly->execute();

$stmt_etaty = $conn->prepare($sql_etaty);
$stmt_wartosc = $conn->prepare($sql_wartosc, array(PDO::ATTR_CURSOR =>PDO::CURSOR_SCROLL));

echo "<table border=\"2\">"
echo "<tr>";
echo "<td></td>";
while ($row_zespoly = $stmt_zespoly->fetch()){
   echo "<td> $row_zespoly[1] </td>";
}
echo "</tr>";

$stmt_etaty->execute();
while($etat = $stmt_etaty->fetch(PDO::FETCH_NUM)){

   echo "<tr>";
   echo "<td> $etat[0] </td>";

   $stmt_zespoly->execute();
   while($zespol = $stmt_zespoly->fetch()){
      $stmt_wartosc->bindParam(1, $etat[0], PDO::PARAM_STR);
      $stmt_wartosc->bindParam(2, $zespol[0], PDO::PARAM_INT);
      $stmt_wartosc->execute();

      $wartosc = $stmt_wartosc->fetch(PDO::FETCH_NUM);
      echo "<td> $wartosc[0] </td>";
   }
   echo "</tr>";

}

echo "</table>";

$stmt_zespoly = null;
$stmt_etaty = null;
$stmt_wartosc = null;

$conn = null;
?>
</body>
</html>

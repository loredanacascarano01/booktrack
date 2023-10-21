<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new PDO("mysql:host=127.0.0.1:8889;dbname=netflix", 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));


if ( isset($_GET['function']) && $_GET['function'] === 'search' ) {

    echo '<p>Stai cercando il prodotto: '.$_POST['product_sale']; // eventualmente aggiungere messaggio relativo al prezzo

    $product_like = '%'.$_POST['product_sale'].'%';

    // esecuzione SELECT query
    $sql = "SELECT * FROM sales WHERE product_sale LIKE :product_sale AND price_sale = :price_sale";

    $res_prepare = $conn->prepare($sql);

    $res_prepare->bindParam(':product_sale', $product_like);

    $res_prepare->bindParam(':price_sale', $_POST['price_sale']);

    $res_prepare->execute();

    while ( $row = $res_prepare->fetch()){

        echo '<p>prodotto presente nella vendita avente id_sale '.$row['id_sale'];

    }

}

if ( isset($_GET['function']) && $_GET['function'] === 'insert' ) {

   
    // esecuzione SELECT query
    $sql = "insert into sales (product_sale, price_sale, customer_sale) values (:product_sale, :price_sale, :customer_sale) ";

    $res_prepare = $conn->prepare($sql);

    $res_prepare->bindParam(':price_sale', $_POST['price_sale']);
    $res_prepare->bindParam(':product_sale', $_POST['product_sale']);
    $res_prepare->bindParam(':customer_sale', $_POST['customer_sale']);


    $res_prepare->execute();
    
    echo '<p>Il record è stato inserito!';

}


?>


<html>
<head>
    <title>Applicazione Basi di Dati 2023</title>
</head>

<body>

<p>form di ricerca</p>

<form action="index.php?function=search" method="POST">

Prodotto: <input type="text" name="product_sale">
Prezzo: <input type="text" name="price_sale">

<input type="submit" value="Cerca!">

</form>

<p>form di inserimento</p>


<form action="index.php?function=insert" method="POST">

Prodotto: <input type="text" name="product_sale">
Prezzo: <input type="text" name="price_sale">
Cliente: <input type="text" name="customer_sale">

<input type="submit" value="Inserisci!">

</form>



</body>
</html>

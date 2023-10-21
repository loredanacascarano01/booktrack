$host = 'localhost';
$dbname = 'BookTrackDB';
$username = 'root';
$password = 'password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    echo 'Connessione al database riuscita!';
} catch (PDOException $e) {
    echo 'Errore di connessione al database: ' . $e->getMessage();
}

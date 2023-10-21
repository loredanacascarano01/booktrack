<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connessione al database MySQL
$host = '127.0.0.1';
$dbname = 'BookTrackDB';
$username = 'root';
$password = 'password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    // Seleziona il database
    $conn->query("USE `$dbname`;");
} catch (PDOException $e) {
    echo 'Errore di connessione al database: ' . $e->getMessage();
}

// Funzione per eseguire la ricerca dei libri
function searchBooks($conn, $searchTerm)
{
    $searchTerm = '%' . $searchTerm . '%';

    $sql = "SELECT * FROM books WHERE title LIKE :searchTerm OR author LIKE :searchTerm";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':searchTerm', $searchTerm);
    $stmt->execute();
    error_log("ciao");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}

// Funzione per effettuare il login
function login($conn, $email, $password)
{
    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}

// Funzione per registrare un nuovo utente
function register($conn, $name, $email, $password)
{
    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    echo '<p>Registrazione completata con successo!</p>';
}

// Funzione per effettuare il logout
function logout()
{
    // Cancella tutte le variabili di sessione
    $_SESSION = array();

    // Cancella il cookie di sessione
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Distruggi la sessione
    session_destroy();

    // Reindirizza alla pagina di login
    header("Location: index.php");
    exit;
}

// Verifica se l'utente è loggato
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Recupera i libri dell'utente
function getMyBooks($conn)
{
    $email = $_SESSION['user_id'];

    $sql = "SELECT books.* FROM library JOIN books ON library.book_id = books.book_id WHERE user_id = (SELECT user_id FROM users WHERE email = :email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $myBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $myBooks;
}


// Recupera le letture dell'utente
function getMyReadings($conn)
{
    $userId = $_SESSION['user_id'];

    $sql = "SELECT book_id FROM readings WHERE user_id = (SELECT user_id FROM users WHERE email = :email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $userId);
    $stmt->execute();

    $bookIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Recupera i dettagli dei libri corrispondenti agli ID
    $sqlBooks = "SELECT * FROM books WHERE book_id IN (" . implode(',', $bookIds) . ")";
    $stmtBooks = $conn->prepare($sqlBooks);
    $stmtBooks->execute();

    $myBooks = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

    return $myBooks;
}

function addBookToLibrary($conn, $userId, $bookId)
{
    // Verifica se l'utente è loggato
    if (!isLoggedIn()) {
        echo '<p>Devi essere loggato per aggiungere un libro alla tua libreria.</p>';
        return;
    }

    // Verifica se il libro esiste
    $sql = "SELECT * FROM books WHERE book_id = :bookId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':bookId', $bookId);
    $stmt->execute();

    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo '<p>Il libro non esiste.</p>';
        return;
    }

    // Aggiungi il libro alla libreria dell'utente
    $sql = "INSERT INTO library (user_id, book_id) VALUES (:userId, :bookId)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':bookId', $bookId);
    $stmt->execute();

    echo '<p>Libro aggiunto alla libreria con successo!</p>';
}

function terminaLettura($conn, $userId, $bookId){
    $sql = "UPDATE READINGS SET END_DATE = CURRENT_TIMESTAMP WHERE user_id = :userId AND book_id = :bookId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':bookId', $bookId);
    echo 'prova';
}
<?php
// Avvia la sessione
session_start();

require_once 'functions.php';

// Gestione delle richieste POST

// Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = login($conn, $email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['email'];
    } else {
        echo '<p>Accesso negato. Email o password non validi.</p>';
    }
}

// Registrazione
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    register($conn, $name, $email, $password);
}

// Inserimento di un nuovo libro
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
}

// Logout
if (isset($_POST['logout'])) {
    logout();
}
 if(isset($_POST['stopreading'])){
    terminaLettura($conn, $_SESSION['user_id'], $_POST['book_id']);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>BookTrack - Traccia i tuoi libri e le tue letture</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div></div>
    <?php if (isLoggedIn()) : ?>
    <h2><a href="index.php?action=my-books">I miei libri</a></h2>

    <?php
        if (isset($_GET['action']) && $_GET['action'] === 'my-books') {
            $myBooks = getMyBooks($conn);

            if ($myBooks) {
                echo '<h2>I miei libri</h2>';
                foreach ($myBooks as $book) {
                    echo '<p>' . $book['title'] . ' by ' . $book['author'] . '</p>';
                }
            } else {
                echo '<p>Nessun libro trovato.</p>';
            }
        }

        ?>
    <div>

        <h2><a href="index.php?action=search-books">Ricerca libri</a></h2>

        <?php
            if (isset($_GET['action']) && $_GET['action'] === 'search-books') {
                echo '
                    <form method="get" action="index.php">
                        <label>Cerca per titolo o autore:</label>
                        <input type="text" name="search_term" required>
                        <button type="submit">Cerca</button>
                        <input type="hidden" name="action" value="search-books">
                    </form>
                    ';

                if (isset($_GET['search_term'])) {
                    $searchTerm = $_GET['search_term'];
                    $results = searchBooks($conn, $searchTerm);

                    echo '<h3>Risultati della ricerca:</h3>';

                    if ($results) {
                        foreach ($results as $result) {
                            echo '<p>' . $result['title'] . ' by ' . $result['author'] . '</p>';


                            // echo '<button class="add-reading-button" data-book-id="' . $result['book_id'] . '">Aggiungi a Letture</button>';

                            echo '<form method="post">';
                            echo '<input type="hidden" name="book_id" value="' . $result['book_id'] . '" />';
                            echo '<input type="submit" name="addToReadings" class="button" value="Aggiungi a letture" />';
                            echo '<input type="submit" name="addBookToLibrary" class="button" value="Aggiungi a libreria" />';
                            echo '</form>';
                        }
                    } else {
                        echo '<p>Nessun risultato trovato per la ricerca effettuata.</p>';
                    }
                }
                $readingId = null;

                if (isset($_POST['addToReadings'])) {
                    $bookId = $_POST['book_id'];
                    // Recupera l'ID dell'utente utilizzando l'email
                    $userId = $_SESSION['user_id'];
                    $userIdQuery = "SELECT user_id FROM users WHERE email = :email";
                    $userIdStmt = $conn->prepare($userIdQuery);
                    $userIdStmt->bindParam(':email', $userId);
                    $userIdStmt->execute();

                    $userId = $userIdStmt->fetchColumn();

                    // Inserisci la lettura nella tabella delle letture
                    $sql = "INSERT INTO readings (user_id, book_id, start_date) VALUES (:user_id, :book_id, CURRENT_TIMESTAMP)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':book_id', $bookId);
                    $stmt->execute();

                    $readingId = $conn->lastInsertId();
                    header("Location: index.php?action=my-readings");
                } else if (isset($_POST['addBookToLibrary'])) {
                    $userId = $_SESSION['user_id'];
                    $userIdQuery = "SELECT user_id FROM users WHERE email = :email";
                    $userIdStmt = $conn->prepare($userIdQuery);
                    $userIdStmt->bindParam(':email', $userId);
                    $userIdStmt->execute();

                    $userId = $userIdStmt->fetchColumn();

                    $bookId = $_POST['book_id'];
                    addBookToLibrary($conn, $userId, $bookId);
                }
                return $readingId;
            }
?>
    </div>
    <div>
        <h2><a href="index.php?action=my-readings">Le mie letture</h2>

        <?php
            if (isset($_GET['action']) && $_GET['action'] === 'my-readings') {
                $myReadings = getMyReadings($conn);

            if ($myReadings) {
                foreach ($myReadings as $reading) {
                    echo '<p>' . $reading['title'] . ' by ' . $reading['author'] . ' ';
                    echo '<form method="post" action="index.php">';
                    echo '<input type="hidden" name="book_id" value="' . $reading['book_id'] . '" />';
                    echo '<button type="submit" name="stopreading">Termina Lettura</button>';
                    echo '</form>';
                    echo '</p>';
                }
            } else {
                    echo '<p>Nessuna lettura trovata.</p>';
                }
            
            }

            ?>
    </div>

    <div>
        <form method="post" action="index.php">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

    <?php else : ?>
    <div class="split left" align="left">
        <h2>Login</h2>

        <form method="post" action="index.php">
            <label>Email:</label>
            <input type="email" name="email" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <button type="submit" name="login">Accedi</button>
        </form>

    </div>

    <div class="split right">
        <h2>Registrazione</h2>
        <form method="post" action="index.php">
            <label>Nome:</label>
            <input type="text" name="name" required><br>

            <label>Email:</label>
            <input type="email" name="email" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <button type="submit" name="register">Registrati</button>
        </form>
    </div>
    <?php endif; ?>


</body>

</html>
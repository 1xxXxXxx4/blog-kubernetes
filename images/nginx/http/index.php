<?php
// Bezpieczna konfiguracja - pobieramy dane wstrzyknięte przez Kubernetes/ESO
$host     = getenv('DB_HOST') ?: 'localhost';
$db       = getenv('DB_NAME') ?: 'nazwa_twojej_bazy';
$user     = getenv('DB_USER') ?: 'twoj_uzytkownik';
$password = getenv('DB_PASSWORD') ?: ''; // Hasło zostanie pobrane ze zmiennej środowiskowej
$port     = getenv('DB_PORT') ?: '5432';

// Ciąg połączenia (DSN) dla PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$db;";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Wyrzucaj wyjątki w przypadku błędów
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Zwracaj wyniki jako tablice asocjacyjne
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Wyłącz emulację przygotowanych zapytań
];

try {
    // Tworzenie połączenia z bazą
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Pobieramy posty od najnowszego (sortowanie po ID malejąco)
    $stmt = $pdo->query('SELECT id, title, content, created_at FROM posts ORDER BY id DESC');
    $posts = $stmt->fetchAll();

} catch (PDOException $e) {
    // W razie błędu połączenia, wyświetlamy komunikat i zatrzymujemy stronę
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mój Portal Informacyjny</title>
    <!-- Tailwind CSS dla szybkiego i responsywnego stylowania -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900 flex flex-col min-h-screen">

    <!-- Nagłówek portalu -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide"><a href="index.php">PHP Portal</a></h1>
            <nav>
                <a href="#" class="hover:underline text-sm sm:text-base">Dodaj post</a>
            </nav>
        </div>
    </header>

    <!-- Główna zawartość -->
    <main class="max-w-4xl w-full mx-auto px-4 py-8 flex-grow">
        <h2 class="text-xl font-semibold mb-6 border-b pb-2 text-gray-700">Najnowsze wpisy</h2>

        <div class="space-y-6">
            <?php foreach ($posts as $post): ?>
                <article class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <!-- Tytuł posta -->
                    <h3 class="text-xl font-bold text-blue-600 hover:underline mb-2">
                        <a href="post.php?id=<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h3>
                    
                    <!-- Meta dane (data) -->
                    <div class="text-xs text-gray-500 mb-4">
                        Opublikowano: <?= date('Y-m-d', strtotime($post['created_at'])) ?>
                    </div>

                    <!-- Skrócona treść (ok. 100 znaków) -->
                    <p class="text-gray-600 leading-relaxed mb-4">
                        <?php 
                            $excerpt = $post['content'];
                            if (mb_strlen($excerpt) > 100) {
                                // Skracamy do 100 znaków i dodajemy wielokropek
                                $excerpt = mb_substr($excerpt, 0, 100) . '...';
                            }
                            echo htmlspecialchars($excerpt);
                        ?>
                    </p>

                    <!-- Link do pełnego posta -->
                    <a href="post.php?id=<?= $post['id'] ?>" class="text-sm font-medium text-blue-500 hover:text-blue-700">
                        Czytaj więcej &rarr;
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Stopka -->
    <footer class="bg-gray-800 text-gray-400 text-center py-4 mt-12 text-sm">
        &copy; 2026 PHP Portal. Wszelkie prawa zastrzeżone.
    </footer>

</body>
</html>
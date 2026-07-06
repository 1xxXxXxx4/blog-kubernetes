<?php
// 1. Mniej wrażliwe dane pobieramy ze zmiennych środowiskowych klastra
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'example-db-name';
$port = getenv('DB_PORT') ?: '1111';

// Ścieżki do bezpiecznych plików zamontowanych w RAM przez Kubernetes
$secretDir = '/magic-variables/secrets';
$userFile     = "$secretDir/db-user";
$passwordFile = "$secretDir/db-pass";

// 2. Bezpieczne odczytywanie UŻYTKOWNIKA
if (file_exists($userFile)) {
    $user = trim(file_get_contents($userFile));
} else {
    $user = getenv('DB_USER') ?: 'user';
}

// 3. Bezpieczne odczytywanie HASŁA
if (file_exists($passwordFile)) {
    $password = trim(file_get_contents($passwordFile));
} else {
    $password = getenv('DB_PASSWORD') ?: '';
}

// Połączenie PDO
$dsn = "pgsql:host=$host;port=$port;dbname=$db;";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Pobranie postów
    $stmt = $pdo->query('SELECT id, title, content, created_at FROM posts ORDER BY id DESC');
    $posts = $stmt->fetchAll();

} catch (PDOException $e) {
    // Logujemy pełny błąd wewnętrznie (do stderr kontenera/kustra K8s)
    error_log("Błąd bazy danych: " . $e->getMessage());
    
    // Użytkownikowi na stronie pokazujemy bezpieczny, ogólny komunikat
    die("Portal jest chwilowo niedostępny.");
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
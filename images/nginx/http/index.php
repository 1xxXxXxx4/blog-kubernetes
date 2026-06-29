<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Powitalna PHP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9ecefa);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: #333;
        }
        .welcome-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        p {
            color: #555;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .date-time {
            margin-top: 25px;
            padding: 10px;
            background-color: #f0f3f6;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #7f8c8d;
            font-weight: 500;
        }
        .highlight {
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <h1>Witaj na naszej stronie!</h1>
        <p>Cieszymy się, że tu jesteś. To jest prosta strona powitalna wygenerowana dynamicznie przy użyciu języka <span class="highlight">PHP</span>.</p>
        
        <div class="date-time">
            <?php
                // Ustawienie strefy czasowej dla Polski
                date_default_timezone_set('Europe/Warsaw');
                
                // Tablica z polskimi nazwami dni tygodnia
                $dni = array("Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota");
                $dzien_tygodnia = $dni[date('w')];
                
                // Formatowanie pełnej daty i godziny
                $data_godzina = date('d.m.Y, \o\p\o\d\z\i\n\i\e H:i');
                
                echo "Dzisiaj jest " . $dzien_tygodnia . "<br>";
                echo "Aktualny czas serwera: " . $data_godzina;
            ?>
        </div>
    </div>

</body>
</html>

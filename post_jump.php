<?php
/**
 * post_jump.php
 * Автоматически отправляет POST-запрос на NextForm.php
 * с параметрами Param1=1000 и Param2=2000
 * 
 * Создано: <?php echo date('Y-m-d H:i:s'); ?> (Московское время)
 */

// Проверяем, был ли уже отправлен запрос
if (!isset($_POST['_auto_submitted'])) {
    // Если это первая загрузка, показываем форму для автоматической отправки
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Автоматическая отправка POST-запроса</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background-color: #f5f5f5;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .loading {
                text-align: center;
                color: #666;
                margin: 20px 0;
            }
            .spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3498db;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Автоматическая отправка POST-запроса</h2>
            <div class="loading">
                <div class="spinner"></div>
                <p>Отправка данных на NextForm.php...</p>
                <p><small>Param1=1000, Param2=2000</small></p>
            </div>
            
            <form id="autoForm" method="post" action="NextForm.php" style="display: none;">
                <input type="hidden" name="Param1" value="1000">
                <input type="hidden" name="Param2" value="2000">
                <input type="hidden" name="_auto_submitted" value="1">
                <input type="hidden" name="_from_post_jump" value="1">
            </form>
            
            <script>
                // Автоматически отправляем форму через небольшую задержку
                window.onload = function() {
                    setTimeout(function() {
                        document.getElementById('autoForm').submit();
                    }, 1000); // Задержка 1 секунда для показа анимации
                };
            </script>
        </div>
    </body>
    </html>
    <?php
} else {
    // Если запрос уже был отправлен, перенаправляем на NextForm.php
    header("Location: NextForm.php");
    exit;
}
?> 
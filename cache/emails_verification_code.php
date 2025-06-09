<?php class_exists('app\core\Template') or exit; ?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #1a1a1a; color: #e0e0e0; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #2a2a2a; padding: 20px; border-radius: 8px; border: 2px solid #28a745; }
        h1 { color: #28a745; text-align: center; }
        p { line-height: 1.6; }
        .code { font-size: 24px; color: #28a745; text-align: center; padding: 10px; border: 1px dashed #28a745; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать, <?= $username ?>!</h1>
        <p>Используй вот этот некий код для аутенфикации:</p>
        <div class="code"><?= $code ?></div>
        <p>15 минут только работает торопись!!</p>
        <p class="footer">© 2025 все права пренадлежат сашке эмочкину.</p>
    </div>
</body>
</html>
<html lang="ru">
<head>
    <title>Форма</title>
    <meta charset="UTF-8">
</head>
<body>
<h2>Регистрация</h2>
<form action="/handle" method="post">
    <dl>
        <dt><label for="firstname">Имя</label></dt>
        <dd><input type="text" name="firstname" id="firstname"></dd>

        <dt><label for="lastname">Фамилия</label></dt>
        <dd><input type="text" name="lastname" id="lastname"></dd>

        <dt><label for="age">Возраст</label></dt>
        <dd><input type="text" name="age" id="age"></dd>

        <dt><label for="email">E-Mail</label></dt>
        <dd><input type="text" name="email" id="email"></dd>

        <dt></dt>
        <dd><input type="submit" value="submit"></dd>


    </dl>


</form>
</body>
</html>
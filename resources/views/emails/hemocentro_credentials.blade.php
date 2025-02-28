<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Tuntum</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
        }
        .title {
            color: #DD2500;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
            font-size: 16px;
        }
        .button {
            background-color: #DD2500;
            color: #fff;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 20px auto;
        }
        .info-text {
            font-size: 14px;
            color: #666;
            margin-top: 15px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            margin-top: 40px;
        }
        p{
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" width="50">
    <h1 class="title">Bem-vindo ao Tuntum, {{ $nome }}.</h1>
    <p>Seu hemocentro foi cadastrado no sistema Tuntum. Abaixo estão suas credenciais de acesso:</p>
    <div class="info-box">
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Senha temporária:</strong> {{ $password }}</p>
    </div>
    <p class="info-text">
        Por motivos de segurança, recomendamos que você altere sua senha imediatamente após o primeiro acesso.
        Para isso, basta acessar o sistema e navegar até a seção de configurações de conta.
        Escolha uma senha forte e exclusiva para garantir a segurança das suas informações.
    </p>
    <a href="{{ $link }}" class="button">Acessar o sistema</a>
    <div class="footer">
        <p>Este é um e-mail automático, por favor não responda.</p>
    </div>
</div>
</body>
</html>

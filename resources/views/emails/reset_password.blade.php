<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
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
        }
        .logo {
            display: block;
            margin: 0 auto 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            color: #DD2500;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .button {
            background-color: #DD2500;
            color: #fff;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            margin-top: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" width="50">
    </div>
    <div class="body">
        <h1 class="title">Solicitação de Redefinição de Senha</h1>
        <p>Olá,</p>
        <p>Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.</p>
        <p>Para redefinir sua senha, clique no link abaixo:</p>
        <a href="http://localhost:3000/redefinir-senha?token={{ $token }}&email={{ $email }}" class="button">Redefinir Senha</a>
        <p>Se você não solicitou uma redefinição de senha, ignore este e-mail.</p>
    </div>
    <div class="footer">
        <p>Este é um e-mail automático, por favor não responda.</p>
    </div>
</div>
</body>
</html>

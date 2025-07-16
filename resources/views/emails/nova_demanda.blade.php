<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Demanda de Sangue</title>
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
        .info-text {
            font-size: 14px;
            color: #666;
            margin-top: 15px;
            text-align: left;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            margin-top: 40px;
        }
        p {
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" width="50">
    <h1 class="title">Olá, {{ $doador->user->nome }}!</h1>

    <p>O hemocentro <strong>{{ $demanda->hemocentro->nome }}</strong> está precisando de doações do tipo sanguíneo <strong>{{ $demanda->tipoSanguineo->tipofator }}</strong>.</p>

    <div class="info-box">
        <p><strong>Detalhes da demanda:</strong></p>
        <ul style="padding-left: 20px; margin: 10px 0;">
            <li>Status: {{ $demanda->status }}</li>
            <li>Período: {{ $demanda->data_inicial }} com prazo até {{ $demanda->data_final ?? 'indefinido' }}</li>
        </ul>
    </div>

    <p class="info-text">
        Por favor, considere fazer uma doação durante este período. Sua contribuição pode salvar vidas.
    </p>

    <div class="footer">
        <p>Atenciosamente,<br>Equipe Tuntum</p>
        <p>Este é um e-mail automático, por favor não responda.</p>
    </div>
</div>
</body>
</html>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bem-Vindo à CEU II</title>
</head>
<body>

<h3>Bem-Vindo à CEU II</h3>
<h5>Olá <strong>{{ $userName }}</strong>, você foi cadastrado no sistema CEU II Management</h5>
<h5>Suas credenciais de acesso são:</h5>
<h5><strong>Matrícula</strong>: {{ $registration }}</h5>
<h5><strong>Senha</strong>: {{ $password }}</h5>
<br>
<h6>Altere sua senha no painel de personalização do sistema.</h6>

</body>
</html>
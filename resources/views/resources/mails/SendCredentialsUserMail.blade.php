<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de acceso</title>
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&display=swap" rel="stylesheet"> -->
    <style>
        * {
            font-family: 'Rajdhani', sans-serif;
            box-sizing: border-box !important;
            margin: 0px !important;
            padding: 0px !important;
        }

        .main {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
        }

        .container {
            width: 100% !important;
            height: auto !important;
        }

        .text-left {
            text-align: left !important;
        }

        .row {
            width: 100% !important;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap !important;
        }

        .header-col {
            min-height: 250px !important;
        }

        .col {
            min-width: 300px;
            width: auto !important;
            padding: 20px 25px !important;
        }

        .bg-danger {
            background-color: #dc3545;
        }

        .bg-white {
            background-color: white;
        }

        .p-2 {
            padding: 1rem !important;
        }

        .p-3 {
            padding: 1.5rem !important;
        }

        .rounded-top {
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .rounded-bottom {
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .rounded {
            border-radius: 5px !important;
        }
    </style>
</head>

<body>
<div class="main">
    <div class="container text-center">
        <!-- <div class="row">
            <div class="col bg-danger rounded-top header-col"
                style="display: flex !important; justify-content:center !important;align-items: center !important;">
                <img src="https://img.freepik.com/vector-gratis/ilustracion-concepto-inicio-sesion_114360-739.jpg?w=2000" alt="academia 750"
                    style="max-width: 200px !important;max-height: 200px !important;">
            </div>
        </div> -->
        <div class="row">
            <div class="col bg-white">
                <h1 class="p-3" style="font-size: 30px;">{{ $details['subject'] }}</h1><br>
                <span class="p-3">Hola! <b>{{ $details['nameStudent'] }}</b> </span>
                <p class="p-3">
                    Tus nuevos datos de acceso son:<br><br>
                    Usuario: <b>{{ $details['user'] }}</b><br>
                    Contraseña: <b>{{ $details['password'] }}</b><br>
                </p><br>
                <p class="text-center p-3">
                    <b>¡A por todas!</b><br><br>
                    Academia 750
                </p>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col bg-danger rounded-bottom">
            </div>
        </div> -->
    </div>
</div>
</body>

</html>

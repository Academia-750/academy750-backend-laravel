<!doctype html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge">
    <title>Reporte TopicGroups PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 15px 0;
        }

        th {
            background-color: #edf2f7;
            font-size: 18px;
            font-weight: bold;
            vertical-align: center;
            text-align: center;
        }

        td {
            vertical-align: center;
            text-align: center;
            padding: 5px;
        }

    </style>
</head>
<body>
<div style="overflow-x:auto;">
    <section>
        <div style="width: 90px; margin: auto;">
            <img
                src="{{asset('imgs/logo-wms.png')}}"
                width="90"
                alt="Logo Wms Log House">
        </div>
        <h1 style="text-align: center;">Wms Log House</h1>
        <h3 style="text-align: center; color: darkgreen;">Reporte TopicGroups - {{ \Carbon\Carbon::now() }}</h3>
    </section>
    <table>
        <tr>
            <th>
                <span>ID</span>
            </th>
            <th>
                <span>Fecha de registro</span>
            </th>
        </tr>
        @foreach($topic_groups as $topic_group)
            <tr>
                <td>
                    <span>{{ $topic_group->id }}</span>
                </td>
                <td>
                    <span>{{ $topic_group->created_at }}</span>
                </td>
            </tr>
        @endforeach


    </table>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        table {
            margin-top: 10px;
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
        }

        table.products tr {
            background-color: rgb(96 165 250);
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
        }

        table tr.items {
            background-color: rgb(241 245 249);
        }

        table tr.items td {
            padding: 0.5rem;
          
            text-align: center; 

        }
    </style>
</head>

<body>

    <div class="container">

        <div class="title">Attendance record </div>
        <div class="title">Name: {{ $data['employee']['full_name'] }} </div>
        <div class="title">Date: {{ $data['data']['month'] }} {{ $data['data']['cut_off'] }}</div>



        <div class="margin-top">
            <table class="products">
                <tr>
                    <th>Date</th>
                    <th>Time in</th>
                    <th>Break out</th>

                    <th>Break in </th>
                    <th>Time out</th>
                </tr>
                @foreach ($data['data']['attendance'] as $item)
                    <tr class="items">

                        <td>
                            {{ $item['date'] }}
                        </td>
                        <td>
                            @if ($item['time_in'] !== null)
                                {{ date('H:i', strtotime($item['time_in'])) }}
                            @else
                                --<!-- Provide a default value or message when break_out is null -->
                            @endif
                        </td>
                        <td>
                            @if ($item['break_out'] !== null)
                                {{ date('H:i', strtotime($item['break_out'])) }}
                            @else
                                --<!-- Provide a default value or message when break_out is null -->
                            @endif
                        </td>
                        <td>
                            @if ($item['break_in'] !== null)
                                {{ date('H:i', strtotime($item['break_in'])) }}
                            @else
                                -- <!-- Provide a default value or message when break_out is null -->
                            @endif
                        </td>
                        <td>
                            @if ($item['time_out'] !== null)
                                {{ date('H:i', strtotime($item['time_out'])) }}
                            @else
                                --<!-- Provide a default value or message when break_out is null -->
                            @endif
                        </td>


                    </tr>
                @endforeach
            </table>
        </div>



    </div>

</body>

</html>

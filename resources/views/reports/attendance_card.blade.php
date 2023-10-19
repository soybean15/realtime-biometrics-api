<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
table {
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
}
    </style>
</head>

<body>

    <div class="container">

        <div class="title">Attendance record  </div>

        <div class="title">Date: {{ $data['month'] }} {{ $data['cut_off'] }}</div>

        <div class="margin-top">
            <table class="products">
                <tr>
                    <th>Date</th>
                    <th>Time in</th>
                    <th>Break out</th>

                    <th>Break in </th>
                    <th>Time out</th>
                </tr>
                @foreach ($data['attendance'] as $item)
                <tr class="items">
                  
                        <td>
                            {{ $item['date'] }}
                        </td>
                        <td>
                            {{ $item['time_in'] }}
                        </td>
                        <td>
                            {{ $item['break_out'] }}
                        </td>
                        <td>
                            {{ $item['break_in'] }}
                        </td>
                        <td>
                            {{ $item['time_out'] }}
                        </td>
                      
                   
                </tr>
                @endforeach
            </table>
        </div>



    </div>

</body>

</html>

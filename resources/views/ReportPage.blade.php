<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Date: {{ $date }}</p>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Task title</th>
                <th>Status</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
            <tr>
                <td>{{ $task->task->title }}</td>
                <td>{{ $task->task_status }}</td>
                <td>{{ $task->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

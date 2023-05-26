<!doctype html>
<html>
<head>
    <title>Sample</title>
    <style>
    body { color:gray; }
    h1 { font-size:18pt; font-weight:bold; }
    </style>
</head>
<body>
    <h1>Sample</h1>
    <form method="post" action="helo">
    @csrf
        <p>{{ $_POST['str'] }}</p>
        <input type="submit">
    </form>

</body>
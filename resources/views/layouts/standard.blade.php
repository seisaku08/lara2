<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <title>@yield('title')</title>
    <link href="/css/style.css" rel="stylesheet" type="text/css">
<head>

    <script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

</head>
</head>

<body>
    <h1>@yield('title')</h1>

    @yield('content')

    <footer>
        @yield('footer')
    </footer>
</body>
</html>

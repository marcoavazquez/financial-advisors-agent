<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.jpeg">
    <title>Financial Advisors AI Agent | Login</title>
    @viteReactRefresh
    @vite(['resources/css/app.scss', 'resources/js/main.tsx'])
  </head>
  <body>
    @error('message' )
      <div class="alert alert-danger">
        {{ $message }}
      </div>
    @enderror
    @yield('content')
  </body>
</html>

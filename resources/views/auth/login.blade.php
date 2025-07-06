<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.jpeg">
    <title>Financial Advisors AI Agent | Login</title>
    @viteReactRefresh
    @vite(['resources/css/login.scss'])
  </head>
  <body>
    <a href="{{ route('auth.redirect') }}" class="btn">
      <x-icons.google />      
      Continue with Google
    </a>
  </body>
</html>

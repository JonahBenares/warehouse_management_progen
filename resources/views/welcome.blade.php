<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ config('constants.company.logo') }}">
        <title>{{ config('constants.company.title') }} - WMS</title>

          @vite('resources/css/app.css')
          {{-- @vite('resources/css/figtreeFont.css') --}}
    </head>
    
    <body >
        <div id="app">
        </div>
        @vite('resources/js/app.js')
    </body>
</html>

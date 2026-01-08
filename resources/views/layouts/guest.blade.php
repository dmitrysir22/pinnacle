<!DOCTYPE html>
<html lang="en">
<head>
    {{-- ========================================= --}}
    {{-- Guest Layout: HEAD (meta, title, CSS) --}}
    {{-- ========================================= --}}
    <meta charset="UTF-8">
    <title>@yield('title', 'Agent Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/agent/assets/custom.css?v={{ filemtime(public_path('agent/assets/custom.css')) }}">

    {{-- Backwards-compatible stacks --}}
    @stack('styles')

    {{-- Match Agent layout convention --}}
    @stack('after_styles')
</head>

<body class="bg-light">
    <div class="page">
        {{-- ========================================= --}}
        {{-- Guest Layout: FLASH / VALIDATION MESSAGES --}}
        {{-- ========================================= --}}
        @if ($errors->any())
            <div class="container-xl py-3">
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ========================================= --}}
        {{-- Guest Layout: PAGE CONTENT --}}
        {{-- ========================================= --}}
        @yield('content')
    </div>

    {{-- ========================================= --}}
    {{-- Guest Layout: SCRIPTS --}}
    {{-- ========================================= --}}
    @stack('scripts')
    @stack('after_scripts')
</body>
</html>

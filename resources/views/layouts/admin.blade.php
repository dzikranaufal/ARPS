<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="ARPS">
    <title>@yield('title', 'ARPS Admin')</title>
    @yield('meta')

    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    {{-- Vendor styles --}}
    <link rel="stylesheet" href="{{ asset('vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendors/simplebar.css') }}">
    <link href="{{ asset('vendors/@coreui/icons/css/free.min.css') }}" rel="stylesheet">

    {{-- Main app style --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <script src="{{ asset('js/config.js') }}"></script>
    <script src="{{ asset('js/color-modes.js') }}"></script>

    {{-- Page-specific extra <head> content (e.g. a page-only stylesheet) --}}
    @stack('styles')
</head>

<body>

    @include('partials.sidebar')

    <div class="wrapper d-flex flex-column min-vh-100">

        @include('partials.header')

        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>

        @include('partials.footer')

    </div>

    <script src="{{ asset('vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/js/simplebar.min.js') }}"></script>
    <script>
        const header = document.querySelector("header.header");
        document.addEventListener("scroll", () => {
            if (header) {
                header.classList.toggle("shadow-sm", document.documentElement.scrollTop > 0);
            }
        });

    </script>

    {{-- QuillJS for admin CRUD deskripsi --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('textarea.quill-editor').forEach(function (textarea) {
                var container = document.createElement('div');
                container.style.height = '280px';
                container.style.marginBottom = '40px';
                textarea.style.display = 'none';
                textarea.parentNode.insertBefore(container, textarea);
                // isi awal
                container.innerHTML = textarea.value;
                var quill = new Quill(container, {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'align': [] }],
                                ['link', 'image', 'clean'],
                                ['code-block']
                            ],
                            handlers: {
                                image: function() {
                                    var input = document.createElement('input');
                                    input.setAttribute('type', 'file');
                                    input.setAttribute('accept', 'image/*');
                                    input.click();
                                    input.onchange = function() {
                                        var file = input.files[0];
                                        if (!file) return;
                                        var formData = new FormData();
                                        formData.append('file', file);
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', '{{ route('admin.upload.image') }}');
                                        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                                        xhr.onload = function() {
                                            if (xhr.status >= 200 && xhr.status < 300) {
                                                var json = JSON.parse(xhr.responseText);
                                                if (json.location) {
                                                    var range = quill.getSelection(true);
                                                    quill.insertEmbed(range.index, 'image', json.location);
                                                }
                                            } else {
                                                alert('Upload gagal: ' + xhr.status);
                                            }
                                        };
                                        xhr.onerror = function() { alert('Upload gagal'); };
                                        xhr.send(formData);
                                    };
                                }
                            }
                        }
                    }
                });
                // sync on submit
                var form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        textarea.value = quill.root.innerHTML;
                    });
                }
                // styling for images inside editor
                var style = document.createElement('style');
                style.textContent = '.ql-editor img{max-width:100%;height:auto}';
                document.head.appendChild(style);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>

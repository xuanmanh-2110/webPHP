<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'FlowerShop')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    {{-- Vite managed assets: app.css (with Tailwind) and app.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-rose-800 m-0 p-0">
    @yield('content')
    <script>
        var btn = document.querySelector('.dropdown > .btn');
        var menu = document.querySelector('.dropdown-menu');

        if(btn && menu) {
            function toggleDropdown() {
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                } else {
                    menu.style.display = 'block';
                }
            }

            btn.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleDropdown();
            });

            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });

            menu.addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item')) {
                    menu.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('search-input');
            const suggestions = document.getElementById('search-suggestions');
            const form = document.getElementById('search-form');
            if (input) {
                input.addEventListener('input', function() {
                    const q = input.value.trim();
                    if (q.length < 2) {
                        suggestions.style.display = 'none';
                        suggestions.innerHTML = '';
                        return;
                    }
                    fetch('/products/suggest?q=' + encodeURIComponent(q))
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                suggestions.style.display = 'none';
                                suggestions.innerHTML = '';
                                return;
                            }
                            suggestions.innerHTML = data.map(item =>
                                `<div class="p-2 cursor-pointer hover:bg-rose-500 hover:text-white text-rose-700" onclick="window.location='/shop?search=${encodeURIComponent(item.name)}'">${item.name}</div>`
                            ).join('');
                            suggestions.style.display = 'block';
                        });
                });
                document.addEventListener('click', function(e) {
                    if (!suggestions.contains(e.target) && e.target !== input) {
                        suggestions.style.display = 'none';
                    }
                });
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const q = input.value.trim();
                    if (q.length > 0) {
                        window.location = '/shop?search=' + encodeURIComponent(q);
                    }
                });
            }

            var btnHero = document.getElementById('order-now-btn');
            if (btnHero) {
                btnHero.addEventListener('click', function() {
                    window.location.href = 'http://127.0.0.1:8000/shop';
                });
            }
            var btnPromo = document.getElementById('order-now-btn-promo');
            if (btnPromo) {
                btnPromo.addEventListener('click', function() {
                    window.location.href = 'http://127.0.0.1:8000/shop';
                });
            }

            var slides = document.querySelectorAll('.hero-slide-img');
            var total = slides.length;
            var idx = 0;
            if (total > 1) {
                setInterval(function() {
                    slides[idx].style.transform = 'translateX(100%)';
                    slides[idx].style.opacity = '0';
                    var nextIdx = (idx + 1) % total;
                    slides[nextIdx].style.transform = 'translateX(0)';
                    slides[nextIdx].style.opacity = '1';
                    for (var i = 0; i < total; i++) {
                        if (i !== idx && i !== nextIdx) {
                            slides[i].style.transform = 'translateX(-100%)';
                            slides[i].style.opacity = '0';
                        }
                    }
                    idx = nextIdx;
                }, 5000);
            }
        });
    </script>
</body>
</html>
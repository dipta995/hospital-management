@php
    $userGuard = Auth::guard('admin')->user();
@endphp
@include('backend.layouts.partials.sidebar._styles')

<div class="main-nav">
    <div class="logo-box">
        <a href="{{ route('admin.home') }}" class="logo-light">
            <img style="max-width: 170px;" src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" class="logo-sm" alt="logo sm">
            {{ \App\Models\Setting::get('company_name') }}
        </a>
    </div>

    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:hamburger-menu-broken" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="nav-item sidebar-search-item">
                <div class="px-3 py-2">
                    <div class="input-group">
                        <input id="sidebarSearchInput" type="search" class="form-control form-control-sm"
                               placeholder="Search menu..." aria-label="Search sidebar" autocomplete="off">
                        <button id="sidebarSearchVoiceBtn" class="btn btn-sm btn-outline-secondary" type="button"
                                title="Voice search">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                    <div id="sidebarSearchSuggestions" class="list-group mt-2" style="display:none; max-height:190px; overflow:auto; font-size:.9rem;
                        box-shadow: 0 6px 15px rgba(0,0,0,.08); border-radius:.35rem;
                        background:#fff; z-index:2000;"></div>
                </div>
            </li>

            @include('backend.layouts.partials.sidebar._main')
            @include('backend.layouts.partials.sidebar._patients')
            @include('backend.layouts.partials.sidebar._diagnostic')
            @include('backend.layouts.partials.sidebar._doctor')
            @include('backend.layouts.partials.sidebar._hospital')
            @include('backend.layouts.partials.sidebar._pharmacy')
            @include('backend.layouts.partials.sidebar._finance')
            @include('backend.layouts.partials.sidebar._reports')
            @include('backend.layouts.partials.sidebar._hr')
            @include('backend.layouts.partials.sidebar._inventory')
            @include('backend.layouts.partials.sidebar._admin')
            @include('backend.layouts.partials.sidebar._tools')

        </ul>
    </div>

    <script>
        (function () {
            var searchInput = document.getElementById('sidebarSearchInput');
            var voiceBtn = document.getElementById('sidebarSearchVoiceBtn');
            var navItems = document.querySelectorAll('#navbar-nav > li.nav-item:not(.sidebar-search-item)');
            var sectionTitles = document.querySelectorAll('#navbar-nav > li.sidebar-section-title');

            function normalizeText(text) {
                return text.toString().trim().toLowerCase();
            }

            function updateSectionTitles() {
                sectionTitles.forEach(function (title) {
                    var section = title.getAttribute('data-sidebar-section');
                    if (!section) {
                        return;
                    }
                    var items = document.querySelectorAll('[data-sidebar-section="' + section + '"].nav-item');
                    var anyVisible = false;
                    items.forEach(function (item) {
                        if (item.style.display !== 'none') {
                            anyVisible = true;
                        }
                    });
                    title.classList.toggle('sidebar-hidden', !anyVisible);
                });
            }

            function filterSidebar() {
                var term = normalizeText(searchInput.value);
                var suggestions = [];

                navItems.forEach(function (item) {
                    var mainLink = item.querySelector(':scope > a.nav-link');
                    var title = mainLink ? normalizeText(mainLink.textContent) : '';
                    var href = mainLink ? mainLink.getAttribute('href') : '#';
                    var subLinks = item.querySelectorAll('.sub-nav-link, .sub-nav-section');
                    var match = term === '' || title.indexOf(term) !== -1;

                    if (term && title.indexOf(term) !== -1) {
                        suggestions.push({ text: mainLink.textContent.trim(), href: href, target: item });
                    }

                    subLinks.forEach(function (subLink) {
                        var subText = normalizeText(subLink.textContent);
                        if (!match && subText.indexOf(term) !== -1) {
                            match = true;
                        }
                        if (term && subText.indexOf(term) !== -1 && subLink.classList.contains('sub-nav-link')) {
                            suggestions.push({ text: subLink.textContent.trim(), href: subLink.getAttribute('href'), target: item });
                        }
                    });

                    item.style.display = match ? '' : 'none';

                    var collapseContainer = item.querySelector('.collapse');
                    if (collapseContainer && term && match) {
                        collapseContainer.classList.add('show');
                        if (mainLink) {
                            mainLink.classList.remove('collapsed');
                            mainLink.setAttribute('aria-expanded', 'true');
                        }
                    }
                });

                if (!term) {
                    document.querySelectorAll('#navbar-nav .collapse').forEach(function (el) {
                        var hadShow = el.classList.contains('show');
                        if (!hadShow) {
                            var toggle = document.querySelector('[aria-controls="' + el.id + '"]');
                            if (toggle) {
                                toggle.classList.add('collapsed');
                                toggle.setAttribute('aria-expanded', 'false');
                            }
                        }
                    });
                }

                updateSectionTitles();
                renderSuggestions(suggestions.slice(0, 8));
            }

            function renderSuggestions(items) {
                var suggestionBox = document.getElementById('sidebarSearchSuggestions');
                if (!suggestionBox) {
                    return;
                }

                if (!searchInput.value.trim() || items.length === 0) {
                    suggestionBox.style.display = 'none';
                    suggestionBox.innerHTML = '';
                    return;
                }

                suggestionBox.innerHTML = items.map(function (item) {
                    return '<a href="' + item.href + '" class="list-group-item list-group-item-action py-2">' +
                        item.text + '</a>';
                }).join('');
                suggestionBox.style.display = 'block';
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterSidebar);
                searchInput.addEventListener('keyup', filterSidebar);
                searchInput.addEventListener('search', filterSidebar);
            }

            var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (voiceBtn && SpeechRecognition) {
                var recognition = new SpeechRecognition();
                recognition.lang = navigator.language || 'bn-BD';
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                voiceBtn.addEventListener('click', function () {
                    recognition.start();
                    voiceBtn.classList.add('active');
                });

                recognition.addEventListener('result', function (event) {
                    searchInput.value = event.results[0][0].transcript;
                    filterSidebar();
                });

                recognition.addEventListener('speechend', function () {
                    recognition.stop();
                    voiceBtn.classList.remove('active');
                });

                recognition.addEventListener('error', function () {
                    voiceBtn.classList.remove('active');
                });
            } else if (voiceBtn) {
                voiceBtn.disabled = true;
                voiceBtn.title = 'Voice search not supported in this browser';
                voiceBtn.classList.add('disabled');
            }

            document.addEventListener('click', function (event) {
                var suggestionBox = document.getElementById('sidebarSearchSuggestions');
                if (!suggestionBox || !searchInput) {
                    return;
                }
                if (!event.target.closest('#sidebarSearchInput') && !event.target.closest('#sidebarSearchSuggestions')) {
                    suggestionBox.style.display = 'none';
                }
            });

            // Restore active module collapses on page load
            document.querySelectorAll('#navbar-nav .collapse.show').forEach(function (el) {
                var toggle = document.querySelector('[aria-controls="' + el.id + '"]');
                if (toggle) {
                    toggle.classList.remove('collapsed');
                    toggle.classList.add('active');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        })();
    </script>
</div>

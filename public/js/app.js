(function () {
    'use strict';

    var ICONS = {
        close: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
        left: '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
        right: '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
        pin: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--blue-700)"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
        area: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>',
        bed: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20V10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10M2 16h20M6 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M12 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
        bath: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6 7 8M6 6a2 2 0 1 1 2-2M3 12h18v3a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5zM5 12V6h9v6M7 20l-1 2M17 20l1 2"/></svg>',
        car: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2v-5l-2-5H5L3 12v5h2M5 17h14M7 17v2H5v-2M19 17v2h-2v-2M5 12h14M7 8v4M17 8v4"/></svg>',
        camera: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg>',
        external: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/></svg>',
    };

    var ENTITIES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

    function esc(value) {
        return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function (character) {
            return ENTITIES[character];
        });
    }

    function pick(property, key, fallback) {
        var value = property && property[key];
        return value === null || value === undefined ? fallback : value;
    }

    function safeUrl(value, fallback) {
        var fallbackUrl = fallback || '/no-image.svg';
        var text = String(value || '').trim();
        if (!text) {
            return fallbackUrl;
        }
        try {
            var url = new URL(text, window.location.href);
            if (url.protocol === 'http:' || url.protocol === 'https:') {
                return url.href;
            }
        } catch (error) {
            return fallbackUrl;
        }
        return fallbackUrl;
    }

    function formatBrl(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    }

    function formatPrice(property, price) {
        var formatted = pick(property, 'formatted_price', '');
        if (formatted) {
            return String(formatted);
        }
        return formatBrl(price) + (pick(property, 'purpose', '') === 'aluguel' ? '/mês' : '');
    }

    function formatArea(property) {
        var formatted = pick(property, 'formatted_area', '');
        if (formatted) {
            return String(formatted);
        }
        var area = Number(pick(property, 'area', 0)) || 0;
        return area ? area.toLocaleString('pt-BR') + ' m²' : '—';
    }

    function locationText(property) {
        var formatted = pick(property, 'location_text', '');
        if (formatted) {
            return String(formatted);
        }
        return [pick(property, 'neighborhood', ''), pick(property, 'city', ''), pick(property, 'state', '')]
            .filter(Boolean)
            .join(' · ');
    }

    function getPhotos(property) {
        var photos = pick(property, 'photos', []);
        if (typeof photos === 'string') {
            try {
                photos = JSON.parse(photos);
            } catch (error) {
                photos = [];
            }
        }
        if (!Array.isArray(photos)) {
            photos = [];
        }
        if (!photos.length && pick(property, 'first_photo', '')) {
            photos = [pick(property, 'first_photo')];
        }
        return photos.filter(Boolean);
    }

    function getFeatures(property) {
        var features = pick(property, 'features', []);
        if (typeof features === 'string') {
            features = features.split(/[\n,]/);
        }
        if (!Array.isArray(features)) {
            features = [];
        }
        return features.map(function (feature) {
            return String(feature).trim();
        }).filter(Boolean);
    }

    function numberOrDash(value) {
        var number = Number(value);
        return number > 0 ? String(number) : '—';
    }

    function mapsUrl(property) {
        if (pick(property, 'maps_url', '')) {
            return safeUrl(pick(property, 'maps_url'), '#');
        }
        var latitude = pick(property, 'latitude', '');
        var longitude = pick(property, 'longitude', '');
        var query = latitude && longitude
            ? latitude + ',' + longitude
            : pick(property, 'address', '') + ', ' + pick(property, 'city', '');
        return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(query);
    }

    function whatsappUrl(property, price) {
        if (pick(property, 'whatsapp_url', '')) {
            return safeUrl(pick(property, 'whatsapp_url'), '#');
        }
        var phone = document.body.getAttribute('data-whatsapp') || '';
        var text = 'Olá, Ricardo! Tenho interesse no imóvel: ' + pick(property, 'title', '') +
            ' (' + price + '). Pode me passar mais informações?';
        return 'https://wa.me/' + encodeURIComponent(phone) + '?text=' + encodeURIComponent(text);
    }

    var activeModal = null;

    function closeModal() {
        if (activeModal && activeModal.backdrop && activeModal.backdrop.parentNode) {
            activeModal.backdrop.parentNode.removeChild(activeModal.backdrop);
        }
        document.body.style.overflow = '';
        if (activeModal && activeModal.onKey) {
            document.removeEventListener('keydown', activeModal.onKey);
        }
        activeModal = null;
    }

    function showModalError() {
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        backdrop.setAttribute('role', 'presentation');
        backdrop.innerHTML = '<div class="modal" role="dialog" aria-modal="true" aria-label="Detalhes do imóvel">' +
            '<button type="button" class="modal-close" aria-label="Fechar">' + ICONS.close + '</button>' +
            '<div class="modal-content"><h2>Não foi possível carregar os detalhes</h2>' +
            '<p>Tente novamente em instantes.</p></div></div>';
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
        var dismiss = function () {
            if (backdrop.parentNode) {
                backdrop.parentNode.removeChild(backdrop);
            }
            document.body.style.overflow = '';
            document.removeEventListener('keydown', onKey);
        };
        var onKey = function (event) {
            if (event.key === 'Escape') {
                dismiss();
            }
        };
        backdrop.addEventListener('click', function (event) {
            if (event.target === backdrop || event.target.closest('.modal-close')) {
                dismiss();
            }
        });
        document.addEventListener('keydown', onKey);
    }

    function renderModal(property) {
        var photos = getPhotos(property);
        var displayPhotos = photos.length ? photos : ['/no-image.svg'];
        var title = String(pick(property, 'title', 'Imóvel'));
        var price = formatPrice(property, pick(property, 'price', 0));
        var purposeLabel = pick(property, 'purpose', '') === 'aluguel' ? 'Para alugar' : 'À venda';
        var condoFee = Number(pick(property, 'condo_fee', pick(property, 'condoFee', 0))) || 0;
        var iptu = Number(pick(property, 'iptu', 0)) || 0;
        var features = getFeatures(property);
        var extras = [];
        if (condoFee > 0) {
            extras.push('Condomínio ' + formatBrl(condoFee));
        }
        if (iptu > 0) {
            extras.push('IPTU ' + formatBrl(iptu));
        }

        var thumbs = '';
        if (displayPhotos.length > 1) {
            thumbs = '<div class="modal-thumbs">';
            displayPhotos.forEach(function (photo, index) {
                thumbs += '<button type="button" class="' + (index === 0 ? 'active' : '') +
                    '" aria-label="Ver foto ' + (index + 1) + '">' +
                    '<img src="' + esc(safeUrl(photo)) + '" alt="" loading="lazy"></button>';
            });
            thumbs += '</div>';
        }

        var navigation = '';
        if (displayPhotos.length > 1) {
            navigation = '<button type="button" class="modal-nav prev" aria-label="Foto anterior">' + ICONS.left + '</button>' +
                '<button type="button" class="modal-nav next" aria-label="Próxima foto">' + ICONS.right + '</button>';
        }

        var featureList = '';
        if (features.length) {
            featureList = '<ul class="modal-features" style="margin-top: 22px;">';
            features.forEach(function (feature) {
                featureList += '<li>' + esc(feature) + '</li>';
            });
            featureList += '</ul>';
        }

        var photoCount = photos.length
            ? '<p style="display: flex; align-items: center; gap: 6px; font-size: 13px;">' + ICONS.camera +
                ' ' + photos.length + ' foto' + (photos.length > 1 ? 's' : '') + ' do imóvel</p>'
            : '';

        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        backdrop.setAttribute('role', 'presentation');
        backdrop.innerHTML =
            '<div class="modal" role="dialog" aria-modal="true" aria-label="Detalhes de ' + esc(title) + '">' +
                '<button type="button" class="modal-close" aria-label="Fechar">' + ICONS.close + '</button>' +
                '<div class="modal-hero">' +
                    '<img src="' + esc(safeUrl(displayPhotos[0])) + '" alt="' + esc(title) + ' - foto 1">' +
                    navigation +
                '</div>' +
                thumbs +
                '<div class="modal-content">' +
                    '<span class="badge badge-sale" style="display: inline-block; margin-bottom: 12px;">' +
                        esc(purposeLabel) + ' · ' + esc(pick(property, 'type', '')) +
                    '</span>' +
                    '<h2>' + esc(title) + '</h2>' +
                    '<p style="display: flex; align-items: center; gap: 6px;">' + ICONS.pin + ' ' +
                        esc(pick(property, 'address', '')) + ' — ' + esc(locationText(property)) + '</p>' +
                    '<div class="modal-specs">' +
                        '<div class="modal-spec">' + ICONS.area + '<strong>' + esc(formatArea(property)) + '</strong><small>Área útil</small></div>' +
                        '<div class="modal-spec">' + ICONS.bed + '<strong>' + esc(numberOrDash(pick(property, 'bedrooms', 0))) + '</strong><small>Quartos</small></div>' +
                        '<div class="modal-spec">' + ICONS.bath + '<strong>' + esc(numberOrDash(pick(property, 'bathrooms', 0))) + '</strong><small>Banheiros</small></div>' +
                        '<div class="modal-spec">' + ICONS.car + '<strong>' + esc(numberOrDash(pick(property, 'parking', 0))) + '</strong><small>Vagas</small></div>' +
                    '</div>' +
                    (pick(property, 'description', '') ? '<p>' + esc(pick(property, 'description')) + '</p>' : '') +
                    featureList +
                    photoCount +
                    '<div class="modal-price-row" style="margin-top: 22px;">' +
                        '<div><small>' + esc(pick(property, 'purpose', '') === 'aluguel' ? 'Aluguel' : 'Valor de venda') + '</small>' +
                            '<strong>' + esc(price) + '</strong>' +
                            (extras.length ? '<p style="font-size: 12px; margin-top: 6px;">' + esc(extras.join(' · ')) + '</p>' : '') +
                        '</div>' +
                        '<div style="display: flex; gap: 10px; flex-wrap: wrap;">' +
                            '<a href="' + esc(mapsUrl(property)) + '" target="_blank" rel="noreferrer" class="btn btn-outline-blue">' + ICONS.external + ' Ver no mapa</a>' +
                            '<a href="' + esc(whatsappUrl(property, price)) + '" target="_blank" rel="noreferrer" class="btn btn-blue">Tenho interesse →</a>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';

        var state = {
            backdrop: backdrop,
            modal: backdrop.querySelector('.modal'),
            photos: displayPhotos,
            index: 0,
            onKey: null,
        };

        function updateSlide(index) {
            state.index = (index + state.photos.length) % state.photos.length;
            var image = state.modal.querySelector('.modal-hero img');
            image.src = safeUrl(state.photos[state.index]);
            image.alt = title + ' - foto ' + (state.index + 1);
            var thumbnails = state.modal.querySelectorAll('.modal-thumbs button');
            for (var i = 0; i < thumbnails.length; i += 1) {
                thumbnails[i].classList.toggle('active', i === state.index);
            }
        }

        state.onKey = function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
            if (state.photos.length > 1 && event.key === 'ArrowRight') {
                updateSlide(state.index + 1);
            }
            if (state.photos.length > 1 && event.key === 'ArrowLeft') {
                updateSlide(state.index - 1);
            }
        };

        backdrop.addEventListener('click', function (event) {
            if (event.target === backdrop) {
                closeModal();
            }
        });

        state.modal.addEventListener('click', function (event) {
            if (event.target.closest('.modal-close')) {
                closeModal();
                return;
            }
            if (event.target.closest('.modal-nav.prev')) {
                updateSlide(state.index - 1);
                return;
            }
            if (event.target.closest('.modal-nav.next')) {
                updateSlide(state.index + 1);
                return;
            }
            var thumbnail = event.target.closest('.modal-thumbs button');
            if (thumbnail) {
                var buttons = Array.prototype.slice.call(state.modal.querySelectorAll('.modal-thumbs button'));
                updateSlide(buttons.indexOf(thumbnail));
            }
        });

        document.addEventListener('keydown', state.onKey);
        activeModal = state;
    }

    function openPropertyModal(id) {
        if (activeModal) {
            closeModal();
        }
        fetch('/api/properties/' + encodeURIComponent(id), {
            headers: { Accept: 'application/json' },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Erro ao carregar imóvel.');
                }
                return response.json();
            })
            .then(function (data) {
                var property = data && (data.property || data.data) ? (data.property || data.data) : data;
                if (!property || typeof property !== 'object') {
                    throw new Error('Imóvel não encontrado.');
                }
                renderModal(property);
            })
            .catch(function () {
                showModalError();
            });
    }

    function initHeader() {
        var header = document.querySelector('.site-header');
        var toggle = document.querySelector('.menu-toggle');
        var nav = document.querySelector('.nav-links');

        function onScroll() {
            if (header) {
                header.classList.toggle('scrolled', window.scrollY > 40);
            }
        }
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        if (toggle && nav) {
            toggle.addEventListener('click', function () {
                var open = nav.classList.toggle('open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
            });
            nav.addEventListener('click', function (event) {
                if (event.target.closest('a')) {
                    nav.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.setAttribute('aria-label', 'Abrir menu');
                }
            });
        }
    }

    function initReveal() {
        var elements = document.querySelectorAll('.reveal:not(.visible)');
        if (!('IntersectionObserver' in window)) {
            for (var i = 0; i < elements.length; i += 1) {
                elements[i].classList.add('visible');
            }
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        function observeAll() {
            document.querySelectorAll('.reveal:not(.visible)').forEach(function (element) {
                observer.observe(element);
            });
        }
        observeAll();
        if ('MutationObserver' in window) {
            new MutationObserver(observeAll).observe(document.body, { childList: true, subtree: true });
        }
    }

    function initFilters() {
        var form = document.getElementById('filter-form');
        if (form) {
            form.querySelectorAll('.filter-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    form.submit();
                });
            });
        }

        var params = new URLSearchParams(window.location.search);
        var hasFilters = ['purpose', 'type', 'city', 'sort'].some(function (key) {
            return Boolean(params.get(key));
        });
        if (hasFilters) {
            window.requestAnimationFrame(function () {
                var target = document.getElementById('imoveis');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }
    }

    function initContactForm() {
        var form = document.getElementById('contact-form');
        if (!form) {
            return;
        }
        var status = document.getElementById('form-status');
        var button = document.getElementById('contact-submit');
        var label = button ? button.querySelector('.btn-label') : null;

        function showStatus(type, text) {
            if (!status) {
                return;
            }
            status.className = 'form-status ' + type;
            status.textContent = text;
            status.hidden = false;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (status) {
                status.hidden = true;
                status.textContent = '';
            }
            if (button) {
                button.disabled = true;
            }
            if (label) {
                label.textContent = 'Enviando...';
            }

            var payload = {};
            new FormData(form).forEach(function (value, key) {
                payload[key] = value;
            });

            fetch('/api/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : '',
                },
                body: JSON.stringify(payload),
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (data) {
                        if (!response.ok) {
                            var firstError = '';
                            if (data.errors && typeof data.errors === 'object') {
                                firstError = Object.keys(data.errors).map(function (key) {
                                    var value = data.errors[key];
                                    return Array.isArray(value) ? value[0] : value;
                                })[0];
                            }
                            throw new Error(data.error || data.message || firstError || 'Erro ao enviar mensagem.');
                        }
                        return data;
                    });
                })
                .then(function () {
                    showStatus('ok', 'Mensagem enviada! Responderei em breve. Obrigado pelo contato.');
                    form.reset();
                })
                .catch(function (error) {
                    showStatus('error', error.message || 'Erro ao enviar mensagem.');
                })
                .finally(function () {
                    if (button) {
                        button.disabled = false;
                    }
                    if (label) {
                        label.textContent = 'Enviar mensagem';
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initHeader();
        initReveal();
        initFilters();
        initContactForm();

        document.addEventListener('click', function (event) {
            var button = event.target.closest('.card-link[data-id]');
            if (button) {
                event.preventDefault();
                openPropertyModal(button.getAttribute('data-id'));
            }
        });
    });
})();

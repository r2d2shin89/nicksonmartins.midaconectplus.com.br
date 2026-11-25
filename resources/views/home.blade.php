@extends('layouts.site')

@section('title', config('broker.name', 'Nickson Martins') . ' | Corretor de Imóveis')

@section('content')
@php
    $filters = $filters ?? [];
    $purpose = (string) ($filters['purpose'] ?? '');
    $type = (string) ($filters['type'] ?? '');
    $city = (string) ($filters['city'] ?? '');
    $sort = (string) ($filters['sort'] ?? 'recent');

    // Monta URLs de filtro preservando os demais parâmetros da busca.
    $filterUrl = function (array $overrides = []) use ($purpose, $type, $city, $sort): string {
        $query = array_filter([
            'purpose' => $purpose,
            'type' => $type,
            'city' => $city,
            'sort' => $sort !== 'recent' ? $sort : '',
        ], fn ($value) => $value !== '' && $value !== null);

        foreach ($overrides as $key => $value) {
            if ($value === '' || $value === null) {
                unset($query[$key]);
            } else {
                $query[$key] = $value;
            }
        }

        $url = route('home');

        return $query ? $url . '?' . http_build_query($query) . '#imoveis' : $url . '#imoveis';
    };

    $svg = fn (string $path, int $size = 16, string $extra = '') => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"' . $extra . '>' . $path . '</svg>';
    $icons = [
        'search' => $svg('<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>', 18),
        'pin' => $svg('<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>', 14),
        'award' => $svg('<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/>', 19),
        'badge' => $svg('<path d="M3 12 5 6l7 3 7-3 2 6-2 6-7-3-7 3z"/><path d="m9 12 2 2 4-4"/>', 19),
        'star' => $svg('<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>', 19),
        'check' => $svg('<path d="m5 12 4 4L19 6"/>', 15),
        'camera' => $svg('<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/>', 13),
        'area' => $svg('<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>', 15),
        'bed' => $svg('<path d="M2 20V10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10M2 16h20M6 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M12 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>', 15),
        'bath' => $svg('<path d="M9 6 7 8M6 6a2 2 0 1 1 2-2M3 12h18v3a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5zM5 12V6h9v6M7 20l-1 2M17 20l1 2"/>', 15),
        'search-x' => $svg('<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3M8 8l6 6M14 8l-6 6"/>', 44),
        'shield' => $svg('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>', 26),
        'chart' => $svg('<path d="M3 3v18h18M7 16l4-5 4 3 5-7"/>', 26),
        'heart' => $svg('<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5 1.1-1.1a5.5 5.5 0 0 0 0-7.8Z"/><path d="m8 13 2 2 4-4"/>', 26),
        'phone' => $svg('<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/>', 19),
        'mail' => $svg('<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 6 10 7 10-7"/>', 19),
        'clock' => $svg('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>', 19),
        'send' => $svg('<path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/>', 17),
        'up-right' => $svg('<path d="M7 17 17 7M8 7h9v9"/>', 17),
        'home' => $svg('<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22v-6h6v6"/>', 20),
        'trend' => $svg('<path d="M3 3v18h18M7 16l4-5 4 3 5-7"/>', 20),
        'users' => $svg('<circle cx="9" cy="8" r="4"/><path d="M2 21v-2a6 6 0 0 1 12 0v2M17 11a4 4 0 0 0 0-8M18 21v-2a6 6 0 0 0-3-5.2"/>', 20),
        'key' => $svg('<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9 2 2-2 2 2 2-3 3-2-2-2 2"/>', 20),
        'arrow' => $svg('<path d="M5 12h14M13 6l6 6-6 6"/>', 15),
        'instagram' => $svg('<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>', 22),
        'facebook' => $svg('<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>', 22),
        'linkedin' => $svg('<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2zM4 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/>', 22),
        'youtube' => $svg('<path d="M2 8a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4z"/><path d="m10 9 5 3-5 3z"/>', 22),
    ];
@endphp

<section class="hero" id="topo">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-glow"></div>

    <div class="container hero-content">
        <div class="hero-badge">
            <span class="dot">✦</span>
            Há Mais de 17 anos realizando sonhos em São Paulo
        </div>

        <h1>
            Encontre o lugar
            <em>
                onde sua vida <span class="gradient-text">acontece.</span>
            </em>
        </h1>

        <p class="lead">
            Curadoria de imóveis excepcionais, atendimento humano e resultado
            que vai além da chave. Vamos encontrar o endereço que combina com
            você.
        </p>

        <div class="hero-actions">
            <a href="#imoveis" class="btn btn-gold">Explorar imóveis <span>→</span></a>
            <a href="#contato" class="btn btn-ghost">Agendar uma conversa</a>
        </div>

        <div class="hero-trust">
            <div class="trust-item">
                <span class="trust-icon">{!! $icons['award'] !!}</span>
                <span><strong>17+</strong><small>anos de experiência</small></span>
            </div>
            <div class="trust-item">
                <span class="trust-icon">{!! $icons['badge'] !!}</span>
                <span><strong>480+</strong><small>imóveis negociados</small></span>
            </div>
            <div class="trust-item">
                <span class="trust-icon">{!! $icons['star'] !!}</span>
                <span><strong>4,9/5</strong><small>satisfação dos clientes</small></span>
            </div>
        </div>
    </div>
</section>

<div class="search-wrap">
    <div class="container">
        <form class="search-card" id="hero-search" method="GET" action="{{ route('home') }}">
            <div class="search-field">
                <label for="purpose">Finalidade</label>
                <select id="purpose" name="purpose">
                    <option value="">Venda ou aluguel</option>
                    <option value="venda" {{ $purpose === 'venda' ? 'selected' : '' }}>Comprar</option>
                    <option value="aluguel" {{ $purpose === 'aluguel' ? 'selected' : '' }}>Alugar</option>
                </select>
            </div>
            <div class="search-field">
                <label for="type">Tipo de imóvel</label>
                <select id="type" name="type">
                    <option value="">Todos os tipos</option>
                    <option value="Apartamento" {{ $type === 'Apartamento' ? 'selected' : '' }}>Apartamento</option>
                    <option value="Casa" {{ $type === 'Casa' ? 'selected' : '' }}>Casa</option>
                    <option value="Cobertura" {{ $type === 'Cobertura' ? 'selected' : '' }}>Cobertura</option>
                    <option value="Comercial" {{ $type === 'Comercial' ? 'selected' : '' }}>Comercial</option>
                </select>
            </div>
            <div class="search-field">
                <label for="city">{!! $icons['pin'] !!} Localização</label>
                <input id="city" name="city" placeholder="Cidade, bairro ou região" value="{{ $city }}">
            </div>
            <button type="submit" class="btn btn-blue">{!! $icons['search'] !!} Buscar imóveis</button>
        </form>
    </div>
</div>

<div class="stats-strip">
    <div class="container stats-grid">
        <div class="stat">
            <span class="trust-icon" style="color: var(--blue-700); background: var(--blue-100); border: 0;">{!! $icons['home'] !!}</span>
            <span><span class="stat-num">480+</span><br><span class="stat-label">Imóveis comercializados</span></span>
        </div>
        <div class="stat">
            <span class="trust-icon" style="color: var(--blue-700); background: var(--blue-100); border: 0;">{!! $icons['trend'] !!}</span>
            <span><span class="stat-num">R$ 120 mi</span><br><span class="stat-label">Em negócios fechados</span></span>
        </div>
        <div class="stat">
            <span class="trust-icon" style="color: var(--blue-700); background: var(--blue-100); border: 0;">{!! $icons['users'] !!}</span>
            <span><span class="stat-num">1.200+</span><br><span class="stat-label">Famílias atendidas</span></span>
        </div>
        <div class="stat">
            <span class="trust-icon" style="color: var(--blue-700); background: var(--blue-100); border: 0;">{!! $icons['key'] !!}</span>
            <span><span class="stat-num">21 dias</span><br><span class="stat-label">Tempo médio de venda</span></span>
        </div>
    </div>
</div>

<section class="section about" id="sobre">
    <div class="container about-grid">
        <div class="about-visual reveal">
            <div class="about-frame"></div>
            <img class="about-img-main" src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=1000&q=85" alt="Foto de {{ config('broker.name', 'Nickson Martins') }}, corretor de imóveis">
            <img class="about-img-secondary" src="https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=600&q=85" alt="Atendimento aos clientes">
            <div class="about-seal">
                <strong>CRECI</strong>
                <span>{{ config('broker.creci', '123.456-J') }}</span>
            </div>
        </div>

        <div class="about-text reveal">
            <span class="eyebrow">Sobre mim</span>
            <h2>O imóvel certo não se encontra.<br><span style="color: var(--blue-700);">Ele se constrói.</span></h2>
            <p>
                Sou <strong>{{ config('broker.name', 'Nickson Martins') }}</strong>, corretor de imóveis com mais de 17 anos de atuação no mercado
                imobiliário de São Paulo e litoral. Acredito que cada cliente tem uma história única — e o
                imóvel certo é aquele que dá continuidade a ela.
            </p>
            <p>
                Minha abordagem combina conhecimento técnico, leitura de mercado e, acima de tudo, escuta.
                Da primeira conversa à assinatura do contrato, você tem um parceiro transparente, ágil e
                comprometido com o seu melhor interesse.
            </p>

            <div class="about-points">
                <div class="point"><span>{!! $icons['check'] !!}</span>Atendimento 100% personalizado</div>
                <div class="point"><span>{!! $icons['check'] !!}</span>Análise de mercado e precificação</div>
                <div class="point"><span>{!! $icons['check'] !!}</span>Documentação e financiamento</div>
                <div class="point"><span>{!! $icons['check'] !!}</span>Rede de contatos em todo o Brasil</div>
            </div>

            <div class="contact-list" style="margin-bottom: 24px;">
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['pin'] !!}</span><span><small>Atuação</small><strong>{{ config('broker.city') }} e região</strong></span></div>
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['phone'] !!}</span><span><small>Telefone</small><strong>{{ config('broker.phone') }}</strong></span></div>
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['mail'] !!}</span><span><small>E-mail</small><strong>{{ config('broker.email') }}</strong></span></div>
            </div>

            <div class="signature">{{ config('broker.name', 'Nickson Martins') }}</div>
        </div>
    </div>
</section>

<section class="section properties" id="imoveis">
    <div class="container">
        <div class="props-head">
            <div class="section-top reveal">
                <span class="eyebrow">Portfólio exclusivo</span>
                <h2 class="section-title">Imóveis que contam<br>a sua próxima história</h2>
                <p class="section-desc">Seleção cuidadosa de oportunidades com localização privilegiada e padrão de qualidade incomparável.</p>
            </div>
            <form class="filters reveal" id="filter-form" method="GET" action="{{ route('home') }}">
                <a class="filter-chip {{ $purpose === '' ? 'active' : '' }}" href="{{ $filterUrl(['purpose' => '']) }}">Todos</a>
                <a class="filter-chip {{ $purpose === 'venda' ? 'active' : '' }}" href="{{ $filterUrl(['purpose' => 'venda']) }}">Comprar</a>
                <a class="filter-chip {{ $purpose === 'aluguel' ? 'active' : '' }}" href="{{ $filterUrl(['purpose' => 'aluguel']) }}">Alugar</a>
                <span class="filter-divider"></span>
                <input type="hidden" name="purpose" value="{{ $purpose }}">
                <input type="hidden" name="city" value="{{ $city }}">
                <select class="filter-select" name="type" aria-label="Filtrar por tipo">
                    <option value="">Tipo de imóvel</option>
                    <option value="Apartamento" {{ $type === 'Apartamento' ? 'selected' : '' }}>Apartamento</option>
                    <option value="Casa" {{ $type === 'Casa' ? 'selected' : '' }}>Casa</option>
                    <option value="Cobertura" {{ $type === 'Cobertura' ? 'selected' : '' }}>Cobertura</option>
                    <option value="Comercial" {{ $type === 'Comercial' ? 'selected' : '' }}>Comercial</option>
                </select>
                <select class="filter-select" name="sort" aria-label="Ordenar imóveis">
                    <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>Mais recentes</option>
                    <option value="price-asc" {{ $sort === 'price-asc' ? 'selected' : '' }}>Menor preço</option>
                    <option value="price-desc" {{ $sort === 'price-desc' ? 'selected' : '' }}>Maior preço</option>
                    <option value="area" {{ $sort === 'area' ? 'selected' : '' }}>Maior área</option>
                </select>
                <noscript><button type="submit" class="btn btn-outline-blue">Filtrar</button></noscript>
            </form>
        </div>

        @if ($city !== '')
            <p style="margin-bottom: 22px; color: var(--muted); font-size: 14px;">
                Resultados para <strong style="color: var(--blue-800);">“{{ $city }}”</strong>
                <a href="{{ $filterUrl(['city' => '']) }}" style="border: 0; background: none; color: var(--blue-700); font-weight: 700;">limpar busca</a>
            </p>
        @endif

        <div class="property-grid">
            @foreach ($properties as $property)
                @php
                    $rawPhotos = $property->photos ?? [];
                    $photoList = is_array($rawPhotos)
                        ? $rawPhotos
                        : (is_string($rawPhotos) && $rawPhotos !== '' ? (json_decode($rawPhotos, true) ?: []) : []);
                @endphp
                <article class="property-card reveal">
                    <div class="card-image">
                        <img src="{{ $property->first_photo ?: '/no-image.svg' }}" alt="{{ $property->title }}" loading="lazy">
                        <div class="card-badges">
                            <span class="badge {{ $property->purpose === 'aluguel' ? 'badge-rent' : 'badge-sale' }}">
                                {{ $property->purpose === 'aluguel' ? 'Aluguel' : 'Venda' }}
                            </span>
                            @if ($property->featured)
                                <span class="badge badge-featured">★ Destaque</span>
                            @endif
                        </div>
                        @if (count($photoList) > 1)
                            <span class="card-photo-count">{!! $icons['camera'] !!} {{ count($photoList) }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <span class="card-location">{!! $icons['pin'] !!} {{ $property->location_text }}</span>
                        <h3 class="card-title">{{ $property->title }}</h3>
                        <div class="card-specs">
                            <span>{!! $icons['area'] !!} {{ $property->formatted_area }}</span>
                            @if ((int) ($property->bedrooms ?? 0) > 0)
                                <span>{!! $icons['bed'] !!} {{ $property->bedrooms }} quartos</span>
                            @endif
                            @if ((int) ($property->bathrooms ?? 0) > 0)
                                <span>{!! $icons['bath'] !!} {{ $property->bathrooms }} banh.</span>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="card-price">
                                <small>{{ $property->purpose === 'aluguel' ? 'Aluguel' : 'Valor de venda' }}</small>
                                <strong>{{ $property->formatted_price }}</strong>
                            </div>
                            <button type="button" class="card-link" data-id="{{ $property->id }}" aria-label="Ver detalhes de {{ $property->title }}">
                                <span style="font-size: 22px; line-height: 1;">↗</span>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($properties->isEmpty())
            <div class="empty-state">
                <div style="color: var(--blue-700); margin: 0 auto;">{!! $icons['search-x'] !!}</div>
                <h3>Nenhum imóvel encontrado</h3>
                <p>Tente ajustar os filtros ou entre em contato para receber novas oportunidades.</p>
                <a href="{{ route('home') }}" class="btn btn-outline-blue" style="margin-top: 20px;">Limpar filtros</a>
            </div>
        @endif

        <div class="props-cta">
            <a href="#contato" class="btn btn-outline-blue">Não encontrou o que procura? Fale comigo →</a>
        </div>
    </div>
</section>

<section class="section differentials" id="diferenciais">
    <div class="container">
        <div class="section-top reveal">
            <span class="eyebrow">Por que escolher mim</span>
            <h2 class="section-title">O diferencial está<br>no detalhe</h2>
            <p class="section-desc">Compromisso, conhecimento e tratamento personalizado em cada negociação.</p>
        </div>
        <div class="diff-grid">
            <div class="diff-card reveal">
                <span class="diff-icon">{!! $icons['shield'] !!}</span>
                <h3>Transparência em cada etapa</h3>
                <p>Sem surpresas. Você recebe informações claras sobre valores, documentação e prazos desde o primeiro contato.</p>
            </div>
            <div class="diff-card reveal">
                <span class="diff-icon">{!! $icons['chart'] !!}</span>
                <h3>Precificação inteligente</h3>
                <p>Análise comparativa de mercado e dados reais da região para comprar bem ou vender no melhor valor.</p>
            </div>
            <div class="diff-card reveal">
                <span class="diff-icon">{!! $icons['heart'] !!}</span>
                <h3>Relacionamento que dura</h3>
                <p>Mais do que uma transação, construímos uma relação de confiança que continua muito depois da entrega das chaves.</p>
            </div>
        </div>
    </div>
</section>

<section class="section social-section" id="redes">
    <div class="container social-inner">
        <div class="reveal">
            <span class="eyebrow">Conecte-se</span>
            <h2>Acompanhe o mercado<br>de perto comigo</h2>
            <p>Novos imóveis, dicas de investimento e bastidores do mercado imobiliário todos os dias. Me siga e faça parte da minha rede.</p>
            <a href="https://wa.me/{{ config('broker.whatsapp') }}" target="_blank" rel="noreferrer" class="btn btn-gold">Quero receber novidades {!! $icons['up-right'] !!}</a>
        </div>
        <div class="social-grid reveal">
            <a class="social-card" href="{{ config('broker.social.instagram') }}" target="_blank" rel="noreferrer">
                <span class="social-card-icon">{!! $icons['instagram'] !!}</span>
                <span><strong>Instagram</strong><small>@ricardomendes.imoveis</small></span>
            </a>
            <a class="social-card" href="{{ config('broker.social.facebook') }}" target="_blank" rel="noreferrer">
                <span class="social-card-icon">{!! $icons['facebook'] !!}</span>
                <span><strong>Facebook</strong><small>/ricardomendesimoveis</small></span>
            </a>
            <a class="social-card" href="{{ config('broker.social.linkedin') }}" target="_blank" rel="noreferrer">
                <span class="social-card-icon">{!! $icons['linkedin'] !!}</span>
                <span><strong>LinkedIn</strong><small>/in/ricardomendes</small></span>
            </a>
            <a class="social-card" href="{{ config('broker.social.youtube') }}" target="_blank" rel="noreferrer">
                <span class="social-card-icon">{!! $icons['youtube'] !!}</span>
                <span><strong>YouTube</strong><small>Ricardo Mendes Imóveis</small></span>
            </a>
        </div>
    </div>
</section>

<section class="section contact" id="contato">
    <div class="container contact-grid">
        <div class="contact-info reveal">
            <span class="eyebrow">Vamos conversar</span>
            <h2>Seu próximo endereço<br>começa com uma conversa</h2>
            <p>Conte o que você procura. Retorno em até 24 horas úteis com uma seleção personalizada de imóveis.</p>
            <div class="contact-list">
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['phone'] !!}</span><span><small>Telefone / WhatsApp</small><strong>{{ config('broker.phone') }}</strong></span></div>
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['mail'] !!}</span><span><small>E-mail</small><strong>{{ config('broker.email') }}</strong></span></div>
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['pin'] !!}</span><span><small>Região de atuação</small><strong>{{ config('broker.city') }}</strong></span></div>
                <div class="contact-item"><span class="contact-item-icon">{!! $icons['clock'] !!}</span><span><small>Horário de atendimento</small><strong>Seg a sáb, das 9h às 19h</strong></span></div>
            </div>
        </div>

        <div class="contact-form-card reveal">
            <h3>Envie sua mensagem</h3>
            <p>Preencha os campos abaixo e entrarei em contato.</p>
            <div id="form-status" class="form-status" hidden></div>
            <form id="contact-form">
                <div class="form-grid">
                    <div class="form-group"><label for="name">Nome completo</label><input id="name" name="name" required placeholder="Seu nome"></div>
                    <div class="form-group"><label for="email">E-mail</label><input id="email" name="email" type="email" required placeholder="voce@email.com"></div>
                    <div class="form-group"><label for="phone">Telefone / WhatsApp</label><input id="phone" name="phone" placeholder="(11) 99999-9999"></div>
                    <div class="form-group">
                        <label for="subject">Assunto</label>
                        <select id="subject" name="subject">
                            <option>Quero comprar um imóvel</option>
                            <option>Quero alugar um imóvel</option>
                            <option>Quero vender meu imóvel</option>
                            <option>Anúncio de imóvel</option>
                            <option>Outro assunto</option>
                        </select>
                    </div>
                    <div class="form-group full"><label for="message">Mensagem</label><textarea id="message" name="message" required placeholder="Conte um pouco sobre o que você procura..."></textarea></div>
                </div>
                <button type="submit" id="contact-submit" class="btn btn-blue" style="width: 100%; margin-top: 20px;">
                    {!! $icons['send'] !!} <span class="btn-label">Enviar mensagem</span>
                </button>
                <p class="form-note">Seus dados são usados apenas para entrarmos em contato.</p>
            </form>
        </div>
    </div>
</section>
@endsection

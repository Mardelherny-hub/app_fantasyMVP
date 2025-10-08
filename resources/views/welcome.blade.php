<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCan Soccer Fantasy - Aprende Jugando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }

        @keyframes gentle-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes soft-pulse {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.35; }
        }

        .soft-glow {
            animation: soft-pulse 6s ease-in-out infinite;
        }

        .gentle-float {
            animation: gentle-float 4s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-slate-900 text-white overflow-x-hidden antialiased" x-data="{
    mobileMenuOpen: false,
    currentLang: 'ES',
    scrolled: false,
    activeTab: 'league',
    translations: {
        ES: {
            nav_home: 'Inicio',
            nav_modes: 'Modos de Juego',
            nav_how: 'Cómo Funciona',
            nav_standings: 'Clasificaciones',
            nav_educational: 'Hub Educativo',
            sign_in: 'Iniciar Sesión',
            get_started: 'Comenzar Gratis',
            hero_badge: 'TEMPORADA 2024/25 EN VIVO',
            hero_title: 'Crea tu equipo, juega en ligas,',
            hero_subtitle: 'disfruta trivias y aprende con amigos.',
            hero_description: 'Únete a la experiencia de fantasy soccer educativa de Canadá. Basada en la Canadian Premier League.',
            hero_cta: 'Comenzar Gratis',
            hero_demo: 'Ver Demo',
            stat_posts: 'Publicaciones Educativas',
            stat_trivia: 'Preguntas Semanales',
            stat_users: 'Usuarios Aprendiendo',
            modes_title: 'Modos de Juego',
            modes_subtitle: 'Elige cómo quieres jugar',
            classic_mode_title: 'Classic Mode',
            classic_mode_desc: 'Torneo de temporada completa donde todos compiten contra todos. El manager con el puntaje más alto al final gana.',
            league10_title: 'League-10',
            league10_desc: 'Liga cara a cara con 10 equipos. Después de la temporada regular, los 5 mejores clasifican a una etapa de playoffs, con un play-in para decidir quién avanza.',
            trivia_title: 'Educational Trivia',
            trivia_desc: 'Desafíos de trivia semanales. Responde preguntas, gana puntos y escala en las clasificaciones cada semana.',
            how_it_works_title: 'Cómo Funciona',
            how_it_works_subtitle: 'Comienza en 3 pasos simples',
            step1_title: 'Crear Cuenta',
            step1_desc: 'Regístrate gratis y forma parte de EduCan — una experiencia de fantasy educativa canadiense.',
            step2_title: 'Construye Tu Equipo',
            step2_desc: 'Selecciona más de 10 jugadores, gestiona tu plantilla y explora estrategias mientras aprendes.',
            step3_title: 'Compite y Aprende',
            step3_desc: 'Juega en Classic o League-10, únete a trivias semanales, y explora publicaciones educativas y noticias mientras te diviertes con amigos.',
            standings_title: 'Clasificaciones',
            standings_subtitle: 'Revisa las tablas de posiciones',
            tab_league: 'Posiciones Liga',
            tab_managers: 'Ranking Managers',
            tab_trivia: 'Trivia Standings',
            educational_hub_title: 'Hub Educativo',
            educational_hub_subtitle: 'Aprende mientras juegas',
            post1_title: 'Estrategias de Fantasy Soccer',
            post1_desc: 'Descubre cómo optimizar tu equipo semana a semana y maximizar tus puntos.',
            post2_title: 'Análisis de Estadísticas',
            post2_desc: 'Aprende a interpretar estadísticas de jugadores y tomar decisiones informadas.',
            post3_title: 'Historia de la CPL',
            post3_desc: 'Conoce la historia de la Canadian Premier League y sus equipos.',
            post4_title: 'Trivia Semanal',
            post4_desc: 'Pon a prueba tu conocimiento sobre el fútbol canadiense cada semana.',
            read_more: 'Leer más',
            participate: 'Participar',
            cta_title: '¿Listo para Comenzar?',
            cta_subtitle: 'Únete a miles de jugadores que están aprendiendo y compitiendo en EduCan Soccer Fantasy.',
            cta_button_main: 'Crear Cuenta Gratis',
            cta_button_info: 'Más Información',
            footer_tagline: 'La experiencia de fantasy soccer educativa de Canadá.',
            footer_platform: 'Plataforma',
            footer_resources: 'Recursos',
            footer_legal: 'Legal',
            footer_classic: 'Classic Mode',
            footer_league10: 'League-10',
            footer_trivia: 'Trivia',
            footer_standings: 'Clasificaciones',
            footer_hub: 'Hub Educativo',
            footer_guides: 'Guías',
            footer_faq: 'FAQ',
            footer_support: 'Soporte',
            footer_terms: 'Términos',
            footer_privacy: 'Privacidad',
            footer_cookies: 'Cookies',
            footer_contact: 'Contacto',
            footer_copyright: '© 2024 EduCan Soccer Fantasy. Todos los derechos reservados.',
            // NUEVO DISCLAIMER
            disclaimer_title: 'Descargo de Responsabilidad',
            disclaimer_text: 'EduCan Soccer Fantasy no está afiliado, asociado, autorizado, respaldado por, ni de ninguna manera conectado oficialmente con la Canadian Premier League (CPL) o cualquiera de sus subsidiarias o afiliadas. Los nombres, marcas y activos de la CPL y sus equipos son propiedad de sus respectivos dueños.'
        },
        EN: {
            nav_home: 'Home',
            nav_modes: 'Game Modes',
            nav_how: 'How it Works',
            nav_standings: 'Standings',
            nav_educational: 'Educational Hub',
            sign_in: 'Sign In',
            get_started: 'Start Free',
            hero_badge: 'SEASON 2024/25 IS LIVE',
            hero_title: 'Create your team, play leagues,',
            hero_subtitle: 'enjoy trivia and learn with friends.',
            hero_description: 'Join Canada\'s educational fantasy soccer experience. Based on the Canadian Premier League.',
            hero_cta: 'Start Free',
            hero_demo: 'Watch Demo',
            stat_posts: 'Educational Posts',
            stat_trivia: 'Weekly Questions',
            stat_users: 'Users Learning',
            modes_title: 'Game Modes',
            modes_subtitle: 'Choose how you want to play',
            classic_mode_title: 'Classic Mode',
            classic_mode_desc: 'Full-season tournament where everyone competes against each other. The manager with the highest score at the end wins.',
            league10_title: 'League-10',
            league10_desc: 'Head-to-head league with 10 teams. After the regular season, the top 5 qualify for a playoff stage, with a play-in to decide who advances.',
            trivia_title: 'Educational Trivia',
            trivia_desc: 'Weekly trivia challenges. Answer questions, earn points, and climb the rankings every week.',
            how_it_works_title: 'How It Works',
            how_it_works_subtitle: 'Start in 3 simple steps',
            step1_title: 'Create Account',
            step1_desc: 'Sign up for free and become part of EduCan — an educational Canadian fantasy experience.',
            step2_title: 'Build Your Team',
            step2_desc: 'Select over 10 players, manage your roster, and explore strategies while learning.',
            step3_title: 'Compete and Learn',
            step3_desc: 'Play in Classic or League-10, join weekly trivia, and explore educational posts and news while having fun with friends.',
            standings_title: 'Standings',
            standings_subtitle: 'Check the league tables',
            tab_league: 'League Standings',
            tab_managers: 'Managers Ranking',
            tab_trivia: 'Trivia Standings',
            educational_hub_title: 'Educational Hub',
            educational_hub_subtitle: 'Learn as you play',
            post1_title: 'Fantasy Soccer Strategies',
            post1_desc: 'Discover how to optimize your team week by week and maximize your points.',
            post2_title: 'Statistical Analysis',
            post2_desc: 'Learn to interpret player stats and make informed decisions.',
            post3_title: 'History of the CPL',
            post3_desc: 'Learn the history of the Canadian Premier League and its teams.',
            post4_title: 'Weekly Trivia',
            post4_desc: 'Test your knowledge of Canadian soccer every week.',
            read_more: 'Read more',
            participate: 'Participate',
            cta_title: 'Ready to Get Started?',
            cta_subtitle: 'Join thousands of players who are learning and competing in EduCan Soccer Fantasy.',
            cta_button_main: 'Create Free Account',
            cta_button_info: 'More Information',
            footer_tagline: 'Canada\'s educational fantasy soccer experience.',
            footer_platform: 'Platform',
            footer_resources: 'Resources',
            footer_legal: 'Legal',
            footer_classic: 'Classic Mode',
            footer_league10: 'League-10',
            footer_trivia: 'Trivia',
            footer_standings: 'Standings',
            footer_hub: 'Educational Hub',
            footer_guides: 'Guides',
            footer_faq: 'FAQ',
            footer_support: 'Support',
            footer_terms: 'Terms',
            footer_privacy: 'Privacy',
            footer_cookies: 'Cookies',
            footer_contact: 'Contact',
            footer_copyright: '© 2024 EduCan Soccer Fantasy. All rights reserved.',
            // NUEVO DISCLAIMER
            disclaimer_title: 'Disclaimer',
            disclaimer_text: 'EduCan Soccer Fantasy is not affiliated, associated, authorized, endorsed by, or in any way officially connected with the Canadian Premier League (CPL) or any of its subsidiaries or affiliates. The names, marks, and assets of the CPL and its teams are the property of their respective owners.'
        },
        FR: {
            nav_home: 'Accueil',
            nav_modes: 'Modes de Jeu',
            nav_how: 'Comment Ça Marche',
            nav_standings: 'Classements',
            nav_educational: 'Hub Éducatif',
            sign_in: 'Se Connecter',
            get_started: 'Commencer Gratuitement',
            hero_badge: 'SAISON 2024/25 EN DIRECT',
            hero_title: 'Créez votre équipe, jouez en ligues,',
            hero_subtitle: 'profitez de quiz et apprenez avec des amis.',
            hero_description: 'Rejoignez l\'expérience de fantasy soccer éducative du Canada. Basé sur la Canadian Premier League.',
            hero_cta: 'Commencer Gratuitement',
            hero_demo: 'Voir la Démo',
            stat_posts: 'Publications Éducatives',
            stat_trivia: 'Questions Hebdomadaires',
            stat_users: 'Utilisateurs Apprenant',
            modes_title: 'Modes de Jeu',
            modes_subtitle: 'Choisissez votre façon de jouer',
            classic_mode_title: 'Mode Classique',
            classic_mode_desc: 'Tournoi de saison complète où tout le monde rivalise. Le manager avec le score le plus élevé à la fin gagne.',
            league10_title: 'Ligue-10',
            league10_desc: 'Ligue en face-à-face avec 10 équipes. Après la saison régulière, les 5 meilleurs se qualifient pour une phase de playoffs, avec un play-in pour décider de l\'avancement.',
            trivia_title: 'Quiz Éducatif',
            trivia_desc: 'Défis de quiz hebdomadaires. Répondez aux questions, gagnez des points et grimpez dans les classements chaque semaine.',
            how_it_works_title: 'Comment Ça Marche',
            how_it_works_subtitle: 'Commencez en 3 étapes simples',
            step1_title: 'Créer un Compte',
            step1_desc: 'Inscrivez-vous gratuitement et faites partie d\'EduCan — une expérience de fantasy éducative canadienne.',
            step2_title: 'Construisez Votre Équipe',
            step2_desc: 'Sélectionnez plus de 10 joueurs, gérez votre effectif et explorez des stratégies tout en apprenant.',
            step3_title: 'Participez et Apprenez',
            step3_desc: 'Juega en Classic o League-10, únete a trivias semanales, y explora publicaciones educativas y noticias mientras te diviertes con amigos.',
            standings_title: 'Classements',
            standings_subtitle: 'Consultez les tableaux de classement',
            tab_league: 'Classement de la Ligue',
            tab_managers: 'Classement des Managers',
            tab_trivia: 'Classement des Quiz',
            educational_hub_title: 'Hub Éducatif',
            educational_hub_subtitle: 'Apprenez en jouant',
            post1_title: 'Stratégies de Fantasy Soccer',
            post1_desc: 'Découvrez comment optimiser votre équipe semaine après semaine et maximiser vos points.',
            post2_title: 'Analyse Statistique',
            post2_desc: 'Apprenez à interpréter les statistiques des joueurs et à prendre des décisions éclairées.',
            post3_title: 'Histoire de la CPL',
            post3_desc: 'Apprenez l\'histoire de la Canadian Premier League et de ses équipes.',
            post4_title: 'Quiz Hebdomadaire',
            post4_desc: 'Testez vos connaissances sur le soccer canadien chaque semaine.',
            read_more: 'Lire la suite',
            participate: 'Participer',
            cta_title: 'Prêt à Commencer ?',
            cta_subtitle: 'Rejoignez des milliers de joueurs qui apprennent et rivalisent dans EduCan Soccer Fantasy.',
            cta_button_main: 'Créer un Compte Gratuit',
            cta_button_info: 'Plus d\'Informations',
            footer_tagline: 'L\'expérience de fantasy soccer éducative du Canada.',
            footer_platform: 'Plateforme',
            footer_resources: 'Ressources',
            footer_legal: 'Légal',
            footer_classic: 'Mode Classique',
            footer_league10: 'Ligue-10',
            footer_trivia: 'Quiz',
            footer_standings: 'Classements',
            footer_hub: 'Hub Éducatif',
            footer_guides: 'Guides',
            footer_faq: 'FAQ',
            footer_support: 'Soutien',
            footer_terms: 'Conditions',
            footer_privacy: 'Confidentialité',
            footer_cookies: 'Cookies',
            footer_contact: 'Contact',
            footer_copyright: '© 2024 EduCan Soccer Fantasy. Tous les droits réservés.',
            // NUEVO DISCLAIMER
            disclaimer_title: 'Avis de non-responsabilité',
            disclaimer_text: 'EduCan Soccer Fantasy n\'est pas affilié, associé, autorisé, approuvé par, ou officiellement connecté de quelque manière que ce soit à la Canadian Premier League (CPL) ou à l\'une de ses filiales ou sociétés affiliées. Les noms, marques et actifs de la CPL et de ses équipes sont la propriété de leurs propriétaires respectifs.'
        }
    }
}"
@scroll.window="scrolled = (window.pageYOffset > 50)">

    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="soft-glow absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-3xl"></div>
        <div class="soft-glow absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-teal-500/15 to-transparent rounded-full blur-3xl" style="animation-delay: 2s;"></div>
    </div>

    <header :class="scrolled ? 'bg-slate-900/90 backdrop-blur-xl shadow-lg' : 'bg-transparent'" class="fixed top-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="#home" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg transform group-hover:rotate-6 transition-transform duration-300"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-futbol text-slate-900 text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold tracking-tight">EduCan</div>
                        <div class="text-[10px] text-emerald-400 -mt-0.5 tracking-wider">SOCCER FANTASY</div>
                    </div>
                </a>

                <nav class="hidden lg:flex items-center space-x-6">
                    <a href="#home" class="text-center w-36 text-sm font-medium hover:text-emerald-400 transition" x-text="translations[currentLang].nav_home"></a>
                    <a href="#modes" class="text-center w-36 text-sm font-medium hover:text-emerald-400 transition" x-text="translations[currentLang].nav_modes"></a>
                    <a href="#how-it-works" class="text-center w-36 text-sm font-medium hover:text-emerald-400 transition" x-text="translations[currentLang].nav_how"></a>
                    <a href="#standings" class="text-center w-36 text-sm font-medium hover:text-emerald-400 transition" x-text="translations[currentLang].nav_standings"></a>
                    <a href="#educational" class="text-center w-36 text-sm font-medium hover:text-emerald-400 transition" x-text="translations[currentLang].nav_educational"></a>
                </nav>

                <div class="hidden lg:flex items-center space-x-4">
                    <div class="flex items-center space-x-1 bg-white/5 rounded-full px-1 py-1 border border-white/10">
                        <button @click="currentLang = 'ES'" :class="currentLang === 'ES' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white'" class="px-2.5 py-1 rounded-full text-xs font-bold transition">ES</button>
                        <button @click="currentLang = 'EN'" :class="currentLang === 'EN' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white'" class="px-2.5 py-1 rounded-full text-xs font-bold transition">EN</button>
                        <button @click="currentLang = 'FR'" :class="currentLang === 'FR' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white'" class="px-2.5 py-1 rounded-full text-xs font-bold transition">FR</button>
                    </div>
                     @auth
                        <a
                            href="{{ url('/es/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-center font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].sign_in"></a>
                        <a href="{{ route('register') }}" class="text-center px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold rounded-full hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-105 transition-all duration-300" x-text="translations[currentLang].get_started"></a>
                    @endauth    
                    </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2">
                    <i :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'" class="fas text-xl"></i>
                </button>
            </div>

            <div x-show="mobileMenuOpen" x-cloak x-transition class="lg:hidden mt-6 pb-6 space-y-4 glass-card rounded-2xl p-6">
                <a href="#home" class="block text-base font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].nav_home"></a>
                <a href="#modes" class="block text-base font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].nav_modes"></a>
                <a href="#how-it-works" class="block text-base font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].nav_how"></a>
                <a href="#standings" class="block text-base font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].nav_standings"></a>
                <a href="#educational" class="block text-base font-semibold hover:text-emerald-400 transition" x-text="translations[currentLang].nav_educational"></a>
                <div class="pt-4 border-t border-white/10 flex flex-col space-y-2">
                    <a href="/login" class="py-2.5 text-center border border-emerald-500 rounded-full font-semibold hover:bg-emerald-500/10 transition" x-text="translations[currentLang].sign_in"></a>
                    <a href="/register" class="py-2.5 text-center bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 rounded-full font-bold" x-text="translations[currentLang].get_started"></a>
                </div>
            </div>
        </div>
    </header>

    <section id="home" class="relative pt-28 pb-16 px-4 min-h-screen flex items-center">
        <div class="container mx-auto relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center space-x-2 glass-card px-5 py-2.5 rounded-full mb-8 border border-emerald-500/20">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-emerald-400" x-text="translations[currentLang].hero_badge"></span>
                </div>

                <h1 class="text-5xl md:text-5xl font-black mb-6 leading-tight">
                    <span x-text="translations[currentLang].hero_title"></span><br/>
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent" x-text="translations[currentLang].hero_subtitle"></span>
                </h1>

                <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto mb-10 leading-relaxed" x-text="translations[currentLang].hero_description"></p>

                <div class="grid md:grid-cols-3 gap-4 max-w-4xl mx-auto mb-10">
                    <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-red-500/30 transition group">
                        <div class="w-12 h-12 bg-red-500/90 rounded-xl flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition">
                            <i class="fas fa-trophy text-white text-xl"></i>
                        </div>
                        <h3 class="text-base font-bold mb-1" x-text="translations[currentLang].classic_mode_title">{{ __('Classic Mode') }}</h3>
                        <p class="text-xs text-gray-400" x-text="translations[currentLang].classic_mode_desc.split('.')[0]"></p>
                    </div>

                    <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-teal-500/30 transition group">
                        <div class="w-12 h-12 bg-cyan-500/90 rounded-xl flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-base font-bold mb-1" x-text="translations[currentLang].league10_title">{{ __('League-1000') }}</h3>
                        <p class="text-xs text-gray-400" x-text="translations[currentLang].league10_desc.split('.')[0]"></p>
                    </div>

                    <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-emerald-500/30 transition group">
                        <div class="w-12 h-12 bg-green-600/90 rounded-xl flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <h3 class="text-base font-bold mb-1" x-text="translations[currentLang].trivia_title">Educational Trivia</h3>
                        <p class="text-xs text-gray-400" x-text="translations[currentLang].trivia_desc.split('.')[0]"></p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="/register" class="group px-7 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold text-base rounded-full hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-300 inline-flex items-center justify-center space-x-2">
                        <span x-text="translations[currentLang].hero_cta"></span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="#modes" class="px-7 py-3.5 glass-card border border-white/20 font-bold text-base rounded-full hover:bg-white/5 transition inline-flex items-center justify-center space-x-2">
                        <i class="fas fa-play-circle"></i>
                        <span x-text="translations[currentLang].hero_demo"></span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="glass-card rounded-xl p-5 border border-white/10">
                        <div class="text-3xl font-black text-emerald-400">500+</div>
                        <div class="text-xs text-gray-400 mt-1" x-text="translations[currentLang].stat_posts"></div>
                    </div>
                    <div class="glass-card rounded-xl p-5 border border-white/10">
                        <div class="text-3xl font-black text-teal-400">50+</div>
                        <div class="text-xs text-gray-400 mt-1" x-text="translations[currentLang].stat_trivia"></div>
                    </div>
                    <div class="glass-card rounded-xl p-5 border border-white/10">
                        <div class="text-3xl font-black text-cyan-400">1,000+</div>
                        <div class="text-xs text-gray-400 mt-1" x-text="translations[currentLang].stat_users"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="modes" class="py-16 px-4 relative">
        <div class="container mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-black mb-3" x-text="translations[currentLang].modes_title">Modos de Juego</h2>
                <p class="text-lg text-gray-400" x-text="translations[currentLang].modes_subtitle">Elige cómo quieres jugar</p>
            </div>

            <div class="max-w-4xl mx-auto space-y-5">
                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-red-500/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-14 h-14 bg-red-500/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-trophy text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].classic_mode_title">Classic Mode</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].classic_mode_desc">Torneo de temporada completa donde todos compiten contra todos. El manager con el puntaje más alto al final gana.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-cyan-500/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-14 h-14 bg-cyan-500/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].league10_title">League-10</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].league10_desc">Liga cara a cara con 10 equipos. Después de la temporada regular, los 5 mejores clasifican a una etapa de playoffs, con un play-in para decidir quién avanza.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-green-600/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-14 h-14 bg-green-600/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].trivia_title">Educational Trivia</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].trivia_desc">Desafíos de trivia semanales. Responde preguntas, gana puntos y escala en las clasificaciones cada semana.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-16 px-4 relative">
        <div class="container mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-black mb-3" x-text="translations[currentLang].how_it_works_title">Cómo Funciona</h2>
                <p class="text-lg text-gray-400" x-text="translations[currentLang].how_it_works_subtitle">Comienza en 3 pasos simples</p>
            </div>

            <div class="max-w-4xl mx-auto space-y-5">
                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-red-500/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-red-500/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl font-black text-white">01</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].step1_title">Crear Cuenta</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].step1_desc">Regístrate gratis y forma parte de EduCan — una experiencia de fantasy educativa canadiense.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-cyan-500/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-cyan-500/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl font-black text-white">02</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].step2_title">Construye Tu Equipo</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].step2_desc">Selecciona más de 10 jugadores, gestiona tu plantilla y explora estrategias mientras aprendes.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-green-600/30 transition">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-green-600/90 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl font-black text-white">03</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2" x-text="translations[currentLang].step3_title">Compite y Aprende</h3>
                            <p class="text-gray-300 leading-relaxed" x-text="translations[currentLang].step3_desc">Juega en Classic o League-10, únete a trivias semanales, y explora publicaciones educativas y noticias mientras te diviertes con amigos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="standings" class="py-16 px-4 relative">
        <div class="container mx-auto">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-4xl md:text-5xl font-black mb-3" x-text="translations[currentLang].standings_title">Clasificaciones</h2>
                    <p class="text-lg text-gray-400" x-text="translations[currentLang].standings_subtitle">Revisa las tablas de posiciones</p>
                </div>

                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <button @click="activeTab = 'league'" :class="activeTab === 'league' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900' : 'glass-card text-gray-400 hover:text-white'" class="px-5 py-2.5 rounded-full font-bold text-sm transition" x-text="translations[currentLang].tab_league">
                        Posiciones Liga
                    </button>
                    <button @click="activeTab = 'managers'" :class="activeTab === 'managers' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900' : 'glass-card text-gray-400 hover:text-white'" class="px-5 py-2.5 rounded-full font-bold text-sm transition" x-text="translations[currentLang].tab_managers">
                        Ranking Managers
                    </button>
                    <button @click="activeTab = 'trivia'" :class="activeTab === 'trivia' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900' : 'glass-card text-gray-400 hover:text-white'" class="px-5 py-2.5 rounded-full font-bold text-sm transition" x-text="translations[currentLang].tab_trivia">
                        Trivia Standings
                    </button>
                </div>

                <div x-show="activeTab === 'league'" x-cloak class="glass-card rounded-2xl border border-white/10 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 px-6 py-3 border-b border-white/10">
                        <div class="grid grid-cols-3 text-sm font-bold text-emerald-400">
                            <div x-text="currentLang === 'EN' ? 'Pos' : 'Pos'">Pos</div>
                            <div x-text="currentLang === 'EN' ? 'Team' : 'Equipo'">Equipo</div>
                            <div class="text-right" x-text="currentLang === 'EN' ? 'Pts' : 'Pts'">Pts</div>
                        </div>
                    </div>
                    <div class="divide-y divide-white/5">
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">1</div>
                            <div class="text-gray-300">Maple FC</div>
                            <div class="text-right font-bold text-emerald-400">25</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">2</div>
                            <div class="text-gray-300">Aurora United</div>
                            <div class="text-right font-bold text-emerald-400">22</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">3</div>
                            <div class="text-gray-300">Polar Bears</div>
                            <div class="text-right font-bold text-emerald-400">19</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">4</div>
                            <div class="text-gray-300">North Stars</div>
                            <div class="text-right font-bold text-emerald-400">15</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">5</div>
                            <div class="text-gray-300">Prairie Rovers</div>
                            <div class="text-right font-bold text-emerald-400">12</div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'managers'" x-cloak class="glass-card rounded-2xl border border-white/10 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 px-6 py-3 border-b border-white/10">
                        <div class="grid grid-cols-3 text-sm font-bold text-emerald-400">
                            <div x-text="currentLang === 'EN' ? 'Rank' : 'Rank'">Rank</div>
                            <div x-text="currentLang === 'EN' ? 'Manager' : 'Manager'">Manager</div>
                            <div class="text-right" x-text="currentLang === 'EN' ? 'Pts' : 'Pts'">Pts</div>
                        </div>
                    </div>
                    <div class="divide-y divide-white/5">
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">1</div>
                            <div class="text-gray-300">SoccerMind</div>
                            <div class="text-right font-bold text-emerald-400">1450</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">2</div>
                            <div class="text-gray-300">EduMaster</div>
                            <div class="text-right font-bold text-emerald-400">1420</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">3</div>
                            <div class="text-gray-300">StrategyKing</div>
                            <div class="text-right font-bold text-emerald-400">1385</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">4</div>
                            <div class="text-gray-300">CanPremPro</div>
                            <div class="text-right font-bold text-emerald-400">1360</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">5</div>
                            <div class="text-gray-300">TacticsGuru</div>
                            <div class="text-right font-bold text-emerald-400">1340</div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'trivia'" x-cloak class="glass-card rounded-2xl border border-white/10 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 px-6 py-3 border-b border-white/10">
                        <div class="grid grid-cols-3 text-sm font-bold text-emerald-400">
                            <div x-text="currentLang === 'EN' ? 'Rank' : 'Rank'">Rank</div>
                            <div x-text="currentLang === 'EN' ? 'User' : 'Usuario'">Usuario</div>
                            <div class="text-right" x-text="currentLang === 'EN' ? 'Pts' : 'Pts'">Pts</div>
                        </div>
                    </div>
                    <div class="divide-y divide-white/5">
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">1</div>
                            <div class="text-gray-300">BrainiacFC</div>
                            <div class="text-right font-bold text-emerald-400">850</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">2</div>
                            <div class="text-gray-300">QuizMaster</div>
                            <div class="text-right font-bold text-emerald-400">820</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">3</div>
                            <div class="text-gray-300">SmartPlay</div>
                            <div class="text-right font-bold text-emerald-400">790</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">4</div>
                            <div class="text-gray-300">LearnToWin</div>
                            <div class="text-right font-bold text-emerald-400">765</div>
                        </div>
                        <div class="px-6 py-4 hover:bg-white/5 transition grid grid-cols-3 items-center">
                            <div class="font-bold">5</div>
                            <div class="text-gray-300">EduChamp</div>
                            <div class="text-right font-bold text-emerald-400">740</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="educational" class="py-16 px-4 relative">
        <div class="container mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-black mb-3" x-text="translations[currentLang].educational_hub_title">Hub Educativo</h2>
                <p class="text-lg text-gray-400" x-text="translations[currentLang].educational_hub_subtitle">Aprende mientras juegas</p>
            </div>

            <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-6">
                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-emerald-500/30 transition group">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-lightbulb text-emerald-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2" x-text="translations[currentLang].post1_title">Estrategias de Fantasy Soccer</h3>
                    <p class="text-gray-400 text-sm mb-4" x-text="translations[currentLang].post1_desc">Descubre cómo optimizar tu equipo semana a semana y maximizar tus puntos.</p>
                    <a href="#" class="text-emerald-400 text-sm font-semibold hover:text-emerald-300 inline-flex items-center space-x-1">
                        <span x-text="translations[currentLang].read_more">Leer más</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-teal-500/30 transition group">
                    <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-chart-line text-teal-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2" x-text="translations[currentLang].post2_title">Análisis de Estadísticas</h3>
                    <p class="text-gray-400 text-sm mb-4" x-text="translations[currentLang].post2_desc">Aprende a interpretar estadísticas de jugadores y tomar decisiones informadas.</p>
                    <a href="#" class="text-teal-400 text-sm font-semibold hover:text-teal-300 inline-flex items-center space-x-1">
                        <span x-text="translations[currentLang].read_more">Leer más</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-cyan-500/30 transition group">
                    <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-futbol text-cyan-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2" x-text="translations[currentLang].post3_title">Historia de la CPL</h3>
                    <p class="text-gray-400 text-sm mb-4" x-text="translations[currentLang].post3_desc">Conoce la historia de la Canadian Premier League y sus equipos.</p>
                    <a href="#" class="text-cyan-400 text-sm font-semibold hover:text-cyan-300 inline-flex items-center space-x-1">
                        <span x-text="translations[currentLang].read_more">Leer más</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-green-600/30 transition group">
                    <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-brain text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2" x-text="translations[currentLang].post4_title">Trivia Semanal</h3>
                    <p class="text-gray-400 text-sm mb-4" x-text="translations[currentLang].post4_desc">Pon a prueba tu conocimiento sobre el fútbol canadiense cada semana.</p>
                    <a href="#" class="text-green-400 text-sm font-semibold hover:text-green-300 inline-flex items-center space-x-1">
                        <span x-text="translations[currentLang].participate">Participar</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 relative">
        <div class="container mx-auto">
            <div class="max-w-4xl mx-auto glass-card rounded-3xl p-12 border border-white/10 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10"></div>
                <div class="relative z-10">
                    <h2 class="text-4xl md:text-5xl font-black mb-4" x-text="translations[currentLang].cta_title">¿Listo para Comenzar?</h2>
                    <p class="text-lg text-gray-300 mb-8 max-w-2xl mx-auto" x-text="translations[currentLang].cta_subtitle">Únete a miles de jugadores que están aprendiendo y compitiendo en EduCan Soccer Fantasy.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/register" class="group px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold text-lg rounded-full hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-300 inline-flex items-center justify-center space-x-2">
                            <span x-text="translations[currentLang].cta_button_main">Crear Cuenta Gratis</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#modes" class="px-8 py-4 glass-card border border-white/20 font-bold text-lg rounded-full hover:bg-white/5 transition inline-flex items-center justify-center space-x-2">
                            <i class="fas fa-info-circle"></i>
                            <span x-text="translations[currentLang].cta_button_info">Más Información</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-12 px-4 border-t border-white/10">
        <div class="container mx-auto">
            
            <div class="max-w-6xl mx-auto mb-10 pb-6 border-b border-white/10">
                <h4 class="font-bold mb-2 text-sm text-emerald-400" x-text="translations[currentLang].disclaimer_title">Descargo de Responsabilidad</h4>
                <p class="text-xs text-gray-500 leading-relaxed" x-text="translations[currentLang].disclaimer_text">EduCan Soccer Fantasy no está afiliado, asociado, autorizado, respaldado por, ni de ninguna manera conectado oficialmente con la Canadian Premier League (CPL) o cualquiera de sus subsidiarias o afiliadas. Los nombres, marcas y activos de la CPL y sus equipos son propiedad de sus respectivos dueños.</p>
            </div>
            <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-futbol text-slate-900 text-base"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-lg font-bold">EduCan</div>
                            <div class="text-[9px] text-emerald-400 -mt-0.5">SOCCER FANTASY</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400" x-text="translations[currentLang].footer_tagline">La experiencia de fantasy soccer educativa de Canadá.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-3 text-sm" x-text="translations[currentLang].footer_platform">Plataforma</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_classic">Classic Mode</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_league10">League-10</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_trivia">Trivia</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_standings">Clasificaciones</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-3 text-sm" x-text="translations[currentLang].footer_resources">Recursos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_hub">Hub Educativo</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_guides">Guías</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_faq">FAQ</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_support">Soporte</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-3 text-sm" x-text="translations[currentLang].footer_legal">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_terms">Términos</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_privacy">Privacidad</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_cookies">Cookies</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition" x-text="translations[currentLang].footer_contact">Contacto</a></li>
                    </ul>
                </div>
            </div>

            <div class="max-w-6xl mx-auto mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400" x-text="translations[currentLang].footer_copyright">© 2024 EduCan Soccer Fantasy. Todos los derechos reservados.</p>
                <div class="flex items-center space-x-4">
                    <a href="#" class="w-9 h-9 glass-card rounded-full flex items-center justify-center hover:bg-white/10 transition">
                        <i class="fab fa-twitter text-gray-400 hover:text-emerald-400 transition"></i>
                    </a>
                    <a href="#" class="w-9 h-9 glass-card rounded-full flex items-center justify-center hover:bg-white/10 transition">
                        <i class="fab fa-facebook text-gray-400 hover:text-emerald-400 transition"></i>
                    </a>
                    <a href="#" class="w-9 h-9 glass-card rounded-full flex items-center justify-center hover:bg-white/10 transition">
                        <i class="fab fa-instagram text-gray-400 hover:text-emerald-400 transition"></i>
                    </a>
                    <a href="#" class="w-9 h-9 glass-card rounded-full flex items-center justify-center hover:bg-white/10 transition">
                        <i class="fab fa-youtube text-gray-400 hover:text-emerald-400 transition"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

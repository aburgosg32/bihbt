<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Hospital Belen 2026',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => true,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Hospital</b>Belen',

    'logo_img' => 'img/logo_hbt2.jpg',

    'logo_img_class' => 'brand-image img-circle elevation-3',

    'logo_img_xl' => null,

    'logo_img_xl_class' => 'brand-image-xs',

    'logo_img_alt' => 'Hospital Belen',
    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'img/logo_hbt2.jpg',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'img/logo_hbt2.jpg',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => false,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'false',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */
    'menu' => [

        [
            'text' => 'CONSULTA EXTERNA',
            'icon' => 'fas fa-stethoscope',
            'can' => 'ver-consulta',
            'submenu' => [

                [
                    'text' => 'Dashboard',
                    'url'  => 'consulta.dashboard',
                    'icon' => 'fas fa-chart-line',
                ],

                [
                    'text' => 'Atenciones CE',
                    'url'  => 'consulta-externa/pacientes-consultorio',
                    'icon' => 'fas fa-user-injured',





                    'active' => ['consulta-externa/pacientes-consultorio'],
                ],

            ],
        ],

        [
            'text' => 'EMERGENCIA',
            'icon' => 'fas fa-ambulance',
            'can' => 'ver-emergencia',
            'active' => ['emergencia/*'],
            'submenu' => [

                [
                    'text' => 'Dashboard Emergencia',
                    'url'  => 'emergencia/dashboard',
                    'icon' => 'fas fa-chart-pie',
                    'active' => ['emergencia/dashboard'],
                ],

                [
                    'text' => 'Pacientes Activos',
                    'url'  => 'emergencia/pacientes-activos',
                    'icon' => 'fas fa-procedures',
                    'active' => ['emergencia/pacientes-activos'],
                ],

                [
                    'text' => 'Camas por Servicio',
                    'url'  => 'emergencia/camas-servicio',
                    'icon' => 'fas fa-bed',
                    'active' => ['emergencia/camas-servicio'],
                ],

            ],
        ],
        [
            'text' => 'HOSPITALIZACIÓN',
            'icon' => 'fas fa-hospital-user',
            'can' => 'ver-hospitalizacion',
            'submenu' => [

                [
                    'text' => 'Dashboard Hospital.',
                    'url'  => 'hospitalizacion/dashboard',
                    'icon' => 'fas fa-chart-pie',
                    'active' => ['hospitalizacion/dashboard'],
                ],

                [
                    'text' => 'Pacientes Activos',
                    'url'  => 'hospitalizacion/pacientes-activos',
                    'icon' => 'fas fa-bed',
                    'active' => ['hospitalizacion/pacientes-activos'],
                ],
                [
                    'text' => 'Camas por Servicio',
                    'url'  => 'hospitalizacion/camas-servicio',
                    'icon' => 'fas fa-bed',
                    'active' => ['hospitalizacion/camas-servicio'],
                ],

            ],
        ],
        [
            'text' => 'RADIOLOGIA',
            'icon' => 'fas fa-x-ray',
            'can' => 'ver-radiologia',
            'submenu' => [

                [
                    'text' => 'Dashboard',
                    'url'  => 'radiologia/dashboard',
                    'icon' => 'fas fa-chart-pie',
                    'active' => ['radiologia/dashboard'],
                ],

                [
                    'text' => 'Citas',
                    'url'  => 'radiologia/citas',
                    'icon' => 'fas fa-calendar-check',
                    'active' => ['radiologia/citas'],
                ],
                [
                    'text' => 'Rayos X',
                    'url'  => 'radiologia/rayos-x',
                    'icon' => 'fas fa-radiation',
                    'active' => ['radiologia/rayos-x'],
                ],
                [
                    'text' => 'Tomografías',
                    'url'  => 'radiologia/tomografias',
                    'icon' => 'fas fa-procedures',
                    'active' => ['radiologia/tomografias'],
                ],
                [
                    'text' => 'Ecografías',
                    'url'  => 'radiologia/ecografias',
                    'icon' => 'fas fa-wave-square',
                    'active' => ['radiologia/ecografias'],
                ],
                [
                    'text' => 'Reportes',
                    'url'  => 'radiologia/reportes',
                    'icon' => 'fas fa-file-alt',
                    'active' => ['radiologia/reportes'],
                ],

            ],
        ],
        [
            'text' => 'GESTIÓN DE CAMAS',
            'icon' => 'fas fa-bed',
            'can' => 'ver-camas',
            'active' => ['camas/*'],

            'submenu' => [

                [
                    'text' => 'Censo de Camas',
                    'url'  => 'camas/censo',
                    'icon' => 'fas fa-clipboard-list',
                    'active' => ['camas/censo*'],
                ],

                [
                    'text' => 'Censo Actual',
                    'url'  => 'camas/pacientes',
                    'icon' => 'fas fa-list',
                    'active' => ['camas/pacientes*'],
                ],

                [
                    'text' => 'Configuración',
                    'icon' => 'fas fa-cog',
                    'active' => [
                        'camas/departamentos*',
                        'camas/servicios*',
                        'camas/habitaciones*',
                        'camas/camas*',
                    ],

                    'submenu' => [

                        [
                            'text' => 'Departamentos',
                            'url'  => 'camas/departamentos',
                            'icon' => 'fas fa-hospital',
                            'active' => ['camas/departamentos*'],
                        ],

                        [
                            'text' => 'Servicios',
                            'url'  => 'camas/servicios',
                            'icon' => 'fas fa-clinic-medical',
                            'active' => ['camas/servicios*'],
                        ],

                        [
                            'text' => 'Habitaciones',
                            'url'  => 'camas/habitaciones',
                            'icon' => 'fas fa-door-open',
                            'active' => ['camas/habitaciones*'],
                        ],

                        [
                            'text' => 'Camas',
                            'url'  => 'camas/camas',
                            'icon' => 'fas fa-bed',
                            'active' => ['camas/camas*'],
                        ],

                    ],
                ],

            ],
        ],
        [
            'text' => 'PROCEDIMIENTOS',
            'icon' => 'fas fa-heartbeat',
            'can' => 'ver-procedimientos',
            'active' => ['procedimientos/*'],

            'submenu' => [
                [
                    'text' => 'Programación',
                    'url'  => 'procedimientos/programaciones',
                    'icon' => 'fas fa-calendar-alt',
                    'active' => [
                        'procedimientos/programaciones*',
                    ],
                ],
                [
                    'text' => 'Citas',
                    'route' => 'procedimientos.citas.index',
                    'icon' => 'fas fa-calendar-check',
                    'active' => [
                        'procedimientos/citas*',
                    ],
                ],
                [
                    'text' => 'Reportes',
                    'route' => 'procedimientos.reportes.index',
                    'icon' => 'fas fa-file-alt',
                    'active' => [
                        'procedimientos/reportes*',
                    ],
                ],
                [
                    'text' => 'Configuración',
                    'icon' => 'fas fa-cog',
                    'active' => [
                        'procedimientos/servicios*',
                    ],

                    'submenu' => [

                        [
                            'text' => 'Servicios',
                            'url'  => 'procedimientos/servicios',
                            'icon' => 'fas fa-clinic-medical',
                            'active' => [
                                'procedimientos/servicios*',
                            ],
                        ],
                        [
                            'text' => 'Procedimientos',
                            'url'  => 'procedimientos/tipos',
                            'icon' => 'fas fa-heartbeat',
                            'active' => [
                                'procedimientos/tipos*',
                            ],
                        ],
                        [
                            'text' => 'Médicos',
                            'url'  => 'procedimientos/medicos',
                            'icon' => 'fas fa-user-md',
                            'active' => [
                                'procedimientos/medicos*',
                            ],
                        ],

                    ],
                ],
            ],
        ],
        [
            'text' => 'SALA DE OPERACIONES',
            'icon' => 'fas fa-procedures',
            'can' => 'ver-quirofano',
            'submenu' => [
                [
                    'text' => 'Nueva solicitud',
                    'route' => 'quirofano.solicitudes.create',
                    'icon' => 'fas fa-file-medical',
                    'active' => [
                        'quirofano/solicitudes/nueva',
                    ],
                ],
                [
                    'text' => 'Solicitudes quirúrgicas',
                    'route' => 'quirofano.solicitudes.index',
                    'icon' => 'fas fa-clipboard-list',
                    'active' => [
                        'quirofano/solicitudes?*',
                    ],
                ],
                [
                    'text' => 'Planificación',
                    'route' => 'quirofano.programacion.index',
                    'icon' => 'fas fa-calendar-alt',
                    'active' => [
                        'quirofano/programacion*',
                    ],
                ],
                [
                    'text' => 'Estado de salas',
                    'route' => 'quirofano.estado-salas.index',
                    'icon' => 'fas fa-door-open',
                    'active' => [
                        'quirofano/estado-salas*',
                    ],
                ],
                [
                    'text' => 'Panel TV',
                    'route' => 'quirofano.panel-tv',
                    'icon' => 'fas fa-tv',
                    'target' => '_blank',
                    'active' => [
                        'quirofano/panel-tv',
                    ],
                ],
                [
                    'text' => 'Reportes',
                    'route' => 'quirofano.reportes.index',
                    'icon' => 'fas fa-fw fa-chart-bar',
                ],
                [
                    'text' => 'Configuración',
                    'icon' => 'fas fa-cogs',
                    'submenu' => [
                        [
                            'text' => 'Salas',
                            'route' => 'quirofano.salas.index',
                            'icon' => 'fas fa-door-open',
                            'active' => [
                                'quirofano/configuracion/salas*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'text' => 'SEGUROS',
            'icon' => 'fas fa-file-medical',
            'can' => 'ver-seguros',
            'submenu' => [
                [
                    'text' => 'Disponibilidad',
                    'route' => 'seguros.disponibilidad',
                    'icon' => 'fas fa-calendar-check',
                ],
                [
                    'text' => 'Seguimiento',
                    'route' => 'seguros.seguimiento',
                    'icon' => 'fas fa-history',
                    'active' => ['seguros/seguimiento*'],
                ],
                [
                    'text' => 'Pacientes',
                    'route' => 'seguros.pacientes.index',
                    'icon' => 'fas fa-user-edit',
                    'active' => ['seguros/pacientes*'],
                ],
                [
                    'text' => 'Gestión de cupos',
                    'route' => 'seguros.cupos.index',
                    'icon' => 'fas fa-calendar-plus',
                    'active' => ['seguros/cupos*'],
                ],
                [
                    'text' => 'Impresión FUA',
                    'route' => 'seguros.impresion-fua.index',
                    'icon' => 'fas fa-print',
                ],
                [
                    'text' => 'Reprogramaciones',
                    'route' => 'seguros.reprogramaciones.index',
                    'icon' => 'fas fa-calendar-alt',
                ],
                [
                    'text' => 'Código de prestación',
                    'route' => 'seguros.codigo-prestacion.index',
                    'icon' => 'fas fa-file-medical',
                ],
                [
                    'text' => 'Panel Consultorios',
                    'route' => 'seguros.panel-tv.index',
                    'icon' => 'fas fa-tv',
                    'active' => ['seguros/panel-tv*'],
                ],
                [
                    'text' => 'Asignar consultorios',
                    'route' => 'seguros.consultorios.index',
                    'icon' => 'fas fa-door-open',
                    'active' => ['seguros/asignar-consultorios*'],
                ],
            ],
        ],
        [
            'text' => 'Reportes y Auditoría',
            'icon' => 'fas fa-fw fa-history',
            'submenu' => [
                [
                    'text' => 'Movimientos SIGH',
                    'route' => 'auditoria.index',
                    'icon' => 'fas fa-fw fa-list',
                ],
            ],
        ],
        [
            'text' => 'SEGURIDAD',
            'icon' => 'fas fa-user-shield',
            'can' => 'administrar-seguridad',
            'submenu' => [
                [
                    'text' => 'Usuarios y permisos',
                    'route' => 'seguridad.usuarios.index',
                    'icon' => 'fas fa-users-cog',
                ],
            ],
        ],

    ],
    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];

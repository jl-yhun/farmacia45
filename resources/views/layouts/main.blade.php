<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') - Farmacia El 45</title>
    <link rel="icon" type="image/png" href="/img/fav.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/dt-1.10.20/r-2.2.3/sc-2.0.1/datatables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ mix('css/main.css') }}">
    @yield('css')
</head>

<body class="@yield('body-class')">
    @if (session('flash'))
        <div class="notificacion alert alert-{{ session('flash')['kind'] }} alert-dismissible" role="alert">
            <span class="mensaje">{!! session('flash')['msj'] !!}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg fixed-bottom navbar-light bg-light">
            <a class="navbar-brand text-center" href="{{ route('punto-venta') }}">
                <img src="/img/logo.png" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    @auth
                        @canany(['productos.view'])
                            <li class="nav-item {{ Route::currentRouteName() == 'productos.index' ? 'active' : '' }}">
                                <a href="{{ route('productos.index') }}" class="nav-link">Productos</a>
                            </li>
                        @endcanany
                        {{-- @canany(['garantias.view', 'garantias.devoluciones.creation', 'garantias.creation'])
                            <li class="nav-item {{ Route::currentRouteName() == 'garantias.index' ? 'active' : '' }}">
                                <a href="{{ route('garantias.index') }}" class="nav-link">Garantías / Devoluciones</a>
                            </li>
                        @endcanany --}}
                        @canany(['ordenes-compra.view'])
                            <li class="nav-item dropup">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Órdenes de compra
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a href="{{ route('ordenes-compra.faltantes') }}" class="nav-link">Ver faltantes</a>
                                    <a href="{{ route('ordenes-compra.index') }}" class="nav-link">Ver órdenes compra</a>
                                    {{-- <a href="{{ route('categorias.index') }}" class="nav-link">Categorías</a>
                                    <a href="{{ route('inventario.create') }}" class="nav-link">Re-inventario</a>
                                    <a href="{{ route('aperturas-caja.index') }}" class="nav-link">Aperturas de caja</a> --}}
                                </div>
                            </li>
                        @endcanany
                        @canany(['perdidas.view'])
                            <li class="nav-item {{ Route::currentRouteName() == 'perdidas.index' ? 'active' : '' }}">
                                <a href="{{ route('perdidas.index') }}" class="nav-link">Pérdidas</a>
                            </li>
                        @endcanany
                        <li class="nav-item {{ Route::currentRouteName() == 'herramientas.conteo' ? 'active' : '' }}">
                            <a href="{{ route('herramientas.conteo') }}" size="lg"
                                class="nav-link modal-link">Conteo</a>
                        </li>

                        <li class="nav-item dropup">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Administración
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                @can('usuarios.view')
                                    <a href="{{ route('usuarios.index') }}" class="nav-link">Usuarios</a>
                                @endcan
                                @can('categorias.view')
                                    <a href="{{ route('categorias.index') }}" class="nav-link">Categorías</a>
                                @endcan
                                @can('gastos.view')
                                    <a href="{{ route('gastos.index') }}" class="nav-link">Gastos</a>
                                @endcan
                                @can('apartados.view')
                                    <a href="{{ route('apartados.index') }}" class="nav-link">Apartados</a>
                                @endcan
                                @role('Admin')
                                    <a href="{{ route('inventario.create') }}" class="nav-link">Re-inventario</a>
                                @endrole
                                @can('aperturas-caja.view')
                                    <a href="{{ route('aperturas-caja.index') }}" class="nav-link">Aperturas de caja</a>
                                @endcan
                            </div>
                        </li>
                        @role('Admin')
                            <li class="nav-item dropup">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReportes"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Reportes
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownReportes">
                                    <a href="{{ route('ventas.productos') }}" class="nav-link">Ventas x Producto</a>
                                    <a href="{{ route('ventas.reporte') }}" class="nav-link">Ventas recientes</a>
                                    <a href="{{ route('reportes.estadisticas') }}" class="nav-link">Estadísticas</a>
                                </div>
                            </li>
                        @endrole
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link logout"
                                onclick="event.preventDefault();
                                                                                                                                            document.getElementById('logout-form').submit();">Cerrar
                                sesión</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li> --}}
                        <li class="nav-item">
                            <a data-cy="btn-abrir-cerrar-caja"
                                href="{{ _c('ESTADO_CAJA') == 'cerrada' ? route('punto-venta.opening') : route('punto-venta.close') }}"
                                class="btn btn-lg btn-warning {{ _c('ESTADO_CAJA') == 'cerrada' ? 'modal-link' : 'btnCerrarCaja' }}"
                                size="lg">
                                <i class="fa fa-{{ _c('ESTADO_CAJA') == 'cerrada' ? 'play' : 'stop' }}"></i>
                                {{ _c('ESTADO_CAJA') == 'cerrada' ? 'Abrir caja' : 'HACER CORTE' }}
                            </a>
                        </li>
                        <li class="nav-item dropup user-menu">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ mb_strtoupper(auth()->user()->email) }}
                                <i class="fa fa-user"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a href="#" class="nav-link logout"
                                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    Cerrar sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    @endauth
                    {{-- Para cuando tengamos login --}}
                    {{-- <li class="nav-item dropup">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Dropdown link
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li> --}}
                </ul>
            </div>
        </nav>
        <div class="row">
            <div class="col-12">
                @yield('contenido')
            </div>
        </div>
        <div class="modal fade" id="modal-general" data-keyboard="false" data-backdrop="static" tabindex="-1"
            role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="session" value="{{ auth()->check() ? 'yes' : 'no' }}">
    <input type="hidden" id="urlCambiarEstadoCaja" value="{{ route('punto-venta.open') }}">
    {{-- @include('layouts.resurtimiento-modal') --}}
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/r-2.2.3/sc-2.0.1/datatables.min.js">
    </script>
    <script src="/js/bootbox.all.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/main.js') }}"></script>

    @yield('js')
</body>

</html>

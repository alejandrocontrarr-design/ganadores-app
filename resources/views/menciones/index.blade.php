<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Menciones - Exa FM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        html, body {
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        #sidebar {
            width: 280px;
            position: fixed;
            top: 0;
            left: -280px;
            height: 100vh;
            z-index: 1050;
            background-color: #212529;
            color: #ffffff;
            transition: all 0.3s ease-in-out;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.3);
        }
        #sidebar.active { left: 0; }
        #sidebarOverlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            top: 0;
            left: 0;
        }
        #sidebarOverlay.active { display: block; }
        .calendar-table th {
            background-color: #8b736c !important;
            color: #ffffff !important;
            text-align: center;
        }
        .calendar-day {
            min-height: 150px;
            vertical-align: top;
            position: relative;
            background-color: #ffffff;
            transition: background-color 0.2s;
        }
        .dia-completado {
            background-color: #d1e7dd !important;
            border: 2px solid #198754 !important;
        }
        .day-number {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .bloque-locutor {
            margin-bottom: 6px;
        }
        .header-locutor {
            font-size: 0.72rem;
            padding: 4px 6px;
            border-radius: 4px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            color: #212529;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }
        .header-locutor.fin-de-semana {
            background-color: #fff3cd;
            border-color: #ffeba2;
            color: #856404;
        }
        .locutor-titulo {
            cursor: pointer;
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .locutor-titulo:hover {
            color: #0d6efd;
        }
        .btn-add-mencion {
            border: none !important;
            background-color: #0d6efd !important;
            color: #ffffff !important;
            border-radius: 3px;
            padding: 1px 6px;
            font-size: 0.75rem;
            font-weight: bold;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-add-mencion:hover {
            background-color: #0b5ed7 !important;
        }
        .menciones-contenedor {
            display: none;
            padding-top: 3px;
        }
        .menciones-contenedor.show {
            display: block;
        }
        .mencion-item {
            font-size: 0.76rem;
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 3px;
            margin-left: 2px;
            cursor: pointer;
            background-color: #ffffff;
            border: 1px solid #0d6efd;
            border-left: 4px solid #0d6efd;
            transition: all 0.2s;
        }
        .mencion-item.marcada {
            background-color: #d1e7dd;
            border-color: #198754;
            border-left-color: #198754;
            color: #0f5132;
        }
        .mencion-item:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .badge-count {
            font-size: 0.68rem;
            border-radius: 10px;
            padding: 2px 6px;
            background-color: #fd7e14 !important;
            color: #ffffff !important;
            font-weight: bold;
        }
        .flecha-toggle {
            display: inline-block;
            transition: transform 0.2s ease;
            font-size: 0.65rem;
        }
        .flecha-toggle.rotada {
            transform: rotate(90deg);
        }
    </style>
</head>
<body>

    <div id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div id="sidebar" class="d-flex flex-column p-3">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-2">
            <h5 class="m-0 fw-bold">⚙️ Menú Principal</h5>
            <button class="btn btn-sm btn-outline-light border-0" onclick="toggleSidebar()">✕</button>
        </div>
        <ul class="nav nav-pills flex-column mb-auto gap-2">
            <li class="nav-item">
                <a href="{{ route('ganadores.index') }}" class="btn btn-outline-light w-100 text-start">🏆 Control de Ganadores</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('menciones.index') }}" class="btn btn-secondary w-100 text-start fw-bold" style="background-color: #8b736c; border-color: #8b736c;">🎙️ Menciones</a>
            </li>
        </ul>

        <!-- ZONA DE REPORTES Y UTILIDADES -->
        <div class="border-top border-secondary pt-3 mt-3">
            <div class="small text-warning fw-bold mb-2">Reportes y Utilidades:</div>
            
            <!-- Solo Administrador puede limpiar mes -->
            @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
            <button type="button" class="btn btn-outline-danger btn-sm w-100 text-start mb-2" data-bs-toggle="modal" data-bs-target="#modalLimpiarMes">
                🗑️ Limpiar Menciones por Mes
            </button>
            @endif

            <!-- Invitado y Administrador pueden descargar el Excel -->
            <button type="button" class="btn btn-outline-success btn-sm w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalExportarMes">
                📊 Descargar Excel del Mes
            </button>
        </div>

        <div class="border-top border-secondary pt-3 mt-auto">
            <div class="small text-muted mb-1">Usuario activo:</div>
            <div class="fw-bold text-truncate">
                @auth
                    {{ Auth::user()->name ?? Auth::user()->email ?? 'Administrador' }}
                @else
                    Invitado
                @endauth
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 py-3 bg-white" style="min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark" type="button" onclick="toggleSidebar()">☰</button>
                <h2 class="m-0 fw-bold text-dark">🎙️ Calendario de Menciones</h2>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMencionesMultiples">
                        📅 Cargar Menciones
                    </button>
                @endif

                <form method="GET" action="{{ route('menciones.index') }}" class="d-flex gap-2">
                    @php
                        $meses = [
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                        ];
                    @endphp
                    <select name="mes" class="form-select">
                        @foreach($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ sprintf('%02d', $mes) == $num ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number" 
                           name="ano" 
                           class="form-select" 
                           style="width: 100px;" 
                           value="{{ $ano }}" 
                           min="2000" 
                           max="2099" 
                           placeholder="Año" 
                           required>

                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                </form>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Cerrar Sesion</button>
                    </form>
                @endauth
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered calendar-table w-100">
                <thead>
                    <tr>
                        <th style="width: 14.28%;">Domingo</th>
                        <th style="width: 14.28%;">Lunes</th>
                        <th style="width: 14.28%;">Martes</th>
                        <th style="width: 14.28%;">Miércoles</th>
                        <th style="width: 14.28%;">Jueves</th>
                        <th style="width: 14.28%;">Viernes</th>
                        <th style="width: 14.28%;">Sábado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $diaActual = 1;
                        $celda = 0;
                    @endphp
                    @while($diaActual <= $diasEnMes)
                        <tr>
                            @for($i = 0; $i < 7; $i++)
                                @if($celda < $primerDiaSemana || $diaActual > $diasEnMes)
                                    <td class="bg-light"></td>
                                @else
                                    @php
                                        $fechaFormatted = sprintf('%04d-%02d-%02d', $ano, $mes, $diaActual);
                                        
                                        $mencionesDelDia = $menciones->get($fechaFormatted, collect())->filter(function($m) {
                                            return !empty(trim($m->texto));
                                        });

                                        $locutoresDia = [];
                                        if (in_array($i, [1, 2, 3, 5])) { 
                                            $locutoresDia = ['León en Exa', 'Gsus con G', 'CB Noticias', 'Sin Filtros', 'Aquí Entre Nos'];
                                        } elseif ($i == 4) { 
                                            $locutoresDia = ['León en Exa', 'Gsus con G', 'CB Noticias', 'Sin Filtros', 'Aquí Entre Nos', 'Reconexión'];
                                        } elseif ($i == 6) { 
                                            $locutoresDia = ['El Merequetengue', '33/45'];
                                        } elseif ($i == 0) { 
                                            $locutoresDia = ['33/45'];
                                        }

                                        $diaCompletado = false;
                                        $totalMenciones = $mencionesDelDia->count();
                                        
                                        if ($totalMenciones > 0) {
                                            $mencionesMarcadas = $mencionesDelDia->filter(function($m) {
                                                return !is_null($m->marcado_at);
                                            })->count();

                                            if ($totalMenciones === $mencionesMarcadas) {
                                                $diaCompletado = true;
                                            }
                                        }
                                    @endphp

                                    <td class="calendar-day p-2 {{ $diaCompletado ? 'dia-completado' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="day-number {{ $diaCompletado ? 'text-success' : '' }}">
                                                {{ $diaActual }} @if($diaCompletado) 🟢 @endif
                                            </span>
                                        </div>

                                        @foreach($locutoresDia as $indexLocutor => $locutorNombre)
                                            @php
                                                $mencionesLocutor = $mencionesDelDia->where('locutor', $locutorNombre);
                                                $cantidadMenciones = $mencionesLocutor->count();
                                                $targetId = "menciones-" . $fechaFormatted . "-" . $indexLocutor;
                                                $esFinDeSemana = ($i == 0 || $i == 6);
                                            @endphp
                                            <div class="bloque-locutor">
                                                <div class="header-locutor {{ $esFinDeSemana ? 'fin-de-semana' : '' }}">
                                                    <div class="locutor-titulo" onclick="toggleMenciones('{{ $targetId }}', this)">
                                                        <span class="flecha-toggle">▶</span>
                                                        <span class="text-truncate">{{ $locutorNombre }}</span>
                                                        @if($cantidadMenciones > 0)
                                                            <span class="badge badge-count ms-auto">{{ $cantidadMenciones }}</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- BOTÓN "+" DISPONIBLE PARA TODOS LOS USUARIOS AUTENTICADOS (ADMIN E INVITADO) -->
                                                    @auth
                                                        <button type="button" class="btn-add-mencion ms-1" title="Agregar mención" 
                                                                onclick="abrirModalNueva('{{ $fechaFormatted }}', '{{ addslashes($locutorNombre) }}')">+</button>
                                                    @endauth
                                                </div>

                                                <div id="{{ $targetId }}" class="menciones-contenedor">
                                                    @foreach($mencionesLocutor as $mencion)
                                                        @php
                                                            $marcadoAt = $mencion->marcado_at ? \Carbon\Carbon::parse($mencion->marcado_at)->format('h:i A') : '';
                                                        @endphp
                                                        <div class="mencion-item {{ $mencion->marcado_at ? 'marcada' : '' }}" 
                                                             onclick="abrirModalEditar({{ $mencion->id }}, '{{ $fechaFormatted }}', '{{ addslashes($mencion->locutor) }}', '{{ addslashes($mencion->texto) }}', '{{ $marcadoAt }}')">
                                                            <div class="text-truncate">{{ $mencion->texto }}</div>
                                                            @if($mencion->marcado_at)
                                                                <div class="fw-bold text-success mt-1" style="font-size: 0.68rem;">⏰ {{ $marcadoAt }}</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                    @php $diaActual++; @endphp
                                @endif
                                @php $celda++; @endphp
                            @endfor
                        </tr>
                    @endwhile
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Mención (Individual) -->
    <div class="modal fade" id="modalMencion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('menciones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="accion" id="modalAccionInput" value="guardar">
                    <input type="hidden" name="fecha" id="modalFechaInput">
                    <input type="hidden" name="mencion_id" id="modalMencionIdInput">

                    <div class="modal-header text-white" style="background-color: #8b736c;">
                        <h5 class="modal-title fw-bold">🎙 Mención (<span id="modalFechaTexto"></span>)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Locutor / Programa</label>
                            <input type="text" name="locutor" id="modalLocutorInput" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Texto / Guion de la Mención</label>
                            <textarea name="texto" id="modalTextoInput" class="form-control" rows="4" placeholder="Escribe el texto de la mención aquí..."></textarea>
                        </div>
                        
                        <div class="mb-2 text-start">
                            <a href="javascript:void(0);" id="enlaceHoraManual" class="text-muted text-decoration-none small" style="font-size: 0.78rem;" onclick="mostrarInputHoraManual()">
                                ⏱️ Escribir hora manual
                            </a>
                        </div>
                        
                        <div id="contenedorHoraManual" class="mb-3 d-none">
                            <label class="form-label small fw-bold text-secondary">Escribe la hora y presiona Marcar (Ej. 1:44 PM)</label>
                            <input type="text" name="hora_manual" id="inputHoraManual" class="form-control form-control-sm" placeholder="Ej. 1:44 PM">
                        </div>

                        <div id="infoMarcado" class="alert alert-success d-none mb-0 py-2">
                            ⏰ Marcad@ como realizada a las: <strong id="horaMarcadoTexto"></strong>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div>
                            <button type="button" id="btnMarcar" class="btn btn-success fw-bold" onclick="enviarFormulario('marcar')">✓ Marcar</button>
                            <button type="button" id="btnDesmarcar" class="btn btn-outline-danger btn-sm d-none" onclick="enviarFormulario('desmarcar')">Desmarcar</button>
                            @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
                                <button type="button" id="btnEliminar" class="btn btn-outline-danger btn-sm d-none" onclick="enviarFormulario('eliminar')">Borrar</button>
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" onclick="document.getElementById('modalAccionInput').value='guardar'" class="btn text-white fw-bold" style="background-color: #8b736c;">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Menciones Múltiples -->
    @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
    <div class="modal fade" id="modalMencionesMultiples" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('menciones.storeMultiple') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">📅 Agregar Mención a Varios Días</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Locutor / Programa</label>
                            <select name="locutor" class="form-select" required>
                                <option value="">Selecciona un programa...</option>
                                <option value="León en Exa">León en Exa</option>
                                <option value="Gsus con G">Gsus con G</option>
                                <option value="CB Noticias">CB Noticias</option>
                                <option value="Sin Filtros">Sin Filtros</option>
                                <option value="Aquí Entre Nos">Aquí Entre Nos</option>
                                <option value="Reconexión">Reconexión</option>
                                <option value="El Merequetengue">El Merequetengue</option>
                                <option value="33/45">33/45</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Texto / Guion de la Mención</label>
                            <textarea name="texto" class="form-control" rows="3" placeholder="Escribe el texto que se duplicará en las fechas seleccionadas..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Selecciona los Días</label>
                            <input type="text" name="fechas" id="fechas_multiples_picker" class="form-control" placeholder="Haz clic para abrir el calendario..." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold">Guardar Menciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL: Limpiar Menciones por Mes -->
    @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
    <div class="modal fade" id="modalLimpiarMes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('menciones.limpiarMes') }}" method="POST" onsubmit="return confirm('¿Estás completamente seguro de borrar TODAS las menciones del mes y año seleccionados?');">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">🗑️ Limpiar Menciones por Mes</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mes</label>
                            <select name="mes" class="form-select" required>
                                <option value="">Selecciona un mes...</option>
                                <option value="01" {{ $mes == '01' ? 'selected' : '' }}>Enero</option>
                                <option value="02" {{ $mes == '02' ? 'selected' : '' }}>Febrero</option>
                                <option value="03" {{ $mes == '03' ? 'selected' : '' }}>Marzo</option>
                                <option value="04" {{ $mes == '04' ? 'selected' : '' }}>Abril</option>
                                <option value="05" {{ $mes == '05' ? 'selected' : '' }}>Mayo</option>
                                <option value="06" {{ $mes == '06' ? 'selected' : '' }}>Junio</option>
                                <option value="07" {{ $mes == '07' ? 'selected' : '' }}>Julio</option>
                                <option value="08" {{ $mes == '08' ? 'selected' : '' }}>Agosto</option>
                                <option value="09" {{ $mes == '09' ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ $mes == '10' ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ $mes == '11' ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ $mes == '12' ? 'selected' : '' }}>Diciembre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Año</label>
                            <input type="number" name="ano" class="form-control" value="{{ $ano ?? 2026 }}" min="2000" max="2099" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-bold">Eliminar Menciones del Mes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL: Descargar Excel por Mes -->
    <div class="modal fade" id="modalExportarMes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('menciones.exportarMes') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">📊 Descargar Menciones en Excel</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mes</label>
                            <select name="mes" class="form-select" required>
                                <option value="">Selecciona un mes...</option>
                                <option value="01" {{ $mes == '01' ? 'selected' : '' }}>Enero</option>
                                <option value="02" {{ $mes == '02' ? 'selected' : '' }}>Febrero</option>
                                <option value="03" {{ $mes == '03' ? 'selected' : '' }}>Marzo</option>
                                <option value="04" {{ $mes == '04' ? 'selected' : '' }}>Abril</option>
                                <option value="05" {{ $mes == '05' ? 'selected' : '' }}>Mayo</option>
                                <option value="06" {{ $mes == '06' ? 'selected' : '' }}>Junio</option>
                                <option value="07" {{ $mes == '07' ? 'selected' : '' }}>Julio</option>
                                <option value="08" {{ $mes == '08' ? 'selected' : '' }}>Agosto</option>
                                <option value="09" {{ $mes == '09' ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ $mes == '10' ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ $mes == '11' ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ $mes == '12' ? 'selected' : '' }}>Diciembre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Año</label>
                            <input type="number" name="ano" class="form-control" value="{{ $ano ?? 2026 }}" min="2000" max="2099" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold">Generar y Descargar Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fechasPicker = document.getElementById('fechas_multiples_picker');
            if (fechasPicker) {
                flatpickr(fechasPicker, {
                    mode: "multiple",
                    dateFormat: "Y-m-d",
                    locale: "es"
                });
            }
        });

        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        }

        function toggleMenciones(targetId, elementoHeader) {
            var contenedor = document.getElementById(targetId);
            var flecha = elementoHeader.querySelector('.flecha-toggle');
            if (contenedor) {
                contenedor.classList.toggle('show');
                if (flecha) {
                    flecha.classList.toggle('rotada');
                }
            }
        }

        function abrirModalNueva(fecha, locutor) {
            document.getElementById('modalMencionIdInput').value = '';
            document.getElementById('modalFechaInput').value = fecha;
            document.getElementById('modalLocutorInput').value = locutor;
            document.getElementById('modalFechaTexto').innerText = fecha;
            document.getElementById('modalTextoInput').value = '';
            document.getElementById('modalAccionInput').value = 'guardar';

            document.getElementById('infoMarcado').classList.add('d-none');
            document.getElementById('btnMarcar').classList.remove('d-none');
            document.getElementById('btnDesmarcar').classList.add('d-none');
            var btnElim = document.getElementById('btnEliminar');
            if(btnElim) btnElim.classList.add('d-none');
            document.getElementById('enlaceHoraManual').style.display = 'none';
            document.getElementById('contenedorHoraManual').classList.add('d-none');
            document.getElementById('inputHoraManual').value = '';

            new bootstrap.Modal(document.getElementById('modalMencion')).show();
        }

        function abrirModalEditar(id, fecha, locutor, texto, marcadoAt) {
            document.getElementById('modalMencionIdInput').value = id;
            document.getElementById('modalFechaInput').value = fecha;
            document.getElementById('modalLocutorInput').value = locutor;
            document.getElementById('modalFechaTexto').innerText = fecha;
            document.getElementById('modalTextoInput').value = texto;
            document.getElementById('modalAccionInput').value = 'guardar';

            var infoMarcado = document.getElementById('infoMarcado');
            var horaMarcadoTexto = document.getElementById('horaMarcadoTexto');
            var btnMarcar = document.getElementById('btnMarcar');
            var btnDesmarcar = document.getElementById('btnDesmarcar');
            var btnEliminar = document.getElementById('btnEliminar');
            var enlaceHoraManual = document.getElementById('enlaceHoraManual');
            var contenedorHoraManual = document.getElementById('contenedorHoraManual');

            if(btnEliminar) btnEliminar.classList.remove('d-none');
            enlaceHoraManual.style.display = 'inline-block';
            contenedorHoraManual.classList.add('d-none');
            document.getElementById('inputHoraManual').value = '';

            if (marcadoAt && marcadoAt !== '') {
                infoMarcado.classList.remove('d-none');
                horaMarcadoTexto.innerText = marcadoAt;
                btnMarcar.classList.add('d-none');
                btnDesmarcar.classList.remove('d-none');
            } else {
                infoMarcado.classList.add('d-none');
                btnMarcar.classList.remove('d-none');
                btnDesmarcar.classList.add('d-none');
            }

            new bootstrap.Modal(document.getElementById('modalMencion')).show();
        }

        function mostrarInputHoraManual() {
            var contenedor = document.getElementById('contenedorHoraManual');
            contenedor.classList.toggle('d-none');
            if(!contenedor.classList.contains('d-none')) {
                document.getElementById('inputHoraManual').focus();
            }
        }

        function enviarFormulario(accion) {
            document.getElementById('modalAccionInput').value = accion;
            document.getElementById('modalMencion').querySelector('form').submit();
        }
    </script>
</body>
</html>
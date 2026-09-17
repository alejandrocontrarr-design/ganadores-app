<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ganador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .box-caza-premios {
            background-color: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">
        <h3 class="mb-4 text-dark fw-bold">➕ Registrar Nuevo Ganador</h3>

        <form action="{{ route('ganadores.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Edad</label>
                    <input type="number" name="edad" class="form-control" placeholder="Ej: 25">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" placeholder="Ej: 4431234567">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Facebook ID / Perfil</label>
                <input type="text" name="facebook_id" class="form-control" placeholder="Ej: juan.perez">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fecha Dinámica</label>
                    <input type="date" name="fecha_dinamica" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fecha Entrega</label>
                    <input type="date" name="fecha_entrega" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Programa</label>
                    <input type="text" name="programa" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Participando por (Premio)</label>
                    <input type="text" name="premio" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Patrocinador</label>
                    <input type="text" name="patrocinador" class="form-control">
                </div>
            </div>

            <!-- CHECK / MARCADOR CAZA PREMIOS -->
            <div class="box-caza-premios p-3 mb-4">
                <div class="form-check form-switch d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="caza_premios" id="caza_premios" value="1">
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="caza_premios">
                        ⚠️ Marcar como Caza Premios
                    </label>
                </div>
                <small class="text-muted d-block mt-1 ms-4"></small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('ganadores.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Registro</button>
            </div>
        </form>
    </div>
</body>
</html>
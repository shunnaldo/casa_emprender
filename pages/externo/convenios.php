<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/convenios.css">

</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Formulario de Registro</h2>
                    </div>
                    <div class="card-body p-4">
                        <form action="guardar_convenios.php" method="POST">
                            <!-- Datos de la Empresa -->
                            <div class="form-section">
                                <h3 class="section-title"><i class="bi bi-info-circle me-2"></i>Datos de la Empresa</h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="Nombre_Empresa" class="form-label required-field">Nombre Empresa</label>
                                        <input type="text" class="form-control" id="Nombre_Empresa" name="Nombre_Empresa" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Nombre_Fantasia" class="form-label">Nombre Fantasía</label>
                                        <input type="text" class="form-control" id="Nombre_Fantasia" name="Nombre_Fantasia">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="Direccion" name="Direccion">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <label class="form-check-label" for="Es_Online">Tienda fisica</label>
                                            <input class="form-check-input" type="checkbox" id="Es_Online" name="Es_Online" value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Rut_Empresa" class="form-label required-field">RUT Empresa</label>
                                        <input type="text" class="form-control" id="Rut_Empresa" name="Rut_Empresa" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="Patente" class="form-label">Patente(si aplica)</label>
                                        <input type="text" class="form-control" id="Patente" name="Patente">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Seremi" class="form-label">Seremi(si aplica)</label>
                                        <input type="text" class="form-control" id="Seremi" name="Seremi">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Representante -->
                            <div class="form-section">
                                <h3 class="section-title"><i class="bi bi-person-badge me-2"></i>Representante</h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="Nombre_Representante" class="form-label">Nombre Representante</label>
                                        <input type="text" class="form-control" id="Nombre_Representante" name="Nombre_Representante">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Cargo_Representante" class="form-label">Cargo Representante</label>
                                        <input type="text" class="form-control" id="Cargo_Representante" name="Cargo_Representante">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="Rut_Representante" class="form-label">RUT Representante</label>
                                    <input type="text" class="form-control" id="Rut_Representante" name="Rut_Representante">
                                </div>
                            </div>
                            
                            <!-- Beneficio -->
                            <div class="form-section">
                                <h3 class="section-title"><i class="bi bi-gift me-2"></i>Beneficio</h3>
                                <div class="mb-3">
                                    <label for="Beneficio" class="form-label">Beneficio</label>
                                    <textarea class="form-control" id="Beneficio" name="Beneficio" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <!-- Interlocutor -->
                            <div class="form-section">
                                <h3 class="section-title"><i class="bi bi-person-lines-fill me-2"></i>Interlocutor</h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="Interlocutor_Nombre" class="form-label required-field">Nombre</label>
                                        <input type="text" class="form-control" id="Interlocutor_Nombre" name="Interlocutor_Nombre" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Interlocutor_Correo" class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="Interlocutor_Correo" name="Interlocutor_Correo">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="Interlocutor_Telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="Interlocutor_Telefono" name="Interlocutor_Telefono" placeholder="+56 9xxxxxxx">
                                </div>
                            </div>
                            
                            <!-- Links -->
                            <div class="form-section">
                                <h3 class="section-title"><i class="bi bi-link-45deg me-2"></i>Links</h3>
                                <div id="linksContainer">
                                    <div class="link-item">
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label class="form-label">Tipo de Link</label>
                                                <select class="form-select" name="Link_Nombre[]">
                                                    <option value="Página Web">Página Web</option>
                                                    <option value="Instagram">Instagram</option>
                                                    <option value="Facebook">Facebook</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label class="form-label">Contenido (ej: @empresa, https://...)</label>
                                                <input type="text" class="form-control linkCuerpo" name="Link_Cuerpo[]">
                                            </div>
                                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger w-100 remove-link" disabled>
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="addLinkBtn" class="btn btn-outline-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar otro link
                                </button>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Enviar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="../../js/convenio.js"></script>
    <!-- Script para agregar links dinámicamente -->
    <script>
     
    </script>
</body>
</html>
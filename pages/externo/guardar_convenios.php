<?php
require_once("../../php/db.php");

// Sanitizar entradas
$nombreEmpresa = $_POST['Nombre_Empresa'];
$nombreFantasia = $_POST['Nombre_Fantasia'];
$direccion = $_POST['Direccion'];
$esOnline = isset($_POST['Es_Online']) ? 1 : 0;
$rutEmpresa = $_POST['Rut_Empresa'];
$patente = $_POST['Patente'];
$seremi = $_POST['Seremi'];
$nombreRep = $_POST['Nombre_Representante'];
$cargoRep = $_POST['Cargo_Representante'];
$rutRep = $_POST['Rut_Representante'];
$beneficio = $_POST['Beneficio'];

// Interlocutor (puede ser múltiple)
$interNombre = isset($_POST['Interlocutor_Nombre']) ? $_POST['Interlocutor_Nombre'] : [];
$interCorreo = isset($_POST['Interlocutor_Correo']) ? $_POST['Interlocutor_Correo'] : [];
$interTelefono = isset($_POST['Interlocutor_Telefono']) ? $_POST['Interlocutor_Telefono'] : [];

// Links (puede ser múltiple)
$linkNombre = isset($_POST['Link_Nombre']) ? $_POST['Link_Nombre'] : [];
$linkCuerpo = isset($_POST['Link_Cuerpo']) ? $_POST['Link_Cuerpo'] : [];

// =========================
// 1. Insertar Empresa
// =========================
$sqlEmpresa = "INSERT INTO tbl_Empresas 
(Nombre_Empresa, Nombre_Fantasia, Direccion, Es_Online, Rut_Empresa, Patente, Seremi,
 Nombre_Representante, Cargo_Representante, Rut_Representante, Beneficio, Firma, Estado_ID)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 
    (SELECT Estado_ID FROM tbl_Estados WHERE Nombre = 'En Proceso' LIMIT 1))";

$stmt = $connCasa->prepare($sqlEmpresa);
$stmt->bind_param(
    "sssssssssss", 
    $nombreEmpresa, $nombreFantasia, $direccion, $esOnline, $rutEmpresa, $patente, $seremi,
    $nombreRep, $cargoRep, $rutRep, $beneficio
);

if ($stmt->execute()) {
    $empresaId = $stmt->insert_id;

    // =========================
    // 2. Insertar Interlocutores
    // =========================
    if (!empty($interNombre)) {
        foreach ($interNombre as $index => $nombre) {
            $correo = $interCorreo[$index] ?? '';
            $telefono = $interTelefono[$index] ?? '';
            if (!empty($nombre)) {
                $sqlInter = "INSERT INTO tbl_Interlocutores (Nombre, Correo, Telefono, Empresa_ID) VALUES (?, ?, ?, ?)";
                $stmt2 = $connCasa->prepare($sqlInter);
                $stmt2->bind_param("sssi", $nombre, $correo, $telefono, $empresaId);
                $stmt2->execute();
            }
        }
    }

    // =========================
    // 3. Insertar Links
    // =========================
    if (!empty($linkNombre)) {
        foreach ($linkNombre as $index => $nombre) {
            $cuerpo = $linkCuerpo[$index] ?? '';
            if (!empty($nombre) && !empty($cuerpo)) {
                // Insertar link con Empresa_ID
                $sqlLink = "INSERT INTO tbl_Links (Nombre, Cuerpo, Empresa_ID) VALUES (?, ?, ?)";
                $stmt3 = $connCasa->prepare($sqlLink);
                $stmt3->bind_param("ssi", $nombre, $cuerpo, $empresaId);
                $stmt3->execute();
            }
        }
    }


    echo "✅ Empresa registrada con éxito.";
} else {
    echo "❌ Error: " . $stmt->error;
}

$connCasa->close();

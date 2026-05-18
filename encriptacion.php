<?php
// Variables para almacenar los estados y resultados en la interfaz
$error = "";
$procesado = false;
$ivHexadecimal = "";
$textoCifrado = "";
$textoDescifrado = "";

// 1. PROCESAMIENTO SEGURO: Capturar la petición cuando el formulario se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensajeOriginal = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    $claveUsuario = isset($_POST['clave']) ? trim($_POST['clave']) : '';

    // Validar que los campos obligatorios no estén vacíos
    if (empty($mensajeOriginal) || empty($claveUsuario)) {
        $error = "Todos los campos son obligatorios. Por favor, rellene el mensaje y la clave.";
    } else {
        $metodo = 'AES-128-CBC'; // Algoritmo requerido por la guía de laboratorio

        // Normalizar la clave del usuario a exactamente 16 caracteres (16 bytes para AES-128)
        // Si es más corta se rellena con caracteres nulos (\0), si es más larga se trunca
        $claveNormalizada = substr(str_pad($claveUsuario, 16, "\0"), 0, 16);

        // BLOQUE CRIPTOGRÁFICO DE OPENSSL
        // A. Generar un Vector de Inicialización (IV) dinámico basado en la longitud del método
        $ivLongitud = openssl_cipher_iv_length($metodo);
        $ivBytes = openssl_random_pseudo_bytes($ivLongitud);

        // B. Cifrar el mensaje en claro (retorna una representación Base64 por defecto)
        $textoCifrado = openssl_encrypt($mensajeOriginal, $metodo, $claveNormalizada, 0, $ivBytes);

        // C. Descifrar inmediatamente en el mismo hilo de ejecución para verificar la simetría
        $textoDescifrado = openssl_decrypt($textoCifrado, $metodo, $claveNormalizada, 0, $ivBytes);

        // D. Transformar el IV binario a formato hexadecimal para su despliegue en la interfaz
        $ivHexadecimal = bin2hex($ivBytes);

        $procesado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio OpenSSL - Cifrado Simétrico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="text-center mb-4">
                <span class="badge bg-secondary text-uppercase mb-2">Universidad Tecnológica de Panamá</span>
                <h2 class="fw-bold text-dark">Desarrollo de Software VII</h2>
                <p class="text-muted">Laboratorio: Seguridad en PHP con OpenSSL</p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Cifrado Simétrico Interactivo (AES-128-CBC)</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="encriptacion.php">

                        <div class="mb-3">
                            <label for="mensaje" class="form-label fw-semibold">Mensaje en Claro:</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="4" placeholder="Escribe aquí el texto secreto que deseas proteger..."><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="clave" class="form-label fw-semibold">Clave Secreta Compartida:</label>
                            <input type="password" class="form-control" id="clave" name="clave" placeholder="Introduce la clave secreta compartida">
                            <div class="form-text text-muted">El sistema normalizará automáticamente la longitud de la cadena a 16 bytes.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-bold py-2">Procesar y Cifrar Mensaje</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($procesado): ?>
                <div class="card shadow-sm border-success bg-white animate__animated animate__fadeIn">
                    <div class="card-header bg-success text-white py-3">
                        <h6 class="card-title mb-0 fw-bold">📊 Despliegue de Resultados Criptográficos</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted text-uppercase small">1. Vector de Inicialización (IV) [Hexadecimal]:</label>
                            <div class="p-3 bg-light border rounded font-monospace text-break text-danger fw-bold">
                                <?php echo htmlspecialchars($ivHexadecimal); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted text-uppercase small">2. Texto Cifrado Resultante [Base64]:</label>
                            <div class="p-3 bg-light border rounded font-monospace text-break text-primary">
                                <?php echo htmlspecialchars($textoCifrado); ?>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted text-uppercase small">3. Resultado Final del Descifrado (Hilo de Verificación):</label>
                            <div class="p-3 bg-light border border-success rounded font-monospace text-break text-success fw-bold">
                                <?php echo htmlspecialchars($textoDescifrado); ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

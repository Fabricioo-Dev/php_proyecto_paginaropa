<?php
// Muestra el mensaje de estado guardado en la sesión ("Cuenta creada", "Compra finalizada", etc.).
// Los scripts del Backend guardan el mensaje antes de redirigir:
//     $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '...'];   (tipo: success o error)
// y la página de destino incluye este archivo donde quiere que aparezca el aviso.
// Se borra después de mostrarlo para que aparezca una sola vez.

if (isset($_SESSION['mensaje'])) {
    $tipo = $_SESSION['mensaje']['tipo'] === 'success' ? 'success' : 'error';
    echo '<div class="alert ' . $tipo . '">' . htmlspecialchars($_SESSION['mensaje']['texto']) . '</div>';
    unset($_SESSION['mensaje']);
}

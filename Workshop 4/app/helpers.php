<?php
function e($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
function url(string $ruta, array $parametros = []): string {
    return 'index.php?' . http_build_query(['r' => $ruta] + $parametros);
}

# Forzar codificación UTF-8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# Limpia la pantalla
Clear-Host

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "   GESTOR DE SUBIDAS A GIT (CORREGIDO)    " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# 1. BÚSQUEDA INTELIGENTE DE LA RAÍZ GIT
# Preguntamos a Git dónde está la carpeta raíz (silenciando errores con 2>$null)
$gitRoot = git rev-parse --show-toplevel 2>$null

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR CRÍTICO: No estás dentro de un repositorio Git." -ForegroundColor Red
    Write-Host "Asegúrate de haber hecho 'git init' o de estar en la carpeta correcta." -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit
}

# Nos movemos a la raíz
Set-Location $gitRoot
Write-Host "✅ Repositorio detectado en: $gitRoot" -ForegroundColor DarkGray

# 2. COMPROBACIÓN DE CAMBIOS (Aquí estaba el error, ya corregido)
$status = git status --porcelain
# CORRECCIÓN: Usamos $null en vez de -null
if ($null -eq $status -or $status -eq "") {
    Write-Host "¡OJO! No hay cambios pendientes para subir." -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit
}

# 3. AGREGAR ARCHIVOS
Write-Host "1. Agregando archivos al staging..." -ForegroundColor Green
git add .

if ($LASTEXITCODE -ne 0) {
    Write-Host "Error al agregar archivos. Revisar permisos." -ForegroundColor Red
    exit
}

# 4. MENSAJE DEL COMMIT
do {
    $comentario = Read-Host "2. Escribe el mensaje del commit (Obligatorio)"
    if ([string]::IsNullOrWhiteSpace($comentario)) {
        Write-Host "   Error: El comentario no puede estar vacío." -ForegroundColor Red
    }
} while ([string]::IsNullOrWhiteSpace($comentario))

Write-Host "   Haciendo commit..." -ForegroundColor Gray
git commit -m "$comentario"

# 5. GESTIÓN DE RAMA AUTOMÁTICA
$ramaActual = git rev-parse --abbrev-ref HEAD
Write-Host "   Estás en la rama: '$ramaActual'" -ForegroundColor Magenta

$inputRama = Read-Host "3. ¿A qué rama subir? (Enter para usar '$ramaActual')"

if ([string]::IsNullOrWhiteSpace($inputRama)) {
    $NombreRama = $ramaActual
} else {
    $NombreRama = $inputRama
}

# 6. SUBIDA (PUSH)
Write-Host "   Subiendo cambios a 'origin/$NombreRama'..." -ForegroundColor Green
git push origin $NombreRama

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ ¡ÉXITO! Todo subido correctamente." -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "❌ ERROR EN EL PUSH." -ForegroundColor Red
    Write-Host "Intenta hacer un 'git pull' primero." -ForegroundColor Yellow
}

Write-Host ""
Read-Host "Presiona Enter para cerrar"
# Forzar codificación UTF-8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# Limpia la pantalla
Clear-Host

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "   BAJAR CAMBIOS DE GITHUB (WINDOWS)      " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# 1. COMPROBACIÓN: ¿Git está instalado?
if (-not (Get-Command "git" -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR CRÍTICO: Git no está instalado." -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit
}

# 2. BÚSQUEDA DE LA RAÍZ DEL PROYECTO
$gitRoot = git rev-parse --show-toplevel 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: No estás en un repositorio Git." -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit
}
Set-Location $gitRoot

# 3. SEGURIDAD: ¿Tienes cosas a medio hacer?
# Si bajas cambios mientras tienes archivos modificados, puedes crear conflictos feos.
$status = git status --porcelain
if ($null -ne $status -and $status -ne "") {
    Write-Host "⚠️  PELIGRO: Tienes cambios locales sin guardar (Commits pendientes)." -ForegroundColor Red
    Write-Host "Si bajas cambios ahora, podrías tener conflictos y romper tu código." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Opciones:" -ForegroundColor Gray
    Write-Host "1. Cancela, usa tu script de 'subir' para guardar tus cambios primero."
    Write-Host "2. O borra tus cambios manuales si no te importan."
    Write-Host ""
    Read-Host "El script se detendrá por seguridad. Presiona Enter para salir"
    exit
}

# 4. DETECTAR RAMA Y BAJAR
$ramaActual = git rev-parse --abbrev-ref HEAD
Write-Host "✅ Todo limpio. Estás en la rama: '$ramaActual'" -ForegroundColor Green
Write-Host "⬇️  Bajando cambios desde GitHub..." -ForegroundColor Cyan
Write-Host ""

git pull origin $ramaActual

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ ¡PROCESO TERMINADO! Tu código está actualizado." -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "❌ ERROR AL BAJAR." -ForegroundColor Red
    Write-Host "Puede que no tengas internet o permisos en el repositorio." -ForegroundColor Yellow
}

Write-Host ""
Read-Host "Presiona Enter para cerrar"
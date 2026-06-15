#!/bin/bash

# Definición de colores para que se vea igual de bien
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

clear

echo -e "${CYAN}==========================================${NC}"
echo -e "${CYAN}   GESTOR DE SUBIDAS A GIT (MAC OS)       ${NC}"
echo -e "${CYAN}==========================================${NC}"
echo ""

# 1. BÚSQUEDA INTELIGENTE DE LA RAÍZ GIT
# Preguntamos a Git dónde está la raíz del proyecto
git_root=$(git rev-parse --show-toplevel 2>/dev/null)

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR CRÍTICO: No estás dentro de un repositorio Git.${NC}"
    echo -e "${YELLOW}Asegúrate de haber hecho 'git init' o de estar en la carpeta correcta.${NC}"
    read -p "Presiona Enter para salir"
    exit 1
fi

# Nos movemos a la raíz
cd "$git_root"
echo -e "✅ Repositorio detectado en: ${NC}$git_root"

# 2. COMPROBACIÓN: ¿Hay cambios pendientes?
if [ -z "$(git status --porcelain)" ]; then
    echo -e "${YELLOW}¡OJO! No hay cambios pendientes para subir.${NC}"
    read -p "Presiona Enter para salir"
    exit 0
fi

# 3. AGREGAR ARCHIVOS
echo -e "${GREEN}1. Agregando archivos al staging...${NC}"
git add .

if [ $? -ne 0 ]; then
    echo -e "${RED}Error al agregar archivos. Revisar permisos.${NC}"
    exit 1
fi

# 4. MENSAJE DEL COMMIT (Con validación)
comentario=""
while [[ -z "$comentario" ]]; do
    read -p "2. Escribe el mensaje del commit (Obligatorio): " comentario
    if [[ -z "$comentario" ]]; then
        echo -e "${RED}   Error: El comentario no puede estar vacío.${NC}"
    fi
done

echo -e "   Haciendo commit..."
git commit -m "$comentario"

# 5. GESTIÓN DE RAMA AUTOMÁTICA
rama_actual=$(git rev-parse --abbrev-ref HEAD)
echo -e "${MAGENTA}   Estás en la rama: '$rama_actual'${NC}"

read -p "3. ¿A qué rama subir? (Enter para usar '$rama_actual'): " input_rama

if [[ -z "$input_rama" ]]; then
    nombre_rama=$rama_actual
else
    nombre_rama=$input_rama
fi

# 6. SUBIDA (PUSH)
echo -e "${GREEN}   Subiendo cambios a 'origin/$nombre_rama'...${NC}"
git push origin "$nombre_rama"

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${CYAN}✅ ¡ÉXITO! Todo subido correctamente.${NC}"
else
    echo ""
    echo -e "${RED}❌ ERROR EN EL PUSH.${NC}"
    echo -e "${YELLOW}Intenta hacer un 'git pull' primero.${NC}"
fi

echo ""
read -p "Presiona Enter para cerrar"

#!/bin/bash

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

clear

echo -e "${CYAN}==========================================${NC}"
echo -e "${CYAN}   BAJAR CAMBIOS DE GITHUB (MAC OS)       ${NC}"
echo -e "${CYAN}==========================================${NC}"
echo ""

# 1. BÚSQUEDA DE LA RAÍZ
git_root=$(git rev-parse --show-toplevel 2>/dev/null)

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: No estás en un repositorio Git.${NC}"
    read -p "Presiona Enter para salir"
    exit 1
fi
cd "$git_root"

# 2. SEGURIDAD: ¿Tienes cosas a medio hacer?
if [ -n "$(git status --porcelain)" ]; then
    echo -e "${RED}⚠️  PELIGRO: Tienes cambios locales sin guardar.${NC}"
    echo -e "${YELLOW}Por seguridad, no puedes bajar cambios si tienes trabajo pendiente.${NC}"
    echo -e "Primero usa tu script de subir cambios (hacer commit) y luego vuelve aquí."
    echo ""
    read -p "Presiona Enter para salir"
    exit 1
fi

# 3. DETECTAR RAMA Y BAJAR
rama_actual=$(git rev-parse --abbrev-ref HEAD)
echo -e "${GREEN}✅ Todo limpio. Rama actual: '$rama_actual'${NC}"
echo -e "${CYAN}⬇️  Bajando cambios desde GitHub...${NC}"
echo ""

git pull origin "$rama_actual"

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${CYAN}✅ ¡PROCESO TERMINADO! Tu código está actualizado.${NC}"
else
    echo ""
    echo -e "${RED}❌ ERROR AL BAJAR.${NC}"
fi

echo ""
read -p "Presiona Enter para cerrar"
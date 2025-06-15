#!/bin/bash

echo "Iniciando Laravel..."
cd /grupo-5/BackEnd
php artisan serve &

echo "Iniciando Vue..."
cd /grupo-5/FrontEnd
npm run dev &

echo "Esperando 5 segundos..."
sleep 5

echo "Iniciando Cloudflared..."
cloudflared tunnel --url http://localhost:5173

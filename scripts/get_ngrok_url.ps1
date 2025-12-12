# Script para obtener la URL de ngrok y mostrarla
# Ejecuta esto en PowerShell mientras ngrok está corriendo

$ngrokApiUrl = "http://127.0.0.1:4040/api/tunnels"

try {
    $response = Invoke-RestMethod -Uri $ngrokApiUrl
    $tunnel = $response.tunnels | Where-Object { $_.name -eq "command_line" }
    
    if ($tunnel) {
        $publicUrl = $tunnel.public_url -replace "^tcp://"
        Write-Host "=" * 60
        Write-Host "🔗 Túnel ngrok activo!" -ForegroundColor Green
        Write-Host "=" * 60
        Write-Host "Host: " -NoNewline
        Write-Host $publicUrl.Split(":")[0] -ForegroundColor Cyan
        Write-Host "Puerto: " -NoNewline
        Write-Host $publicUrl.Split(":")[1] -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Copia esto en Railway:" -ForegroundColor Yellow
        Write-Host "DB_HOST=$($publicUrl.Split(':')[0])"
        Write-Host "DB_PORT=$($publicUrl.Split(':')[1])"
        Write-Host "=" * 60
    }
    else {
        Write-Host "❌ No se encontró el túnel. ¿ngrok está ejecutándose?" -ForegroundColor Red
    }
}
catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Asegúrate de que ngrok está ejecutándose" -ForegroundColor Yellow
}

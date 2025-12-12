# Guía: Conectar SQL Server Local con Railway usando ngrok

## ¿Qué es ngrok?

ngrok crea un túnel público desde tu máquina local a Internet, permitiendo que Railway acceda a tu SQL Server.

## Pasos:

### 1. Descargar ngrok

-   Ve a https://ngrok.com/download
-   Descarga la versión para Windows
-   Descomprime en C:\ngrok\

### 2. Crear cuenta en ngrok (gratis)

-   Regístrate en https://dashboard.ngrok.com/
-   Obtén tu "Authtoken"

### 3. Configurar ngrok

Abre PowerShell y ejecuta:

```powershell
cd C:\ngrok
.\ngrok config add-authtoken TU_AUTH_TOKEN_AQUI
```

### 4. Iniciar el túnel hacia SQL Server

```powershell
.\ngrok tcp 1433
```

Deberías ver algo como:

```
Session Status                online
Account                       tu@email.com
Version                        3.x.x
Region                         us (United States)
Latency                        50ms
Web Interface                  http://127.0.0.1:4040
Forwarding                     tcp://0.tcp.ngrok.io:12345 -> localhost:1433
```

**IMPORTANTE:** Copia la URL: `0.tcp.ngrok.io:12345` (el puerto cambia cada vez)

### 5. En Railway, configura estas variables:

```
DB_HOST=0.tcp.ngrok.io
DB_PORT=12345
DB_USERNAME=laravel_user
DB_PASSWORD=123
DB_DATABASE=Clinica_01
DB_CONNECTION=sqlsrv
```

### 6. Haz redeploy en Railway

---

## Notas importantes:

-   ⚠️ El puerto de ngrok cambia cada vez que reinicia
-   ⚠️ ngrok gratuito solo permite 3 túneles simultáneos
-   ✅ El túnel debe estar ejecutándose SIEMPRE que uses Railway
-   ✅ No expone tu contraseña de SQL Server (ngrok maneja la seguridad)

## Alternativa: Usar ngrok permanente

Para mantener el mismo puerto, puedes:

1. Comprá un plan Pro de ngrok ($10/mes)
2. O crea un script que inicie ngrok automáticamente al iniciar Windows

---

## Script para iniciar automáticamente:

Crea un archivo `start_tunnel.ps1`:

```powershell
# Inicia ngrok en background
Start-Process -NoNewWindow -FilePath "C:\ngrok\ngrok.exe" -ArgumentList "tcp 1433"
Write-Host "Túnel ngrok iniciado. Abre http://127.0.0.1:4040 para ver los detalles"
```

Luego añádelo a tu Startup de Windows.

---

## Verificar que funciona:

```powershell
# En PowerShell, prueba la conexión
sqlcmd -S 0.tcp.ngrok.io,12345 -U laravel_user -P 123
```

Si conecta, ¡funciona!

---

**¿Próximos pasos?**

1. Instala ngrok
2. Inicia el túnel
3. Obtén la URL (0.tcp.ngrok.io:PUERTO)
4. Configura en Railway
5. Redeploy

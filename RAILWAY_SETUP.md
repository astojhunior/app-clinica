# 🚀 Despliegue en Railway - Clínica Calidad

## Requisitos Previos

1. **Cuenta en Railway** - https://railway.app
2. **GitHub conectado** - El repositorio debe estar en GitHub (ya lo está ✅)
3. **SQL Server accesible** - Debe ser alcanzable desde Internet

---

## Paso 1: Configurar SQL Server para Railway

### Si tu SQL Server está en Azure SQL Database (Recomendado):

1. Ve a tu SQL Server en Azure Portal
2. En **Seguridad > Firewalls y redes virtuales**
3. **Añade la IP de Railway:**

    - IP de Railway: `0.0.0.0/0` (permite todas)
    - O específicamente: `140.0.0.0/8` (rangos de Railway)

4. **O configura regla específica:**
    - En tu SQL Server → Firewalls → Añade regla
    - Nombre: `Railway`
    - IP inicio: `0.0.0.0`
    - IP fin: `255.255.255.255`

### Si tu SQL Server es local (en tu máquina): ✅ USA NGROK

⚠️ **Railway NO puede acceder a máquinas locales directamente**

**Solución: Usar ngrok para crear un túnel**

1. **Descarga ngrok** - https://ngrok.com/download
2. **Inicia el túnel:**
    ```powershell
    ngrok tcp 1433
    ```
3. **Copia la URL que te muestra** - Algo como: `0.tcp.ngrok.io:12345`
4. **En Railway configura:**
    ```
    DB_HOST=0.tcp.ngrok.io
    DB_PORT=12345
    DB_USERNAME=laravel_user
    DB_PASSWORD=123
    ```

📖 **Guía completa:** Ver `NGROK_SETUP.md`

### Si tu SQL Server es en otro servidor remoto:

-   Asegúrate de que sea accesible desde Internet
-   Permite conexiones en el firewall del servidor
-   Usa las credenciales correctas

---

## Paso 2: Obtener datos de conexión a SQL Server

En Azure SQL Database:

```
Servidor: nombre-servidor.database.windows.net
Base de datos: Clinica_01
Usuario: laravel_user
Contraseña: (tu contraseña segura)
Puerto: 1433
```

---

## Paso 3: Configurar en Railway

### Opción A: Desde la interfaz web

1. Ve a tu proyecto en Railway
2. Haz clic en el servicio `web`
3. Ve a la pestaña **Variables**
4. Añade cada variable (ver `RAILWAY_ENV_VARIABLES.md`)

### Opción B: Desde la CLI de Railway

```powershell
# Instalar Railway CLI
npm install -g @railway/cli

# Logearse
railway login

# Ir al proyecto
cd c:\Proyectos\Clinica_Calidad
railway link

# Crear .env con variables
railway variables

# Deployar
railway up
```

---

## Variables principales a configurar:

```env
# OBLIGATORIAS
APP_KEY=base64:oxXGUHwd2igORZsM07uDXKPsYonDfLMI2R6MjaWOf4M=
DB_HOST=nombre-servidor.database.windows.net
DB_DATABASE=Clinica_01
DB_USERNAME=laravel_user
DB_PASSWORD=tu_contraseña_segura
APP_URL=https://tu-app.railway.app

# RECOMENDADAS
APP_ENV=production
APP_DEBUG=false
MAIL_FROM_ADDRESS=noreply@example.com
```

---

## Paso 4: Verificar el despliegue

1. Ve a Railway → Tu proyecto → Logs
2. Debería ver:

    ```
    ✅ Aplicación lista!
    🌐 Apache iniciando en puerto 80...
    ```

3. Abre en navegador: `https://tu-app.railway.app`

---

## Troubleshooting

### Error: "Cannot connect to database"

```
✅ Solución: Verifica que:
- Las credenciales SQL Server son correctas
- El firewall de SQL Server permite conexiones desde Railway
- La base de datos existe y está activa
```

### Error: "Could not decode JSON"

```
✅ Solución: Algunas variables no se guardaron correctamente
- Verifica que no haya caracteres especiales sin escapar
- Usa comillas simples para valores con espacios
```

### El sitio no carga

```
✅ Solución:
- Espera 2-3 minutos a que termine la inicialización
- Revisa los logs en Railway → Logs
- Verifica que APP_KEY esté configurado
```

---

## Próximos pasos

1. ✅ Configura variables de entorno en Railway
2. ✅ Verifica conexión a SQL Server
3. ✅ Haz redeploy
4. ✅ Accede a la aplicación

Si todo está bien, tu app debería estar disponible en:

```
https://tu-app.railway.app
```

---

## Comandos útiles en Railway

```bash
# Ver logs en tiempo real
railway logs -f

# Ver variables actuales
railway variables

# Ejecutar comando Artisan
railway run php artisan migrate

# SSH a contenedor
railway ssh
```

---

**¿Necesitas ayuda?** Revisa los logs en Railway para ver detalles específicos del error.

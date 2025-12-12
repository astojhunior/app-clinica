# ==================================================

# CONFIGURACIÓN DE RAILWAY - CLÍNICA CALIDAD

# ==================================================

# Copia estas variables en el panel de Railway

#

# Pasos:

# 1. Ve a tu servicio web en Railway

# 2. Click en "Variables"

# 3. Copia y pega estas líneas

# 4. Actualiza los valores según tu configuración

# ==================================================

# ============ LARAVEL CORE ============

APP_NAME="Clínica Calidad"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:oxXGUHwd2igORZsM07uDXKPsYonDfLMI2R6MjaWOf4M=
APP_URL=https://your-railway-url.railway.app

# ============ LOGGING ============

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=notice

# ============ DATABASE - SQL SERVER ============

# Importante: Asegúrate de que tu SQL Server sea accesible desde Internet

# Si está en una red privada, necesitarás configurar un VPN o IP allowlist

DB_CONNECTION=sqlsrv
DB_HOST=your-sql-server.database.windows.net
DB_PORT=1433
DB_DATABASE=Clinica_01
DB_USERNAME=laravel_user
DB_PASSWORD=your_secure_password_here

# ============ SESIONES ============

SESSION_DRIVER=file
SESSION_LIFETIME=120

# ============ CACHÉ ============

CACHE_STORE=file

# ============ COLAS ============

QUEUE_CONNECTION=sync

# ============ BROADCAST ============

BROADCAST_CONNECTION=log

# ============ FILESYSTEM ============

FILESYSTEM_DISK=local

# ============ MAIL - MAILERSEND ============

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailersend.net
MAIL_PORT=587
MAIL_USERNAME=MS_4VPi29@test-q3enl6k627042vwr.mlsender.net
MAIL_PASSWORD=mssp.COJMZ8j.k68zxl2d10e4j905.O878Eij
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@clinic.com
MAIL_FROM_NAME="Clínica Calidad"

MAILERSEND_API_KEY=mlsn.96a2dc4f70b99f623a05b23c2cb26250ba3fe1a7c5076eda0da2f1bc7674afa7

# ============ OPCIONAL - AWS S3 ============

# Si quieres subir archivos a S3

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# ============ LOCALES ============

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

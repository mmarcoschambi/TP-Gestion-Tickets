# TP — Gestión de tickets e incidencias con MantisBT

> **BORRADOR, NO ENTREGA FINAL — prueba funcional básica auxiliar completada.** Se aplicó el Compose final a la comparación nativa aislada y se comprobaron acceso, un ticket sintético, adjunto, notas, historial, resolución, cierre y persistencia tras recrear sólo el contenedor de la aplicación. Esto no completa las ocho actividades académicas: sus diez registros y evidencias siguen pendientes.

El [borrador del TP](BORRADOR_TP_GESTION_TICKETS.md) contiene dos puntos teóricos, ocho actividades prácticas y **exactamente diez incidencias propuestas** para un caso ilustrativo de ventas e inventario. **INC-09 e INC-10** son las dos reservas para defensa; aún no son tickets reales ni se informan ocho casos resueltos.

## Alcance público

La publicación prevista en `mmarcoschambi/TP-Gestion-Tickets` se limita a estos cinco archivos:

```text
.
├── .gitignore
├── README.md
├── BORRADOR_TP_GESTION_TICKETS.md
└── src/
    └── mantis/
        ├── docker-compose.yml
        └── leame.txt
```

No se redistribuyen los PDF, videos, archivos de cátedra ni otros materiales originales. Las rutas de **S1–S4** citadas en el borrador corresponden al espacio de trabajo original y **no están incluidas** en el clon público. Se conserva su procedencia para no presentar esas fuentes como propias. Las referencias IEEE y oficiales de MantisBT están enlazadas en el borrador.

Quedan fuera también instrucciones y estado de herramientas de IA, configuración local, credenciales, datos SQL, personalizaciones generadas, volcados y registros. La allowlist de `.gitignore` no reemplaza revisar los archivos antes de publicarlos.

## Requisitos

- Git para obtener el repositorio cuando esté publicado.
- WSL con Bash, Docker Engine y el complemento Docker Compose accesibles desde esa distribución.
- Puerto local 8989 disponible, acceso para descargar imágenes y espacio para datos persistentes.
- Autorización para un laboratorio aislado y uso exclusivo de datos ficticios.

## Preparación propuesta desde un clon

Los comandos siguientes son el procedimiento para un clon nuevo, no una transcripción de pruebas. En WSL Ubuntu, con Docker 29.5.3 y Compose 5.1.4, el montaje SQL original de Windows en `/mnt/d` (v9fs) falló al crear un índice único en el esquema 191: MariaDB no pudo renombrar un archivo de tabla. Con las mismas imágenes y volúmenes nativos nuevos para SQL y configuración se alcanzaron esquema 213 y página de acceso HTTP 200. Se preservaron los datos anteriores, sin migración. El contraste no demuestra causalidad general ni éxito del TP completo.

### Comprobaciones observadas y pendientes

La prueba aislada `tp-gestion-tickets-native-validation` utilizó el proyecto privado auxiliar `VALIDACION-TP-Ventas` (ID 1) y **un único ticket sintético `SMOKE-TP-001` (ID 1)**. No corresponde a INC-01–INC-10 ni sustituye sus diez casos y dos reservas de defensa.

| Comprobación | Resultado y límite |
|---|---|
| Compose final | Aplicado a la comparación nativa existente; `config --quiet`, `up -d` y `ps` satisfactorios. |
| Acceso y seguimiento básico | Acceso con la cuenta administrativa inicial del proveedor; ticket, notas e historial comprobados; resolución (80) y cierre (90) confirmados. No valida la matriz de roles. |
| Adjunto | Un TXT sintético de 81 bytes cargado y descargado con contenido coincidente. |
| Persistencia | Tras recrear **sólo el contenedor de la aplicación**, persistieron acceso, estado 90, bytes del adjunto y diez entradas de historial. No prueba recreación de la base ni restauración de respaldo. |
| Acceso sin autenticar | La vista redirigió al acceso sin exponer el resumen del ticket. **No prueba reporte anónimo.** |
| Pendiente académico | Diez registros INC-01–INC-10, sus evidencias y las ocho actividades; conservar INC-09 e INC-10 para defensa. |
| Funciones no probadas | Matriz de roles, campos personalizados, SMTP, reporte anónimo y restablecimiento de contraseña; informes académicos pendientes. |

No se enviaron correos reales ni se crearon o accedieron archivos `.env`. No se aportan capturas inventadas ni se presentan usuarios reales. La cuenta inicial quedó **sin endurecer, sólo para esta prueba aislada en loopback**: cambiar sus credenciales antes de cualquier uso futuro. Al cerrar la validación se detuvieron ambos contenedores de prueba, conservando sus volúmenes y datos; el laboratorio no quedó activo. No se publica ninguna contraseña ni se afirma una versión exacta de MantisBT a partir de la imagen.

```bash
git clone https://github.com/mmarcoschambi/TP-Gestion-Tickets.git
cd TP-Gestion-Tickets
cd src/mantis
read -rsp 'Contraseña SQL administrativa: ' MYSQL_ROOT_PASSWORD; printf '\n'
read -rsp 'Contraseña SQL de la aplicación: ' MYSQL_PASSWORD; printf '\n'
export MYSQL_ROOT_PASSWORD MYSQL_PASSWORD
export MYSQL_DATABASE=bugtracker MYSQL_USER=mantisbt
docker compose --env-file /dev/null config --quiet
docker compose --env-file /dev/null up -d
docker compose --env-file /dev/null ps
```

Ingresar dos contraseñas propias y distintas, nunca vacías. No escribirlas en comandos, documentos ni archivos de credenciales. `--env-file /dev/null` evita la lectura implícita de `.env`; mantener esa opción en **cada** invocación de Compose. `config --quiet` evita mostrar la configuración interpolada con secretos.

Las variables sólo se transmiten a procesos de esa terminal. Antes de comandos posteriores, volver a proporcionarlas si se abrió otra sesión o se retiraron del entorno. `MYSQL_DATABASE` y `MYSQL_USER` son parámetros **no secretos**, con valores predeterminados `bugtracker` y `mantisbt`; si se cambian, el instalador debe usar los mismos valores. Al terminar los comandos que necesitan las variables:

```bash
unset MYSQL_ROOT_PASSWORD MYSQL_PASSWORD MYSQL_DATABASE MYSQL_USER
```

## Acceso y precauciones

- Abrir **http://localhost:8989** en el anfitrión; la publicación es `127.0.0.1:8989:80`, no todas las interfaces. Comprobar el acceso local efectivo en WSL sin abrirlo a Internet.
- El host SQL interno es **mysql**. Usar la base, usuario y contraseña elegidos para SQL; **no son la identidad administrativa de MantisBT**. Consultar privadamente las credenciales iniciales del proveedor y cambiarlas inmediatamente. No asumir que el instalador siempre requiere acceso SQL root.
- Revisar `MANTIS_ENABLE_ADMIN` y restringir o deshabilitar el instalador y las funciones administrativas después de instalar. La plantilla no constituye una configuración de producción.
La plantilla de cátedra fue adaptada, no copiada sin cambios. La persistencia actual es:

| Montaje | Uso |
|---|---|
| `mysql_data:/var/lib/mysql` | Volumen nativo administrado por Docker para MariaDB. |
| `mantis_config:/var/www/html/config` | Volumen nativo administrado por Docker para configuración. |
| `./custom:/var/www/html/custom` | Bind opcional, relativo a `src/mantis`, ignorado y no requerido para campos personalizados administrados desde MantisBT. |

Los dos volúmenes se declaran al nivel superior de Compose. Docker los administra fuera del repositorio: no se versionan y persisten al recrear contenedores. Sus nombres efectivos dependen del proyecto Compose; las claves coinciden con la comparación `tp-gestion-tickets-native-validation`. No se publican datos ni configuración generada. Persistencia no equivale a respaldo; no ejecutar `down -v` ni eliminar los datos anteriores.
- Cambiar variables de inicialización o recrear contenedores **no rota las contraseñas de una base ya existente**. No borrar datos como solución; planificar cambios y respaldos autorizados.
- Se conservan `xlrl/mantisbt:latest` y `mariadb:latest`. No fijan versiones ni garantizan compatibilidad o disponibilidad inmediata de SQL. Registrar las versiones efectivas y probarlas antes de afirmar éxito.

El [instructivo de instalación](src/mantis/leame.txt) amplía estos pasos y sus límites.

## Próximos pasos del laboratorio

1. Confirmar con la cátedra fecha, integrantes, formato y proyecto realmente conocido.
2. Cambiar las credenciales iniciales y revisar el endurecimiento antes de reutilizar el laboratorio; ampliar las pruebas más allá del contenedor de aplicación sin exponer secretos.
3. Ejecutar las ocho actividades con usuarios y datos ficticios; asociar las diez referencias a IDs reales.
4. Validar permisos, formularios, fechas, informes, correo y recuperación; conservar dos casos abiertos para defensa.
5. Incorporar únicamente evidencia depurada y distinguir resultados observados de procedimientos pendientes antes de preparar la entrega final.

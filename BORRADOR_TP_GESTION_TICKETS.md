# Borrador del TP: gestión de tickets e incidencias con MantisBT

> **Estado: borrador académico; prueba funcional básica auxiliar completada.** En una comparación aislada con volúmenes nativos y el Compose final se comprobaron acceso autenticado, un ticket sintético, adjunto, notas, historial, resolución, cierre y persistencia tras recrear sólo el contenedor de aplicación. No se probaron la matriz de roles, campos personalizados, SMTP, reporte anónimo ni restablecimiento de contraseña. Las ocho actividades y los diez registros académicos con sus evidencias siguen pendientes; no se aportan capturas ni se presentan las incidencias ficticias como pruebas realizadas.

## Alcance y requisitos de la consigna

Este trabajo corresponde a Herramientas Informáticas Avanzadas, Unidad 5: Soporte y Mantenimiento. Se propone organizar el soporte de un sistema de ventas e inventario mediante MantisBT sobre Docker.

| Requisito | Aplicación prevista |
|---|---|
| Equipo de hasta dos integrantes | Completar nombres antes de entregar; los integrantes no equivalen a la dotación del caso. |
| Organización de diez empleados | Distribuir diez personas entre operación, soporte y conducción. |
| Software recientemente puesto en producción | Usar como caso ilustrativo un sistema de ventas e inventario. |
| Software conocido por los estudiantes | Confirmar que el proyecto elegido coincide con una experiencia real de los integrantes. |
| Instalación de MantisBT o GLPI sobre Docker | Se elige MantisBT y la plantilla local suministrada. |
| Desarrollo teórico y ocho actividades prácticas | Se incluyen ambos puntos teóricos y las ocho actividades como procedimientos propuestos. |
| Al menos diez incidencias y al menos dos para defensa | Catálogo de diez casos propuestos; INC-09 e INC-10 reservados para defensa. No son registros reales. |

La fecha indicada literalmente es **«Domingo 18 de Noviembre»**, sin año explícito en el enunciado. No corresponde inferir el año por el nombre del archivo ni corregir el día: debe consultarse a la cátedra. El formato de entrega y la obligatoriedad de capturas también requieren confirmación. [S1, p. 1]

### Caso propuesto y límites

El sistema ilustrativo permite registrar ventas, actualizar existencias, administrar productos y consultar movimientos. Tras su liberación aparecen dificultades operativas que hoy podrían comunicarse informalmente, sin responsable ni seguimiento común. Esta situación es una hipótesis de trabajo, no un diagnóstico observado.

Antes de presentar el TP se deberán ajustar funciones, arquitectura, versión y ejemplos al software que efectivamente conocen los estudiantes. No se afirma que hayan desarrollado el sistema aquí descripto. Los datos del laboratorio serán ficticios y no incluirán clientes, comprobantes ni información comercial reales.

## Punto teórico 1. Tipos de mantenimiento de software

Los cuatro tipos siguientes se fundamentan en el complemento IEEE Technology Navigator, apartado **“Types of Maintenance”**, autorizado separadamente de los materiales de la materia. Los ejemplos son elaboración propuesta para este caso, no ejemplos tomados de IEEE. [S6]

| Tipo | Definición | Ejemplo propuesto en ventas e inventario |
|---|---|---|
| Correctivo | Modificación posterior a la entrega para corregir fallas. | Corregir un descuento duplicado que produce un total de venta incorrecto. |
| Adaptativo | Modificación para mantener operativo el software ante cambios del entorno. | Adaptar el acceso a datos a una nueva versión del motor de base de datos. |
| Perfectivo | Mejora de prestaciones, rendimiento o documentación del software. | Optimizar la consulta de movimientos para reducir la espera con catálogos grandes. |
| Preventivo | Modificación de fallas latentes antes de que generen fallas operativas. | Reforzar validaciones de consistencia para impedir futuros movimientos de stock inválidos. |

La clasificación depende del propósito de la modificación: mejorar una consulta puede ser perfectivo; corregir un tiempo de respuesta que incumple un requisito existente puede ser correctivo. No se debe asignar el tipo únicamente por el módulo intervenido. [S6; aplicación razonada al caso]

**Clasificar mantenimiento no equivale a gestionar incidentes.** El tipo de mantenimiento describe la finalidad del trabajo sobre el software. La gestión de incidentes organiza la detección, registro, clasificación, asignación y restauración del servicio. Puede restaurarse el servicio mediante una solución temporal sin haber eliminado todavía la causa subyacente. [S2, pp. 2, 9–11; S3, pp. 1, 5–6]

La gestión de problemas estudia causas subyacentes; la gestión de cambios trata modificaciones que requieren evaluación y autorización. Agregar una nueva funcionalidad no debe presentarse automáticamente como incidente. Una falla recurrente podría originar un análisis de problema y una corrección controlada, vinculados al ticket original. [S2, pp. 4 y 11; S3, pp. 1 y 6]

## Punto teórico 2. Recomendaciones y elección de herramienta

### Proceso recomendado

Se propone una mesa de ayuda como punto inicial de contacto. En una organización pequeña una persona puede asumir esa función sin crear un departamento independiente. El objetivo es restaurar la operación con trazabilidad, no agregar pasos burocráticos. [S2, p. 3; S3, p. 1]

1. **Registrar y admitir:** identificar módulo, síntoma, momento, versión, pasos y evidencia; verificar alcance y posibles duplicados. Cada registro real deberá conservar su identificador de MantisBT. [S2, p. 9; S3, p. 5]
2. **Clasificar:** determinar categoría, impacto y urgencia; justificar la prioridad y describir la severidad del defecto. La matriz propuesta figura en la actividad 3. [S2, pp. 6 y 10]
3. **Asignar y comprometer una fecha prevista:** designar responsable, estimar atención y resolución y comunicar el objetivo al usuario. Los objetivos de este borrador no son un SLA contratado. [S3, p. 5]
4. **Investigar y restaurar:** consultar antecedentes, documentar diagnóstico y posibles soluciones temporales; escalar al especialista cuando exceda la capacidad de primera línea. La necesidad de recursos o decisiones superiores exige escalado jerárquico. [S2, pp. 7 y 11]
5. **Validar y cerrar cuando corresponda:** registrar la solución, comprobarla con el usuario y conservar conocimiento reutilizable. Si persiste el síntoma, continuar la investigación en lugar de cerrar por mera declaración técnica. [S3, p. 6]
6. **Revisar el servicio:** analizar recurrencia, pendientes y tiempos con datos reales; ajustar recursos y procedimientos sin inventar mejoras cuantitativas. [S3, pp. 6–7]

### Justificación de MantisBT

Se elige MantisBT por la orientación del ejercicio hacia fallas de un producto de software: proyectos, categorías, responsables, prioridad, severidad y seguimiento permiten estructurar ese trabajo. La documentación local lo identifica como herramienta disponible y ofrece referencias de administración y una plantilla Docker. [S4, sección 1; S5]

Para diez empleados se propone un único proyecto inicial, categorías por módulo y responsabilidades explícitas, evitando multiplicar proyectos o permisos administrativos. La utilidad esperada es reunir conversaciones y evidencias en un registro compartido; deberá comprobarse en el laboratorio, no darse por demostrada.

La elección no implica que MantisBT implemente automáticamente toda la gestión ITIL, una CMDB o los cálculos de cumplimiento de SLA. La organización debe definir criterios, responsabilidades y controles. No se realizó una comparación experimental con GLPI ni se atribuyen resultados de rendimiento a ninguna herramienta.

## Preparación propuesta: MantisBT sobre Docker

**Prueba funcional básica limitada a un laboratorio auxiliar aislado, separado de los diez casos académicos y sus dos reservas.** El procedimiento se basa en `src/mantis/leame.txt` y `src/mantis/docker-compose.yml`, adaptados de la plantilla original para publicación con contraseñas requeridas por variables de proceso, puerto local y volúmenes nativos Docker para SQL y configuración. No son copias inalteradas del material de cátedra. Desde la raíz del repositorio clonado, se propone usar Bash en WSL con Docker y Compose disponibles: [S5]

```bash
cd src/mantis
read -rsp 'Contraseña SQL administrativa: ' MYSQL_ROOT_PASSWORD; printf '\n'
read -rsp 'Contraseña SQL de la aplicación: ' MYSQL_PASSWORD; printf '\n'
export MYSQL_ROOT_PASSWORD MYSQL_PASSWORD
export MYSQL_DATABASE=bugtracker MYSQL_USER=mantisbt
docker compose --env-file /dev/null config --quiet
docker compose --env-file /dev/null up -d
docker compose --env-file /dev/null ps
```

No crear archivos de credenciales. La opción `--env-file /dev/null` evita la lectura implícita de `.env` y debe conservarse en cada comando. Las contraseñas se ingresan localmente con entrada oculta; no se publican. Las variables deben volver a proporcionarse en otra terminal o si se retiraron del entorno. Al terminar los comandos que las necesitan, usar `unset MYSQL_ROOT_PASSWORD MYSQL_PASSWORD MYSQL_DATABASE MYSQL_USER`. La comprobación silenciosa de configuración no prueba el funcionamiento de la aplicación.

Luego se propone abrir **http://localhost:8989**. Antes de ejecutar, verificar Docker y Compose disponibles, puerto 8989 libre y autorización para descargar las imágenes. Estos comandos son instrucciones para un clon nuevo, no una transcripción de la comparación ni una garantía de instalación en otro entorno.

La plantilla declara el servicio `mantisbt` con imagen `xlrl/mantisbt:latest` y el servicio `mysql` con imagen `mariadb:latest`. El puerto publicado es `127.0.0.1:8989:80`. El nombre del servicio de base de datos, `mysql`, será el host interno previsto para la configuración inicial. [S5]

En el asistente inicial se propone usar el host `mysql`, la base `MYSQL_DATABASE` (por defecto `bugtracker`), el usuario SQL `MYSQL_USER` (por defecto `mantisbt`) y la contraseña ingresada como `MYSQL_PASSWORD`; configurar la zona horaria de Argentina y la URL anterior conforme al instructivo. La identidad administrativa de MantisBT es distinta del usuario SQL: consultar privadamente las credenciales iniciales del proveedor y cambiarlas de inmediato. No se reproducen valores de autenticación ni se presupone que siempre haga falta acceso SQL root. Después se deberá comprobar acceso y registrar la versión efectiva de MantisBT y de la base. [S5]

| Persistencia declarada | Alcance previsto |
|---|---|
| `mantis_config:/var/www/html/config` | Volumen nativo Docker para configuración de la aplicación. |
| `./custom:/var/www/html/custom` | Bind opcional ignorado; no requerido para campos personalizados administrados desde MantisBT. |
| `mysql_data:/var/lib/mysql` | Volumen nativo Docker para datos persistentes de MariaDB. |

`mysql_data` y `mantis_config` se declaran al nivel superior de Compose con las mismas claves que la comparación aislada; sus nombres efectivos dependen del proyecto Compose. Docker administra estos volúmenes fuera del repositorio: no se versionan y persisten al recrear contenedores. Sólo `./custom` es relativo a `src/mantis/`. La persistencia no reemplaza un respaldo ni garantiza compatibilidad entre versiones. Recrear contenedores o cambiar variables de inicialización no rota las contraseñas de una base ya existente. No ejecutar `down -v` ni eliminar datos anteriores para resolver diferencias. Los datos y la configuración generada quedan fuera de la publicación. Las etiquetas `latest` no fijan versiones ni garantizan que otro entorno arranque correctamente. [S5; cautela de implementación]

La plantilla habilita funciones de administración mediante `MANTIS_ENABLE_ADMIN`. Se deberá revisar su alcance en la imagen instalada y restringir las funciones de instalación/administración una vez terminada la preparación. El mapeo preparado limita la publicación a loopback del anfitrión Docker; comprobar el acceso local efectivo en WSL y mantener el laboratorio aislado, sin exposición pública. No se propone desplegar esta plantilla sin revisión en producción.

**Evidencia técnica observada (laboratorio auxiliar, no actividades del TP):** en WSL Ubuntu, con Docker 29.5.3 y Compose 5.1.4, el bind SQL original de Windows en `/mnt/d` (v9fs) falló al crear un índice único en el esquema 191 porque MariaDB no pudo renombrar un archivo de tabla. La comparación `tp-gestion-tickets-native-validation`, con las mismas imágenes y volúmenes nativos para SQL y configuración, alcanzó esquema 213 y `login_page.php` HTTP 200. Los datos anteriores se preservaron, sin migración. El contraste motiva la persistencia elegida, pero no demuestra causalidad general ni éxito del TP completo.

El Compose final se aplicó a esa comparación nativa existente: `config --quiet`, `up -d` y `ps` fueron satisfactorios. Se comprobó acceso con la cuenta administrativa inicial del proveedor y se creó el proyecto privado auxiliar `VALIDACION-TP-Ventas` (ID 1), con **un único ticket sintético `SMOKE-TP-001` (ID 1), ajeno a INC-01–INC-10**. Se verificaron un adjunto TXT de 81 bytes con descarga coincidente, notas e historial, resolución (80) y cierre (90). Tras recrear **sólo el contenedor de la aplicación**, persistieron acceso, estado 90, bytes del adjunto y diez entradas de historial; no prueba recreación de la base ni recuperación de respaldos.

La vista sin autenticar redirigió al acceso sin exponer el resumen: esto **no valida reporte anónimo**. No se enviaron correos reales ni se crearon o accedieron archivos `.env`. La cuenta inicial quedó sin endurecer, exclusivamente para la prueba aislada en loopback: cambiar sus credenciales antes de cualquier uso futuro. No se publican contraseñas ni se afirma una versión exacta de MantisBT por la imagen usada.

**Evidencia pendiente:** diez registros académicos y su correspondencia INC-01–INC-10, matriz de roles, campos personalizados, informes, SMTP, reporte anónimo, restablecimiento de contraseña y evidencias de las ocho actividades. No se aportan capturas nuevas ni usuarios reales; la prueba auxiliar no sustituye los casos académicos ni sus dos reservas para defensa.

## Actividad práctica 1. Gestión de usuarios

**Procedimiento propuesto:** crear un proyecto privado denominado provisionalmente `Ventas e inventario`; cargar usuarios nominales y asignar el menor nivel de acceso que permita sus tareas. Comprobar los permisos con sesiones separadas, no únicamente desde administración. Los nombres de niveles corresponden a MantisBT y deben contrastarse con la versión y configuración efectiva. [S1, actividad 1; S4, sección 1]

| Personas | Función en la organización | Nivel propuesto en el proyecto | Responsabilidad |
|---|---|---|---|
| 1: ENC-01 | Encargado de la organización | MANAGER | Revisar prioridades, recursos y validación del servicio. |
| 1: MD-01 | Mesa de ayuda | UPDATER | Registrar, completar y seguir incidencias según permisos habilitados. |
| 2: DEV-01 y DEV-02 | Desarrollo y soporte técnico | DEVELOPER | Diagnosticar, atender y documentar soluciones. |
| 3: VEN-01 a VEN-03 | Operación de ventas | REPORTER | Informar fallas y aportar confirmación funcional. |
| 2: DEP-01 y DEP-02 | Depósito/inventario | REPORTER | Informar problemas de stock y verificar resultados. |
| 1: ADM-01 | Administración comercial | REPORTER | Reportar dificultades de comprobantes e informes. |
| **Total: 10 personas** | **Dotación humana del caso** | | |

La distribución es ilustrativa y debe validarse con la experiencia del proyecto. Si UPDATER no permite una acción de coordinación necesaria, se deberá revisar el umbral correspondiente o derivarla a MANAGER, sin otorgar ADMINISTRATOR indiscriminadamente.

Las identidades técnicas **no agregan empleados**: se prevé una cuenta administrativa separada, operada por una persona autorizada ya contabilizada; la conexión de base de datos es una identidad de servicio, no un usuario humano del proyecto. Una eventual cuenta compartida para la demostración anónima también será técnica y se definirá en la actividad 8.

**Evidencia pendiente:** tabla de diez personas y roles, permisos por proyecto, prueba de reporte por usuario final y prueba de que ese usuario no administra la configuración. Las capturas deberán ocultar datos personales y parámetros sensibles.

## Actividad práctica 2. Categorías por aspectos del producto

**Procedimiento propuesto:** dentro de la administración del proyecto, crear categorías estables por módulo. La categoría identifica el área funcional afectada; no sustituye prioridad, severidad ni estado. [S1, actividad 2; S2, p. 10; S4, sección 1]

| Categoría | Alcance | Responsable técnico inicial propuesto |
|---|---|---|
| Ventas | Registro, totales, descuentos y confirmación de operaciones. | DEV-01 |
| Inventario | Existencias, movimientos, ajustes y disponibilidad. | DEV-02 |
| Productos | Altas, modificaciones, códigos y precios de artículos. | DEV-02 |
| Clientes y comprobantes | Datos ficticios de compradores y emisión de documentos. | DEV-01 |
| Acceso y permisos | Sesiones y autorización sobre funciones del sistema del caso. | DEV-02 |
| Informes y consultas | Listados, filtros y resultados operativos. | DEV-01 |

Mesa de ayuda revisará la categoría al registrar; el responsable podrá reclasificar justificándolo en una nota. Una dependencia entre módulos no requiere duplicar automáticamente el incidente: se documentará el alcance y, si procede, la relación con otros registros. [S2, pp. 9–10]

**Evidencia pendiente:** categorías vinculadas al proyecto y filtro por categoría aplicado a registros futuros. No se afirma que las asignaciones automáticas estén configuradas.

## Actividad práctica 3. Ponderación y objetivos de atención

**Impacto** expresa el efecto sobre el negocio y usuarios; **urgencia**, cuánto puede demorarse la atención sin agravar el daño. La prioridad se obtiene de ambos y puede revisarse si cambia la situación, por ejemplo al habilitar una solución temporal. [S2, p. 6; S3, p. 3]

Se propone impacto alto para interrupción de ventas o integridad de stock; medio para un módulo o sector afectado con alternativa; bajo para afectación individual o limitada sin bloqueo esencial. La urgencia será alta si requiere intervención en el turno actual, media si admite el siguiente tramo de trabajo y baja si admite planificación acordada.

| Impacto / urgencia | Alta | Media | Baja |
|---|---|---|---|
| Alto | P1 → `urgent` | P2 → `high` | P2 → `high` |
| Medio | P2 → `high` | P3 → `normal` | P4 → `low` |
| Bajo | P3 → `normal` | P4 → `low` | P4 → `low` |

Esta es una **matriz local propuesta**, no una configuración automática ni una tabla oficial de MantisBT. Mesa de ayuda deberá justificar la selección y verificar las etiquetas disponibles en la versión instalada. `immediate` quedará fuera de esta matriz, salvo revisión explícita del criterio.

La **severidad** describe la naturaleza o gravedad técnica del defecto, mientras la **prioridad** ordena su atención en este contexto. Se propone utilizar valores nativos como `minor`, `major`, `crash` o `block` según el síntoma, sin una equivalencia automática con P1–P4. Un error menor puede requerir atención urgente si bloquea una operación crítica; no corresponde elevar su severidad sólo para adelantarlo. [S4, sección 1; criterio local]

| Prioridad | Primera respuesta propuesta | Objetivo de restauración o resolución propuesta |
|---|---|---|
| P1 / `urgent` | Dentro de 1 hora laboral. | Dentro de 4 horas laborales; escalar si resulta inviable. |
| P2 / `high` | Dentro de 4 horas laborales. | Dentro de 1 jornada laboral. |
| P3 / `normal` | Dentro de 1 jornada laboral. | Dentro de 3 jornadas laborales. |
| P4 / `low` | Dentro de 2 jornadas laborales. | Dentro de 5 jornadas laborales. |

**Son objetivos didácticos propuestos, no tiempos medidos ni SLA oficiales.** Se asume una jornada de ocho horas laborales, con calendario a acordar. Se distinguirá restauración temporal de corrección definitiva y se documentarán excepciones, sin borrar incumplimientos retrospectivamente. [S3, pp. 5–7; propuesta local]

Se definirá **T0** como el inicio futuro del laboratorio; cada plazo se calculará desde el registro de la incidencia, expresado relativamente a T0 y al calendario acordado, sin inventar fechas reales. La fecha prevista concreta y su implementación en MantisBT se desarrollarán en la actividad 5.

**Evidencia pendiente:** matriz aprobada para el caso, ejemplos de justificación, captura de prioridad y severidad independientes y registro de cualquier repriorización. No existen aún mediciones de atención o resolución.

## Actividad práctica 4. Registro propuesto de diez incidencias

**Catálogo ficticio para preparar el laboratorio:** se propone registrar los diez casos siguientes en MantisBT. Las etiquetas INC-01 a INC-10 son referencias de este borrador, **no identificadores reales de MantisBT**. Cada ficha deberá incorporar luego su ID real, versión del sistema y evidencia obtenida. Ninguno de los síntomas fue observado ni probado. [S1, actividad 4; S2, p. 9]

Se usará un entorno descartable con artículos, compradores y operaciones sintéticos. Los requisitos funcionales que siguen son supuestos a confirmar con el proyecto real; no se supone integración fiscal ni emisión de facturas ante un organismo externo.

### Tabla de clasificación y plazos previstos

Sea **Ti** el instante futuro de registro de cada caso, con Ti ≥ T0. Cada vencimiento se calculará desde su propio Ti, no desde una fecha común inventada. `hL` significa horas laborales; `jL`, jornadas de ocho horas conforme al calendario acordado. La columna objetivo corresponde a restauración o resolución; la primera respuesta conserva los objetivos de la actividad 3.

| Referencia | Módulo / categoría | Defecto propuesto | Prioridad | Severidad | Responsable | Objetivo desde Ti |
|---|---|---|---|---|---|---|
| INC-01 | Ventas | Confirmación bloqueada con dos artículos | P1 / urgent | block | DEV-01 | Ti + 4 hL |
| INC-02 | Inventario | Venta confirmada sin descontar existencias | P1 / urgent | major | DEV-02 | Ti + 4 hL |
| INC-03 | Ventas | Descuento aplicado dos veces | P2 / high | major | DEV-01 | Ti + 1 jL |
| INC-04 | Productos | Cambio de precio no persiste | P3 / normal | minor | DEV-02 | Ti + 3 jL |
| INC-05 | Clientes y comprobantes | Dirección modificada no aparece en vista previa | P3 / normal | minor | DEV-01 | Ti + 3 jL |
| INC-06 | Acceso y permisos | Perfil vendedor puede ejecutar ajuste de stock | P1 / urgent | major | DEV-02 | Ti + 4 hL |
| INC-07 | Informes y consultas | Filtro por vendedor incluye otras operaciones | P3 / normal | major | DEV-01 | Ti + 3 jL |
| INC-08 | Productos | Búsqueda exacta falla al conservar espacios finales | P4 / low | minor | DEV-02 | Ti + 5 jL |
| INC-09 | Inventario | Devolución reintentada duplica la reposición | P2 / high | major | DEV-02 | Ti + 1 jL |
| INC-10 | Informes y consultas | Exportación omite movimientos del último día | P3 / normal | minor | DEV-01 | Ti + 3 jL |

**Reserva de defensa:** únicamente INC-09 e INC-10 se prevén registrados y pendientes de resolución al iniciar la exposición. Para los otros ocho se propone practicar la atención y una eventual resolución validada; **no se afirma que estén resueltos o cerrados**. La reserva es una decisión didáctica, no una autorización para desatender incidentes reales. [S1, actividad 6]

### Fichas reproducibles propuestas

#### INC-01 — Confirmación de venta bloqueada

- **Precondiciones y pasos:** VEN-01 inicia sesión; existen dos artículos activos con stock suficiente. Agregar una unidad de cada uno a una venta y confirmar.
- **Esperado / observado hipotético:** se genera una sola venta con su detalle y movimientos; en la simulación, la confirmación presenta error y no permite finalizar.
- **Impacto alto, urgencia alta → P1:** bloquea ventas habituales del sector sin alternativa validada y exige atención en el turno actual.
- **Adjunto propuesto:** captura del error y registro técnico depurado, sin datos personales. **Aceptación:** confirmar ambos artículos genera una venta coherente; repetir la prueba no produce registros parciales ni duplicados.

#### INC-02 — Venta sin descuento de existencias

- **Precondiciones y pasos:** DEP-01 comprueba cinco unidades del artículo ficticio A; VEN-02 vende dos y DEP-01 consulta stock y movimientos.
- **Esperado / observado hipotético:** quedan tres unidades y un egreso de dos; en el caso simulado, la venta figura confirmada pero permanecen cinco unidades sin movimiento asociado.
- **Impacto alto, urgencia alta → P1:** compromete integridad de inventario y disponibilidad ofrecida durante el turno; continuar vendiendo agrava la inconsistencia.
- **Adjunto propuesto:** comparación antes/después y detalle de venta sintético. **Aceptación:** el saldo y el movimiento concuerdan con la venta; un reintento no vuelve a descontar la operación.

#### INC-03 — Descuento duplicado

- **Precondiciones y pasos:** VEN-03 prepara una venta de un artículo de precio 100 unidades monetarias, aplica descuento único de 10 % y revisa el total antes de confirmar.
- **Esperado / observado hipotético:** total 90; en la hipótesis, el sistema aplica nuevamente el descuento y muestra 81.
- **Impacto medio, urgencia alta → P2:** afecta ventas con descuentos; puede venderse sin esa modalidad con autorización, pero el sector necesita recuperarla en el turno para evitar cobros incorrectos.
- **Adjunto propuesto:** captura de cálculo y detalle de entradas. **Aceptación:** total 90 y descuento único visible; guardar o reabrir la venta no modifica el importe ni reaplica el beneficio.

#### INC-04 — Precio actualizado que no persiste

- **Precondiciones y pasos:** DEP-02, con permiso de edición previsto en el sistema del caso, abre un artículo de prueba, cambia su precio de 100 a 120, guarda y vuelve a consultarlo.
- **Esperado / observado hipotético:** se conserva 120; en la simulación reaparece 100 aunque se informó guardado satisfactorio.
- **Impacto medio, urgencia media → P3:** dificulta la actualización del catálogo; se puede suspender la venta del artículo afectado y programar la atención en el siguiente tramo de trabajo.
- **Adjunto propuesto:** secuencia de pantallas de edición y consulta. **Aceptación:** el nuevo valor persiste tras recargar y abrir otra sesión autorizada; una venta nueva toma el precio vigente.

#### INC-05 — Dirección desactualizada en comprobante interno

- **Precondiciones y pasos:** ADM-01 modifica la dirección ficticia de un comprador y genera la vista previa de un nuevo comprobante interno vinculado a ese comprador.
- **Esperado / observado hipotético:** el documento nuevo usa la dirección vigente; en la hipótesis conserva la anterior pese a que la ficha muestra la actualización.
- **Impacto bajo, urgencia alta → P3:** afecta un despacho individual, no toda la operación; debe revisarse durante el turno para evitar una entrega errónea. No involucra validación fiscal externa.
- **Adjunto propuesto:** ficha y vista previa con datos sintéticos. **Aceptación:** los documentos nuevos muestran la dirección correcta; el tratamiento de documentos históricos respeta el requisito del proyecto, sin alterarlos indiscriminadamente.

#### INC-06 — Ajuste de inventario sin autorización

- **Precondiciones y pasos:** en el sistema de ventas, no en MantisBT, se acuerda que el vendedor no ajusta stock. VEN-01 intenta acceder a esa función y confirmar un ajuste sobre un artículo descartable.
- **Esperado / observado hipotético:** se rechaza la operación sin modificar datos; en la simulación se permite guardar el ajuste con perfil vendedor.
- **Impacto alto, urgencia alta → P1:** expone la integridad del stock a cambios no autorizados; requiere contención en el turno, aunque no se haya producido daño real.
- **Adjunto propuesto:** evidencia del perfil y resultado, con información sensible ocultada. **Aceptación:** rechazo tanto desde interfaz como en la operación subyacente; un perfil autorizado conserva la función y queda trazabilidad del ajuste permitido.

#### INC-07 — Informe no respeta vendedor seleccionado

- **Precondiciones y pasos:** crear ventas sintéticas de VEN-01 y VEN-02; ENC-01 filtra el informe exclusivamente por VEN-01 y contrasta sus filas con el detalle de origen.
- **Esperado / observado hipotético:** aparecen sólo sus ventas y total correspondiente; en la hipótesis se incluyen también las de VEN-02.
- **Impacto medio, urgencia media → P3:** distorsiona el control del sector; se dispone del detalle individual como alternativa y admite el siguiente tramo de trabajo.
- **Adjunto propuesto:** filtro, listado y cálculo manual con los mismos datos. **Aceptación:** filas y total coinciden con el vendedor elegido, incluyendo pruebas sin resultados y sin filtro.

#### INC-08 — Espacios finales impiden buscar un producto

- **Precondiciones y pasos:** en un catálogo sintético con código único A-01, pegar `A-01 ` en la búsqueda exacta; repetir sin el espacio. Se supone que el requisito exige normalizar espacios de entrada.
- **Esperado / observado hipotético:** ambas entradas recuperan el artículo; en la simulación, sólo funciona la segunda.
- **Impacto bajo, urgencia baja → P4:** incomodidad individual con alternativa simple, sin bloqueo de venta ni alteración de datos; permite planificación acordada.
- **Adjunto propuesto:** entradas delimitadas para hacer visible el espacio y resultados. **Aceptación:** la normalización satisface el requisito, sin confundir códigos realmente distintos ni devolver artículos incorrectos.

#### INC-09 — Devolución reintentada duplica stock — reserva abierta

- **Precondiciones y pasos:** DEP-01 dispone de una venta sintética de dos unidades y devolución autorizada; confirmar devolución y repetir el envío por un supuesto reintento tras demora.
- **Esperado / observado hipotético:** la misma devolución repone dos unidades una sola vez; en la hipótesis ambos envíos se aceptan y reponen cuatro.
- **Impacto alto, urgencia media → P2:** amenaza la integridad de inventario; en el laboratorio se supone suspendido el reintento y control manual autorizado, contención que permite atención en el siguiente tramo. Sin esa contención deberá reevaluarse la urgencia.
- **Adjunto propuesto:** identificador sintético de devolución, secuencia de solicitudes depurada y saldos. **Aceptación:** repetir la misma operación no duplica movimientos; devoluciones diferentes válidas siguen admitidas. Se dejará registrado y sin resolver para demostrar investigación y seguimiento en la defensa.

#### INC-10 — Exportación excluye el último día — reserva abierta

- **Precondiciones y pasos:** ADM-01 prepara movimientos sintéticos al inicio y final de dos días consecutivos; selecciona ambos días completos y exporta el informe.
- **Esperado / observado hipotético:** se incluyen todos los movimientos del intervalo acordado; en la simulación se omiten los posteriores a las 00:00 del último día.
- **Impacto medio, urgencia media → P3:** afecta la conciliación administrativa; el detalle en pantalla sirve como alternativa y admite el siguiente tramo de trabajo.
- **Adjunto propuesto:** filtro, archivo exportado y conjunto esperado con horas ficticias. **Aceptación:** exportación y consulta coinciden para límites inclusivos y zona horaria acordados, sin sumar operaciones del día siguiente. Se dejará registrado y sin resolver para demostrar solicitud de información y validación de límites durante la defensa.

**Procedimiento y evidencia pendiente:** para cada ficha se propone buscar duplicados, registrar categoría, resumen, descripción, pasos, resultado esperado, síntoma simulado y entorno; adjuntar sólo archivos depurados. Se completará una correspondencia entre referencia de borrador e ID real, junto con captura del listado de diez registros y vista de cada ficha. No se reemplazará evidencia por capturas ficticias. [S2, p. 9; S3, p. 5]

## Actividad práctica 5. Clasificación, responsables y fechas previstas

Se propone que MD-01 revise integridad y duplicados y eleve a ENC-01 las decisiones que excedan sus permisos. El responsable técnico será el indicado en el catálogo, sin que ello implique que ya haya aceptado o resuelto el caso. ENC-01 revisará capacidad disponible: varios objetivos simultáneos pueden exigir escalado o renegociación explícita, no una promesa de cumplimiento automático. [S1, actividad 5; S2, pp. 7 y 10]

1. Confirmar categoría e impacto/urgencia con el informante; trasladar la prioridad resultante a MantisBT y elegir severidad por el síntoma. Registrar la razón en una nota.
2. Asignar al desarrollador correspondiente y verificar que pueda consultar y actualizar el registro; derivar a MANAGER las acciones que UPDATER no tenga autorizadas.
3. Registrar Ti y calcular la fecha prevista desde los objetivos del catálogo, usando el calendario laboral acordado. Separar primera respuesta, restauración y eventual corrección definitiva.
4. Revisar periódicamente pendientes, cambios de alcance y bloqueos; conservar motivo y fecha de cada reasignación o revisión del objetivo, además del compromiso original.

### Habilitación propuesta de vencimientos

Los valores de la configuración oficial `master` consultada durante la preparación son `due_date_view_threshold = NOBODY`, `due_date_update_threshold = NOBODY` y `due_date_default = ''`. Por ello **no debe suponerse que la fecha de vencimiento nativa esté visible o editable por defecto**. Estos valores deben comprobarse en la versión instalada. [D5, opciones `due_date_*`]

Se propone que administración habilite, con el alcance más reducido permitido por esa versión, **`$g_due_date_view_threshold = REPORTER;`** y **`$g_due_date_update_threshold = DEVELOPER;`**. Así los informantes podrían consultar el compromiso y desarrollo o niveles superiores actualizarlo. MD-01 solicitaría el cambio al responsable, sin recibir permisos administrativos. Antes de usarlo, comprobar la visualización en reporte, edición, detalle y filtros según la interfaz efectiva. Esta es una configuración futura, no aplicada. [D5]

**Alternativa si el campo nativo no resulta viable:** crear un campo personalizado de tipo fecha denominado `Fecha prevista`, vincularlo al proyecto y definir permisos de lectura/escritura y visibilidad en los formularios pertinentes. Registrar en una nota la hora límite y el calendario si el campo sólo conserva fecha; una fecha sola no representa correctamente el objetivo de cuatro horas laborales. No mantener dos fuentes de vencimiento contradictorias. [D1, §7.2; propuesta local]

El registro de horas trabajadas o **time tracking no sustituye las fechas previstas**: esfuerzo técnico, tiempo transcurrido y compromiso de servicio son conceptos diferentes. La disponibilidad y configuración de esa función se validarán aparte. [D6, Time Tracking]

**Evidencia pendiente:** detalle con categoría, responsable y objetivo; comprobación de permisos de lectura/escritura del vencimiento o del campo alternativo; comparación entre Ti y fecha calculada. Los registros reservados también tendrán responsable y plazo: reservarlos no equivale a dejarlos sin gestión.

## Actividad práctica 6. Progreso, documentación y resolución propuesta

El objetivo es simular el trabajo hasta una resolución comprobable, dejando dos casos para la defensa. **No se presenta una secuencia universal obligatoria de estados.** El flujo dependerá de permisos, transiciones habilitadas y necesidad real de cada caso. Los nombres siguientes deben verificarse en la versión instalada. [S1, actividad 6; S4, sección 1]

| Situación propuesta | Estado o acción posible | Registro que se espera conservar |
|---|---|---|
| Reporte inicial | `new` | Descripción, entorno, pasos y adjuntos iniciales. |
| Falta información | `feedback` y pedido concreto | Pregunta, respuesta del informante y evidencia adicional. |
| Recepción y verificación | `acknowledged` o `confirmed`, si corresponde | Quién recibió el caso y cómo verificó el síntoma. |
| Responsable trabajando | `assigned` | Responsable, diagnóstico, contención y objetivo actualizado. |
| Solución disponible | `resolved` y resolución apropiada | Explicación, versión corregida o alternativa y prueba técnica. |
| Aceptación satisfactoria | `closed`, por rol autorizado | Confirmación del usuario y evidencia de aceptación. |
| Síntoma persiste o reaparece | Reapertura según flujo configurado | Motivo, nueva reproducción y reasignación cuando corresponda. |

Un caso puede pasar de nuevo a asignado sin recorrer todos los estados intermedios. `resolved` indica una solución registrada, no necesariamente aceptación final; se propone cerrar después de verificarla con el usuario. Si hay una solución temporal, se explicitará qué servicio restaura y qué trabajo queda pendiente. Las transiciones y permisos efectivos se documentarán, no se inferirán del nombre del rol. [S3, pp. 3 y 6; criterio de simulación]

### Ejemplos de recorrido y registros a preparar

- Para el bloqueo de confirmación se propone reproducción por mesa de ayuda, asignación a DEV-01, nota de diagnóstico, adjunto técnico depurado y prueba funcional con VEN-01; sólo si satisface la aceptación se propondrá resolución y cierre.
- Para el comprobante con dirección anterior se propone pedir aclaración en `feedback`: «¿Ocurre en un documento nuevo o en uno histórico? Adjuntar ejemplo sintético». La respuesta determinará si existe defecto o diferencia respecto del requisito.
- Para el filtro por vendedor se propone ensayar una validación negativa: si después de una supuesta corrección sigue incluyendo otro vendedor, documentar el resultado y reabrir conforme a los permisos disponibles, sin simular un cierre exitoso.
- Para la falla de permisos se propone registrar contención y escalar por impacto sobre integridad; no publicar detalles explotables ni datos reales en adjuntos o comentarios visibles para usuarios no autorizados.

Cada actualización deberá explicar acción, resultado y siguiente paso. Un comentario propuesto sería: «Se solicita confirmar la versión y repetir con el artículo sintético A; aún no hay evidencia suficiente para dar el caso por resuelto». Los adjuntos deberán tener nombre descriptivo y contexto; no bastará subir un archivo sin explicar qué demuestra. [S2, p. 9; S3, p. 6]

Se propone revisar **historial** de cambios de estado, responsable y prioridad para reconstruir el trabajo. Usar **relaciones** sólo cuando haya evidencia: vincular la venta sin descuento de stock con el problema de devoluciones como casos relacionados si se demuestra un componente común, no declararlos duplicados por compartir categoría. Si aparece un duplicado real, conservar referencia al principal y explicar la decisión. Probar otras opciones pertinentes de seguimiento según versión, sin forzar funciones ajenas al caso. [S2, pp. 9–11]

**Plan de reserva:** los dos casos marcados en el catálogo se registrarán con sus datos y se mantendrán abiertos, por ejemplo asignado el de devoluciones y en solicitud de información el de exportación. No se los marcará resueltos antes de la defensa. Los restantes ocho tendrán un recorrido previsto hasta validación, pero su resultado quedará pendiente de ejecución y evidencia; no se informa «ocho resueltos».

**Evidencia pendiente:** comentarios y adjuntos reales del laboratorio, historial, relación justificada si corresponde, una solicitud de información, una reapertura controlada y aceptación del informante cuando sea viable. Para cada registro se conservarán ID real y estado observado; una tabla final distinguirá claramente resultados efectivos de acciones aún propuestas. [S3, p. 6]

## Actividad práctica 7. Informes y evaluación del servicio

Se propone utilizar y documentar las variantes efectivamente disponibles en la versión instalada, con el mismo proyecto, período y permisos para poder compararlas. Un informe sólo será evidencia después de generarlo con datos del laboratorio. No se presentan estadísticas ni gráficos de resultados inexistentes. [S1, actividad 7; S3, pp. 6–7]

| Variante propuesta | Uso y comprobación | Límite a documentar |
|---|---|---|
| Resumen nativo / `Summary`, si está disponible | Revisar distribuciones que ofrezca por estado, categoría, prioridad o responsable; contrastar con listado. | Registrar versión, permisos y filtros efectivos; no asumir que todas las combinaciones existen. |
| Listado con filtros | Consultar pendientes, prioridades altas, categoría o responsable; guardar filtros si está permitido. | Explicitar proyecto, período, criterio de estado y alcance visible al usuario. |
| Vista de impresión / informe imprimible | Preparar una selección legible para revisión del encargado. | Verificar campos incluidos; imprimir no equivale a calcular indicadores de servicio. |
| Exportación CSV | Exportar selección y campos disponibles para análisis externo reproducible. | Comprobar codificación, separadores, fechas, campos personalizados y ausencia de datos sensibles. |

Los permisos de impresión y exportación son configurables: en la configuración `master` de referencia se documentan `print_reports_threshold` y `export_issues_threshold`, con valores UPDATER y VIEWER respectivamente. Esto no garantiza permisos efectivos ni formatos idénticos en la instalación futura. [D7, opciones de informes y exportación]

### Indicadores externos propuestos

Para una ventana de análisis W se propone fijar fecha de corte, zona horaria, calendario laboral, proyecto y población de tickets únicos. Excluir duplicados de los conteos de carga cuando se contabiliza el principal, dejando explícita esa decisión. Las reservas de defensa se identificarán para no interpretarlas como comportamiento normal del servicio.

| Indicador | Fórmula propuesta y alcance |
|---|---|
| Pendientes al corte | Cantidad de tickets registrados hasta el corte que no están resueltos ni cerrados según el criterio declarado. Separar ambos estados en el informe. |
| Tiempo medio de resolución | Σ(t_resolución − t_registro) / N de casos con resolución válida; declarar primera resolución o última resolución estable tras reapertura. |
| Tiempo medio de cierre | Σ(t_cierre − t_registro) / N de casos efectivamente cerrados; incluye la espera de aceptación, por lo que no equivale al tiempo de resolución. |
| Cumplimiento del hito acordado | 100 × casos que alcanzaron el hito comprometido dentro del plazo / casos evaluables. Usar vencimientos dentro de W y definir por ticket si el compromiso corresponde a restauración o resolución; comparar su marca temporal verificada con el vencimiento original. Sin alcanzar ese hito al vencer el plazo, el caso incumple; los aún no vencidos quedan pendientes de evaluación. |
| Primera respuesta | t_primera_respuesta_humana − t_registro, por caso; no considerar un correo automático como atención humana. |
| Resolución en primera línea | 100 × casos resueltos por mesa de ayuda sin escalado / casos resueltos evaluados; requiere evidencia del responsable y escalados. |
| Reaperturas | 100 × tickets con al menos una reapertura / tickets que alcanzaron resolución en la cohorte acordada; declarar ventana de observación. |

Un ticket abierto que restauró el servicio dentro del plazo mediante una solución temporal documentada puede cumplir un compromiso de **restauración**, aunque siga pendiente la corrección definitiva. Si se comprometió **resolución**, esa restauración por sí sola no demuestra cumplimiento. Se informarán por separado ambos hitos y sus marcas temporales verificables.

Los abiertos **no tienen tiempo de resolución cero**: se informará su antigüedad al corte por separado. Sin datos evaluables o con denominador cero se indicará «no calculable». Los objetivos locales no se llamarán SLA oficial ni medido contractualmente. Se preservará el compromiso original y la justificación de revisiones para no mejorar artificialmente el indicador.

Tiempo calendario, tiempo laboral y **esfuerzo registrado** son medidas distintas. El esfuerzo es la suma de horas de trabajo informadas por técnicos; no puede deducirse de la diferencia entre apertura y cierre. La función opcional de time tracking no proporciona por sí sola todo el análisis anterior. [D6; S3, pp. 6–7]

No se supone que el CSV incluya todos los cambios de estado, escalados o fechas históricas. Se comprobarán las columnas exportadas y se obtendrán las marcas temporales faltantes del historial y de registros verificables; si no están disponibles, no se calculará esa métrica. La hoja de cálculo externa conservará fórmulas, criterios de inclusión y trazabilidad al ID real.

**Conclusiones condicionales:** si predominan pendientes de inventario, revisar validaciones y capacidad del responsable; si hay muchas reaperturas, reforzar pruebas y aceptación; si se demora el cierre pero no la resolución, revisar coordinación con informantes; si se incumplen objetivos, analizar carga, estimación y escalado antes de atribuirlo a personas. Una muestra didáctica pequeña no demuestra causalidad ni mejora sostenida.

**Evidencia pendiente:** captura de cada variante disponible, filtros y alcance, exportación depurada, tabla externa con fuentes y fórmulas y una conclusión limitada a los datos realmente obtenidos. Registrar también variantes ausentes o bloqueadas por permisos.

## Actividad práctica 8. Personalización y comunicación

Las cuatro extensiones siguientes son procedimientos propuestos. Los documentos oficiales de referencia corresponden a `master`, no a una versión fijada por la imagen `latest`; se deberá identificar la versión efectiva y comprobar nombres de opciones, permisos y comportamiento antes de aplicar cambios. [S1, actividad 8; S5; D1–D7]

### 8.1. Personalización de formularios

1. Proponer campos `Impacto` y `Urgencia` con valores alto/medio/bajo y ayuda breve; conservar la prioridad y severidad nativas para no confundirlas con esos campos.
2. Desde administración, definir los campos personalizados del sistema y sus tipos, valores y permisos. El gestor vinculará los campos necesarios al proyecto. [D1, §7.2 Custom Fields]
3. Incorporar `Entorno/versión del sistema` y, sólo si se adopta la alternativa de la actividad 5, `Fecha prevista` de tipo fecha. Evitar duplicar el vencimiento nativo.
4. Ajustar visibilidad y obligatoriedad en reporte, actualización o resolución según lo admita la versión; comprobar que un informante pueda completar lo que se le exige sin editar campos reservados al soporte.
5. Probar un registro completo y otro con un dato obligatorio omitido; verificar validación, lectura por los roles previstos y disponibilidad en filtros/exportación cuando esté soportada.

**Aceptación y evidencia pendiente:** formulario adaptado al caso, campos vinculados al proyecto, prueba positiva y negativa y exportación de ejemplo. Los campos no calcularán automáticamente la matriz a menos que se implemente y verifique un mecanismo adicional; aquí se propone selección justificada por mesa de ayuda.

### 8.2. Notificaciones de eventos por correo saliente SMTP

1. Acordar un servidor SMTP autorizado y destinatarios de prueba. Usar marcadores como `<HOST_SMTP>`, `<PUERTO_SMTP>`, `<MODO_TLS>`, `<USUARIO_SMTP>` y `<REMITENTE_AUTORIZADO>` hasta disponer de la configuración aprobada; no suponer un proveedor real.
2. Verificar en la versión instalada el método SMTP y configurar `phpMailer_method`, `smtp_host`, `smtp_port`, `smtp_connection_mode` y la autenticación requerida mediante las opciones de configuración admitidas. El `master` consultado emplea `PHPMAILER_METHOD_SMTP`; no copiar constantes sin comprobar versión. [D2, §5.8; D5]
3. Comprobar conexión saliente y política del servidor desde el entorno de la aplicación. `localhost` dentro del contenedor se refiere al propio contenedor, no automáticamente al equipo anfitrión; no existe un SMTP disponible por el solo hecho de usar Docker.
4. Habilitar notificaciones y seleccionar eventos y destinatarios pertinentes: informante, asignado y monitores según permisos y preferencias. Verificar direcciones válidas y ajustes individuales; `email_receive_own` está desactivado por defecto en la referencia consultada, por lo que no debe usarse sólo una acción propia como prueba. [D2]
5. Con dos participantes de prueba, proponer una asignación y un comentario que deban notificar al otro. Comprobar recepción, asunto, vínculo al ticket y ausencia de contenido sensible; revisar registros depurados si falla.

**Aceptación y evidencia pendiente:** evento y destinatario esperados frente al mensaje recibido, hora y enlace accesible. Ocultar direcciones personales y parámetros sensibles en capturas. El correo saliente de notificación no implementa automáticamente creación de tickets desde correos entrantes. No se enviaron mensajes durante la redacción.

### 8.3. Reporte anónimo de tickets en un laboratorio aislado

La consigna requiere poder **reportar**, no sólo consultar anónimamente. Una cuenta VIEWER de sólo lectura no cumple ese objetivo. La configuración de referencia requiere `allow_anonymous_login` y una cuenta existente indicada por `anonymous_account`; el permiso efectivo de reporte depende de `report_bug_threshold` y del acceso al proyecto. [D4, Miscellaneous / anonymous login]

1. Preparar un proyecto de demostración sin datos sensibles, aislado del proyecto con información confidencial. Definir una cuenta técnica compartida exclusiva para esta prueba, sin contarla como empleado.
2. Otorgarle únicamente acceso al proyecto de demostración y el nivel mínimo que alcance `report_bug_threshold`, normalmente REPORTER. Comprobar el umbral real; no otorgar administración ni acceso global amplio para resolver restricciones.
3. Proponer la habilitación de `allow_anonymous_login` y la selección de esa cuenta en `anonymous_account`, con alcance y controles compatibles con la versión. Mantener la instalación sin exposición pública a Internet.
4. Desde una sesión sin autenticación personal, comprobar que se puede abrir el formulario, enviar un reporte sintético y volver a localizarlo según la política de visibilidad acordada. Si el acceso al proyecto privado elegido no funciona con este modo, revisar una configuración de laboratorio compatible, nunca abrir el proyecto confidencial como atajo.
5. Ejecutar controles negativos: no acceder a otros proyectos ni tickets privados/confidenciales, no administrar usuarios, no modificar la cuenta compartida ni resolver registros sin autorización. Revisar también la visibilidad de adjuntos y listados.

Todos los reportes anónimos comparten identidad: no proporcionan atribución individual ni seguimiento personalizado fiable. La referencia indica que la cuenta anónima no recibe notificaciones estándar ni usa el monitoreo habitual; no prometer correos personales a quien reporta. [D4]

**Aceptación y evidencia pendiente:** envío real de un reporte sin autenticación nominal y pruebas negativas de aislamiento. Una captura de consulta anónima por sí sola no demuestra reporte. Si no puede garantizarse aislamiento, no habilitar la función fuera del entorno descartable y documentar la limitación.

### 8.4. Restablecimiento de contraseña mediante correo

1. Usar una cuenta nominal de prueba con dirección verificada y una instalación cuya URL base sea accesible desde el navegador del destinatario. `localhost` no identifica el servidor correcto desde otro equipo.
2. Verificar correo operativo, notificaciones habilitadas y opciones `send_reset_password` y `lost_password_feature` según la versión. Desactivar notificaciones impide el restablecimiento por correo en la referencia consultada. [D2; D3, §5.7 Signup and Lost Password]
3. Desde la pantalla de acceso, iniciar la recuperación con los datos que solicite la versión; comprobar llegada del mensaje y que el enlace apunte a la instalación prevista.
4. Seguir el enlace y establecer una contraseña nueva desde el formulario. Comprobar acceso con ella, rechazo de la anterior y comportamiento del enlace ya utilizado conforme a la versión.
5. Si falla, revisar dirección, configuración saliente, URL y registros depurados antes de repetir. No sustituir esta validación por un cambio manual de contraseña de otro usuario.

**Aceptación y evidencia pendiente:** solicitud, recepción del mensaje, formulario de restablecimiento y prueba de acceso, sin mostrar enlaces de recuperación utilizables ni contraseñas. Nunca registrar ni enviar contraseñas en texto plano como evidencia del procedimiento.

## Defensa: exactamente dos escenarios reservados

Los siguientes guiones se proponen para registros que deberán llegar **abiertos y sin resolución** al inicio de la defensa. Aún no existen IDs reales, capturas ni resultados. Mantener sus objetivos desde Ti aunque la reserva didáctica deba explicarse al evaluar tiempos.

### Defensa 1 — INC-09: devolución duplicada por reintento

- **Preparación:** disponer del registro asignado a DEV-02, conjunto sintético y evidencia inicial del intento duplicado; verificar su clasificación alto/medio → P2 y objetivo Ti + 1 jL. No marcarlo resuelto previamente.
- **Guion:** mostrar ID real y relato; reproducir de forma controlada la devolución y su reintento sobre datos descartables; comparar saldo esperado con resultado real. Si no se reproduce, registrar esa limitación sin fingirla.
- **Gestión a demostrar:** añadir comentario de investigación, adjuntar comparación depurada, explicar la contención de reintentos y revisar urgencia si deja de ser viable; identificar prueba de idempotencia como condición de aceptación.
- **Evidencia esperada:** historial con responsable, prioridad y plazo; movimientos vinculados a una devolución; nota de siguiente paso. Sólo una corrección disponible y validada permitiría resolver durante la defensa; el guion no promete que exista.

### Defensa 2 — INC-10: límite final de un rango exportado

- **Preparación:** disponer del registro sin resolver, eventualmente en `feedback`, ADM-01 como informante y DEV-01 responsable. Confirmar medio/medio → P3 y objetivo Ti + 3 jL.
- **Guion:** pedir fechas, horas y zona horaria; incorporar respuesta con movimientos sintéticos en los límites; comparar consulta y exportación para ambos días completos, incluyendo el final del último día y excluyendo el siguiente.
- **Gestión a demostrar:** registrar la información recibida y el resultado de contraste, actualizar estado sólo si corresponde y explicar por qué exportación y pantalla deben usar el mismo criterio temporal.
- **Evidencia esperada:** pregunta y respuesta, filtro, archivo depurado, listado esperado e historial. Sin corrección validada seguirá pendiente; una diferencia real tras una supuesta solución justificaría continuar o reabrir según el flujo, no cerrar automáticamente.

## Matriz de evidencia por completar

Todos los elementos académicos de esta matriz están **pendientes de ejecución y captura**. La prueba básica auxiliar no los completa. Los IDs de INC-01–INC-10 se incorporarán después de su registro; correos, pruebas e informes deberán provenir del laboratorio, no de imágenes inventadas.

| Actividad | Evidencia mínima propuesta | Comprobación |
|---|---|---|
| 1. Usuarios | Distribución de diez personas y accesos por proyecto. | Roles efectivos y cuenta administrativa separada. |
| 2. Categorías | Configuración y listado filtrado. | Correspondencia con módulos del caso. |
| 3. Ponderación | Matriz y dos justificaciones contrastantes. | Prioridad separada de severidad. |
| 4. Registro | Diez fichas e IDs reales asociados a referencias. | Pasos, datos ficticios y resultados claramente identificados. |
| 5. Gestión | Responsables, vencimientos y prueba de permisos. | Fechas calculadas desde cada Ti y calendario declarado. |
| 6. Progreso | Comentarios, adjuntos, historial y aceptación o bloqueo. | Dos reservas abiertas; ningún cierre sin sustento. |
| 7. Informes | Variantes disponibles y análisis externo reproducible. | Filtros, fuentes, denominadores y limitaciones explícitos. |
| 8. Extensiones | Registro conjunto de configuración y pruebas. | Las cuatro funciones se verifican separadamente. |
| 8.1. Formularios | Campos, vínculo al proyecto y validaciones. | Prueba positiva y negativa con roles pertinentes. |
| 8.2. SMTP | Evento y mensaje de prueba recibido. | Destinatario, preferencias y enlace correctos. |
| 8.3. Anónimo | Reporte sin identidad nominal y controles negativos. | Reporte permitido; proyectos confidenciales inaccesibles. |
| 8.4. Recuperación | Solicitud, mensaje y acceso posterior. | Contraseña anterior rechazada, sin divulgar datos sensibles. |

## Dudas a resolver antes de la entrega

- **Fecha:** confirmar año y fecha efectiva de «Domingo 18 de Noviembre»; no deducirlos del archivo de 2026.
- **Equipo y entrega:** confirmar integrantes dentro del máximo de dos, formato solicitado, capturas necesarias y modalidad de presentación del entorno.
- **Reservas:** consultar si los dos casos de defensa deben estar incluidos en los diez y qué estado espera la cátedra; aquí se supone que sí y se conservan abiertos.
- **Proyecto:** confirmar experiencia real de los estudiantes y ajustar requisitos, datos y ejemplos al desarrollo conocido.
- **Versiones y permisos:** identificar versión instalada, compatibilidad entre imágenes y configuración de flujo, formularios, fechas e informes disponibles.
- **Correo y seguridad:** acordar servidor SMTP, remitente autorizado, destinatarios de prueba y URL accesible; confirmar aislamiento de reporte anónimo y tratamiento de evidencias.
- **Compromisos:** acordar calendario, interpretación de restauración frente a resolución y forma de documentar las reservas en indicadores didácticos.

## Conceptos para estudiar y defender

| Tema | Pregunta orientadora |
|---|---|
| Mantenimiento | ¿Qué propósito distingue correctivo, adaptativo, perfectivo y preventivo? |
| Incidente, problema y cambio | ¿Qué restaura servicio, qué investiga causas y qué autoriza modificaciones? |
| Base de conocimiento y escalado | ¿Qué antecedente permite resolver y cuándo corresponde pedir apoyo funcional o jerárquico? |
| Impacto, urgencia, severidad y prioridad | ¿Cómo se justifica el orden de atención sin confundir gravedad técnica y necesidad comercial? |
| Roles y ciclo de vida | ¿Quién puede actuar y qué evidencia separa resolución, aceptación, cierre y reapertura? |
| Métricas | ¿Qué población, marcas temporales y denominador sostienen cada conclusión? |
| Docker y persistencia | ¿Qué persiste en cada montaje y por qué persistencia no equivale a respaldo? |
| SMTP y acceso | ¿Qué diferencia hay entre notificar, recibir tickets por correo y recuperar una contraseña? |
| Reporte anónimo | ¿Por qué consultar no equivale a reportar y qué riesgos introduce una identidad compartida? |

## Referencias y alcance de las fuentes

Las rutas S1–S4 corresponden al espacio de trabajo original: esos materiales no se redistribuyen en el repositorio público. Las páginas de los PDF son **páginas físicas**, contadas desde la primera. Los materiales locales se leyeron para este borrador. Los contenidos oficiales externos identificados a continuación fueron verificados durante la preparación por la coordinación del trabajo y se utilizan mediante su mapa de evidencia; no se afirma una nueva consulta web durante esta redacción. Las decisiones del caso y objetivos temporales se identifican como propuestas, no como datos de esas fuentes.

- **S1.** `consigna/practica_-_Gestion_de_ticket_2026.pdf`, p. 1: requisitos, puntos teóricos 1–2 y actividades 1–8; fecha literal sin año. Autoridad para el alcance de la entrega.
- **S2.** `material/Gestion_de_Incidentes.pdf`, 11 páginas: p. 2 objetivos; p. 3 primera línea; p. 4 incidente/cambio; p. 6 impacto y urgencia; p. 7 escalado; p. 8 ticket; p. 9 registro; p. 10 clasificación; p. 11 análisis y resolución.
- **S3.** `material/Gestion_de_Incidentes_ITIL.pdf`, 7 páginas: p. 1 restauración del servicio y distinción de problemas/cambios; p. 3 clasificación y escalado; p. 5 registro y responsables; p. 6 actualización, aceptación, conocimiento e informes; pp. 6–7 métricas. No se atribuye a este material una edición actual específica de ITIL.
- **S4.** `material/DOCUMENTACION_OFICIAL.md`, sección 1 «MantisBT»: enlaces oficiales, roles y orientación de funciones. Es una guía de referencias, no evidencia de configuración aplicada ni flujo universal obligatorio.
- **S5.** `src/mantis/leame.txt`, procedimiento de instalación, y `src/mantis/docker-compose.yml`, servicios, imágenes, puerto y montajes. Adaptación de la plantilla original, no copia inalterada: contraseñas requeridas por variables de proceso, publicación en loopback y volúmenes nativos `mysql_data`/`mantis_config`, con bind opcional `./custom` ignorado y no requerido para campos personalizados. Se adoptó esta persistencia tras la comparación aislada con las mismas imágenes que alcanzó esquema 213. El Compose final aplicado a esa comparación confirmó acceso y seguimiento de un ticket sintético auxiliar, con adjunto e historial persistentes tras recrear sólo la aplicación; no acredita las ocho actividades ni los diez casos académicos. `latest` no determina una versión fija de MantisBT ni garantiza compatibilidad futura.
- **S6.** IEEE Technology Navigator, **“Software Maintenance” → “Types of Maintenance”**, https://technav.ieee.org/topic/software-maintenance/ . Complemento externo autorizado separadamente para las cuatro categorías; sin edición o fecha explícita asumida. No implica lectura íntegra de ISO/IEC 14764 ni de SWEBOK.
- **D1.** MantisBT Administration Guide, `master`, **§7.2 Custom Fields**: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.customize.customfields.html . Definición administrativa, vinculación al proyecto y uso de campos personalizados.
- **D2.** MantisBT Administration Guide, `master`, **§5.8 Email**: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.config.email.html . Transporte de correo, destinatarios, preferencias y notificaciones.
- **D3.** MantisBT Administration Guide, `master`, **§5.7 Signup and Lost Password**: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.config.signup.html . Opciones de recuperación y requisitos de correo.
- **D4.** MantisBT Administration Guide, `master`, **Miscellaneous**, opciones `allow_anonymous_login` y `anonymous_account`: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.config.misc.html . Identidad compartida, restricciones y permisos de reporte que deben validarse en la instalación.
- **D5.** Repositorio oficial MantisBT, rama `master`, **`config_defaults_inc.php`**, opciones `due_date_view_threshold`, `due_date_update_threshold`, `due_date_default` y `phpMailer_method`: https://raw.githubusercontent.com/mantisbt/mantisbt/master/config_defaults_inc.php . Valores de referencia, no prueba de la configuración efectiva.
- **D6.** MantisBT Administration Guide, `master`, **Time Tracking**: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.config.timetracking.html . Registro de esfuerzo opcional, distinto de fechas previstas y cálculo integral de SLA.
- **D7.** MantisBT Administration Guide, `master`, **Miscellaneous**, opciones `print_reports_threshold` y `export_issues_threshold`: https://mantisbt.org/docs/master/en-US/Admin_Guide/html/admin.config.misc.html . Umbrales de impresión y exportación sujetos a configuración y versión.

### Límites de acceso y de interpretación

El video `material/hia_2026_-_Gestion_de_incidentes_caso_glpi.mp4` sólo cuenta con verificación de metadatos durante la preparación: no fue visto, escuchado ni transcripto para sustentar este texto. `material/mantis.rar` sólo fue listado; se identificaron nombres de archivos, sin extracción ni comparación de contenido con la plantilla local.

El acceso preparatorio a un PDF de SWEBOK v4 no alcanzó el capítulo de mantenimiento: **no se utilizan definiciones de ese capítulo inaccesible**. La clasificación se apoya específicamente en S6. Tampoco se citan páginas oficiales que devolvieron errores de acceso. La documentación `master` puede cambiar y deberá contrastarse con la versión realmente instalada.

**Cierre del borrador:** la propuesta documental cubre los puntos teóricos y las ocho actividades. Se completó una prueba funcional básica en un laboratorio auxiliar aislado; no es el TP completo. Quedan pendientes la confirmación docente, la adaptación al proyecto real, los diez registros académicos y sus IDs, la ejecución y evidencia de las ocho actividades, capturas, pruebas de correo y las funciones aún no verificadas. Se mantienen exactamente dos reservas para defensa: INC-09 e INC-10.

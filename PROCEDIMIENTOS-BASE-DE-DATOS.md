# Procedimientos seguros al actualizar la base de datos (HeidiSQL / MySQL)

Notas basadas en lo que aprendimos recuperando `chacabuconoticias_db` — para no repetir el mismo susto.

## 1. Antes de tocar CUALQUIER base de datos real

- **Backup con datos, no solo estructura.** Al exportar (clic derecho en la base → *Export database as SQL*), revisá que la columna/opción de **Datos** esté tildada para cada tabla. Si dice "La exportación de datos fue deseleccionada", ese backup es un cascarón vacío y no sirve para restaurar nada. Esto fue justo lo que nos confundió al principio.
- **Guardá el backup como archivo `.sql` Y como base de datos aparte** (por ejemplo `nombre_db_backup_2026-08-20`), así tenés dos formas de recuperarlo.
- Confirmá en qué base de datos estás parado antes de escribir nada (mirá la pestaña "Base de datos" arriba en HeidiSQL, o poné el nombre completo `basededatos.tabla` en cada consulta para no depender de cuál esté seleccionada en el árbol).

## 2. Antes de correr un UPDATE, DELETE, INSERT o TRUNCATE

1. **Escribí primero un SELECT con el mismo WHERE** para ver exactamente qué filas van a verse afectadas. Nunca asumas.
2. Si el cambio es un reemplazo de texto (`REPLACE`, `REGEXP_REPLACE`), **probá el resultado en una columna nueva** (`SELECT ..., REGEXP_REPLACE(...) AS columna_nueva FROM ...`) antes de aplicarlo con `UPDATE`.
3. Recién cuando el preview se vea bien, cambiás el `SELECT` por el `UPDATE`/`DELETE` real.
4. **Ejecutá una sola sentencia por vez.** No selecciones ni corras varias consultas pegadas de una — así fue como se rompió la base la primera vez (una consulta con "muchas queries" corrió contra la base equivocada).
5. Fijate siempre el resumen que te da HeidiSQL después de ejecutar (`Filas afectadas`, `Rows matched / Changed`) y confirmá que el número tiene sentido para lo que esperabas.

## 3. Detalles de MySQL que nos complicaron (para no repetir el error)

- **Backreferences en `REGEXP_REPLACE`:** en MySQL se usan **`$1`, `$2`** (con signo de pesos), **no** `\1`, `\2`. Si usás `\1`, MySQL descarta la barra y te queda el número suelto como texto literal.
- Si tu patrón necesita una barra invertida literal (por ejemplo `\.` para un punto, o `\s` para espacio), en el string de MySQL hay que escribirla **doble**: `\\.` y `\\s`.
- Un archivo `.sql` de HeidiSQL suele traer sus propias líneas `CREATE DATABASE` / `USE`, que apuntan a la base **original** de donde se exportó, sin importar qué base tengas seleccionada en el árbol. Si ese archivo trae también un `DROP DATABASE IF EXISTS`, al ejecutarlo puede **borrar la base real** aunque quisieras importarlo en otra. Revisá siempre las primeras líneas de un `.sql` antes de ejecutarlo.

## 4. Si algo se rompe

1. No entres en pánico ni corras más queries "a ver si arregla". Primero diagnosticá:
   - `SHOW TABLES FROM basedatos;`
   - `SELECT COUNT(*) FROM basedatos.tabla;`
2. Si hay un backup, restaurá en una base de **prueba** primero (no directo sobre la real), confirmá que los datos están bien, y recién ahí reemplazá la base real.
3. Revisá si tu propio código de la aplicación ya tiene alguna herramienta pensada para esto (como el comando `wordpress:sync-news` que encontramos) — puede ahorrar tener que escribir SQL a mano y a mano es donde se cometen los errores.

## 5. Checklist rápido antes de cualquier cambio

- [ ] ¿Tengo un backup reciente **con datos**?
- [ ] ¿Probé el cambio con un `SELECT` antes del `UPDATE`/`DELETE`?
- [ ] ¿Estoy ejecutando **una sola** sentencia, no un bloque con varias?
- [ ] ¿El nombre de la base de datos en la consulta es el correcto (`basededatos.tabla`)?
- [ ] Si usé `REGEXP_REPLACE`, ¿usé `$1`/`$2` y no `\1`/`\2`?
- [ ] ¿Revisé el resumen de filas afectadas después de ejecutar?

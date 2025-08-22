# Task Manager v1

Aplicación web para gestionar tareas de usuarios.

## Funcionalidades

- Login básico
- Crear tareas con título, descripción, estado y fecha de vencimiento
- Actualizar estado de tareas

## Tecnologías

- PHP (sin framework)
- MySQL
- CSS básico

## Instalación

1. Clonar el repositorio
2. Importar `dump.sql` en MySQL
3. Configurar credenciales en `config/database.php`
4. Acceder vía navegador: `http://localhost/task_manager_v1/views/login.php`

## Usuario de prueba

```sql
INSERT INTO users (username, password) VALUES ('admin', '<?= password_hash("1234", PASSWORD_DEFAULT) ?>');

Hash jefe: $2y$10$BpW0rqT4WrCwpv.vFco30u7XJEV2000.WDjoEN2oh.1J4RgRWA3jS
Hash normal: $2y$10$CwHhmAraRSRINhbquip9PeO3rr8mgS0ImL1xFEdXYmDo3i3utjGde

clave Rol_Admin: claveJefe123
clave usuario01: claveUsuario456
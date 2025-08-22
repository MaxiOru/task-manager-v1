# Task Manager V1

Una aplicación web para gestionar tareas pendientes de usuarios, desarrollada en PHP con MySQL y optimizada para XAMPP.

## 📋 Características principales

- **Sistema de usuarios** con autenticación y roles diferenciados
- **Gestión completa de tareas** (Crear, listar, actualizar estados)
- **Sistema de aprobación** - Los usuarios deben ser aprobados por el jefe
- **Estados de tareas**: Pendiente, En progreso, Completada
- **Fechas de vencimiento** para cada tarea
- **Interfaz responsive** y fácil de usar

## 🚀 Tecnologías utilizadas

- **Backend**: PHP 7.4+
- **Base de datos**: MySQL 8.0+ (compatible con XAMPP)
- **Frontend**: HTML5, CSS3
- **Servidor**: Apache (XAMPP)
- **Arquitectura**: MVC (Modelo-Vista-Controlador)
- **Seguridad**: Contraseñas hasheadas, sesiones PHP, consultas preparadas

## 📁 Estructura del proyecto

```
task-manager_v1/
├── index.php                # Punto de entrada principal
├── logout.php               # Cerrar sesión
├── register.php             # Registro de usuarios
├── approve_user.php         # Aprobación de usuarios
├── config/
│   └── database.php         # Configuración de base de datos
├── controllers/
│   ├── AuthController.php   # Control de autenticación
│   └── TaskController.php   # Control de tareas
├── models/
│   ├── User.php             # Modelo de usuarios
│   └── Task.php             # Modelo de tareas
├── views/
│   ├── login.php            # Vista de inicio de sesión
│   ├── dashboard_jefe.php   # Panel del jefe
│   ├── dashboard_usuario.php # Panel del usuario
│   └── create_task.php      # Crear nueva tarea
├── public/
│   └── css/
│       └── style.css        # Estilos CSS
├── dump.sql                 # Base de datos completa
└── README.md               # Este archivo
```

## ⚙️ Instalación paso a paso

### Requisitos previos
- **XAMPP** 8.0+ instalado
- **PHP** 7.4 o superior
- **MySQL** 8.0 o superior
- **Apache** servidor web

### Pasos de instalación

1. **Descargar/Clonar el proyecto**
   ```bash
   git clone https://github.com/tu-usuario/task-manager_v1.git
   ```

2. **Mover a XAMPP**
   - Copiar la carpeta `task-manager_v1` a `C:/xampp/htdocs/`

3. **Iniciar servicios XAMPP**
   - Abrir el Panel de Control de XAMPP
   - Iniciar **Apache** y **MySQL**

4. **Crear la base de datos**
   - Abrir http://localhost/phpmyadmin
   - Importar el archivo `dump.sql` 
   - O crear manualmente la base de datos `task_manager_v1`

5. **Configurar conexión (si es necesario)**
   - Editar `config/database.php` si tus credenciales son diferentes:
   ```php
   private $host = "localhost";
   private $db_name = "task_manager_v1";
   private $username = "root";        // Usuario por defecto XAMPP
   private $password = "";            // Contraseña vacía por defecto
   ```

6. **Acceder a la aplicación**
   - Abrir navegador en: http://localhost/task-manager_v1

## 🔑 Credenciales de prueba

El sistema incluye usuarios de demostración ya configurados:

### Usuario Jefe/Administrador:
- **Usuario**: `admin`
- **Contraseña**: `password`
- **Funciones**: Aprobar usuarios, ver todos los usuarios

### Usuario Normal:
- **Usuario**: `usuario_demo`
- **Contraseña**: `password`
- **Funciones**: Crear y gestionar tareas propias

## 📖 Guía de uso

### Para usuarios normales:
1. **Registrarse** en http://localhost/task-manager_v1/register.php
2. **Esperar aprobación** del jefe
3. **Iniciar sesión** con las credenciales
4. **Crear tareas** desde el dashboard o desde "Crear nueva tarea"
5. **Actualizar estados** de las tareas (pendiente → en progreso → completada)
6. **Ver todas las tareas** personales en el dashboard

### Para el jefe/administrador:
1. **Iniciar sesión** con credenciales de admin
2. **Aprobar nuevos usuarios** desde el panel de administración
3. **Ver lista de usuarios** pendientes y aprobados
4. **Gestionar el sistema** de usuarios

## 🔧 Funcionalidades técnicas implementadas

### Autenticación y Seguridad
- ✅ Login con validación de credenciales
- ✅ Contraseñas hasheadas con `password_hash()`
- ✅ Gestión de sesiones PHP
- ✅ Validación de roles y permisos
- ✅ Consultas preparadas (PDO) contra inyecciones SQL
- ✅ Escape de datos con `htmlspecialchars()`

### Base de datos
- ✅ Estructura normalizada con relaciones
- ✅ Claves foráneas con integridad referencial
- ✅ Campos enum para estados y roles
- ✅ Indices optimizados para consultas

### Arquitectura y Código
- ✅ Patrón MVC implementado
- ✅ Separación clara de responsabilidades
- ✅ Controladores para lógica de negocio
- ✅ Modelos para acceso a datos
- ✅ Vistas para presentación

## 🚀 Funcionalidades adicionales implementadas

- ✅ **Sistema de aprobación** de usuarios por el jefe
- ✅ **Roles diferenciados** (jefe/usuario normal)
- ✅ **Modelos MVC** para mejor organización del código
- ✅ **Validación completa** de formularios
- ✅ **Mensajes de error y éxito** informativos
- ✅ **Navegación intuitiva** entre páginas
- ✅ **Fechas de vencimiento** con validación
- ✅ **Estados múltiples** de tareas
- ✅ **Interfaz limpia** y responsive

## 📊 Base de datos

### Tabla `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | int(11) | ID único auto-incremental |
| username | varchar(50) | Nombre de usuario único |
| password | varchar(255) | Contraseña hasheada |
| role | enum('jefe','normal') | Rol del usuario |
| approved | tinyint(1) | Estado de aprobación |

### Tabla `tasks`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | int(11) | ID único auto-incremental |
| user_id | int(11) | ID del usuario (FK) |
| title | varchar(100) | Título de la tarea |
| description | text | Descripción detallada |
| status | enum(...) | Estado de la tarea |
| due_date | date | Fecha de vencimiento |
| approved | tinyint(1) | Aprobación futura |
| assigned_to | int(11) | Asignación futura (FK) |

## 🔄 Flujo de la aplicación

1. **Registro** → Usuario se registra (approved = 0)
2. **Aprobación** → Jefe aprueba usuario (approved = 1)
3. **Login** → Usuario inicia sesión
4. **Dashboard** → Se redirige según rol (jefe/usuario)
5. **Gestión** → Crear/actualizar tareas o aprobar usuarios

## 📝 Posibles mejoras futuras

- [ ] Sistema de asignación de tareas entre usuarios
- [ ] Filtros avanzados y búsqueda de tareas
- [ ] Notificaciones por email
- [ ] API REST para integración
- [ ] Frontend moderno (React/Vue)
- [ ] Sistema de comentarios en tareas
- [ ] Dashboard con gráficos y estadísticas
- [ ] Exportación de reportes
- [ ] Recordatorios automáticos
- [ ] Categorías de tareas

## 🚨 Solución de problemas comunes

### Error de conexión a la base de datos
- Verificar que XAMPP esté ejecutando Apache y MySQL
- Revisar credenciales en `config/database.php`
- Asegurarse de que la base de datos `task_manager_v1` exista

### Página en blanco o errores PHP
- Revisar logs de error en `C:/xampp/apache/logs/error.log`
- Verificar que la versión de PHP sea compatible
- Asegurar que todos los archivos estén en la ruta correcta

### Problemas de autenticación
- Verificar que las contraseñas sean exactamente: `password`
- Limpiar cookies y caché del navegador
- Revisar que los usuarios estén aprobados (approved = 1)

## 📄 Licencia

Este proyecto está desarrollado como tarea técnica para evaluación de habilidades en desarrollo web con PHP.

## 👨‍💻 Información técnica

- **Desarrollado**: Como prueba técnica para puesto de desarrollador
- **Tiempo de desarrollo**: Proyecto académico/profesional
- **Nivel**: Intermedio - enfocado en buenas prácticas y funcionalidad
- **Compatibilidad**: XAMPP, Apache, MySQL, PHP 7.4+

---

**¿Necesitas ayuda?** Revisa la sección de solución de problemas o verifica que XAMPP esté funcionando correctamente.
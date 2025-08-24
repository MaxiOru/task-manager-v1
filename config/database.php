<?php
/**
 * Clase Database
 * Gestiona la conexión a la base de datos MySQL usando PDO.
 * 
 * Nota importante:
 * Las credenciales están hardcodeadas en este archivo únicamente por motivos de simplicidad
 * en el contexto de la prueba técnica. En un entorno real o de producción, esta práctica
 * representa un riesgo de seguridad, ya que expone información sensible.
 * 
 * Recomendación:
 * Para aplicaciones reales, se recomienda utilizar variables de entorno o archivos de configuración
 * externos que no estén expuestos públicamente, con el fin de proteger las credenciales.
 */
class Database {
    // Parámetros de conexión (solo para fines de prueba técnica)
    private $host = "localhost";
    private $db_name = "task_manager_v1";
    private $username = "root";
    private $password = "";
    public $conn; // Instancia de conexión PDO

    /**
     * Establece y retorna la conexión PDO a la base de datos.
     * @return PDO|null
     */
    public function connect() {
        $this->conn = null;
        try {
            // Crear nueva instancia PDO con los parámetros configurados
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            // Configurar modo de errores para lanzar excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Mostrar mensaje de error si la conexión falla
            echo "Error de conexión: " . $e->getMessage();
        }
        // Retornar la instancia de conexión (o null si falló)
        return $this->conn;
    }
}
?>


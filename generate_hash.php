<?php
echo "Hash jefe: " . password_hash("claveJefe123", PASSWORD_DEFAULT) . "<br>";
echo "Hash normal: " . password_hash("claveUsuario456", PASSWORD_DEFAULT);
?>
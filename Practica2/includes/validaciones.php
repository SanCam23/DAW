<?php

/**
 * Valida el nombre de usuario
 */

function validarUsuario($usuario) {
    $usuario = trim($usuario);
    
    if (empty($usuario)) {
        return "El nombre de usuario es obligatorio.";
    }
    
    if (strlen($usuario) < 3 || strlen($usuario) > 15) {
        return "El nombre de usuario debe tener entre 3 y 15 caracteres.";
    }
    
    // No puede comenzar con número
    if (preg_match('/^[0-9]/', $usuario)) {
        return "El nombre de usuario no puede comenzar con un número.";
    }
    
    // Sólo letras inglesas y números
    if (!preg_match('/^[a-zA-Z0-9]+$/', $usuario)) {
        return "El nombre de usuario sólo puede contener letras del alfabeto inglés y números.";
    }
    
    return true;
}

/**
 * Valida la contraseña
 */
function validarPassword($password) {
    if (empty($password)) {
        return "La contraseña es obligatoria.";
    }
    
    if (strlen($password) < 6 || strlen($password) > 15) {
        return "La contraseña debe tener entre 6 y 15 caracteres.";
    }
    
    // Caracteres permitidos
    if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $password)) {
        return "La contraseña sólo puede contener letras del alfabeto inglés, números, guion y guion bajo.";
    }
    
    // Al menos una mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        return "La contraseña debe contener al menos una letra mayúscula.";
    }
    
    // Al menos una minúscula
    if (!preg_match('/[a-z]/', $password)) {
        return "La contraseña debe contener al menos una letra minúscula.";
    }
    
    // Al menos un número
    if (!preg_match('/[0-9]/', $password)) {
        return "La contraseña debe contener al menos un número.";
    }
    
    return true;
}

/**
 * Valida que las contraseñas coincidan
 */
function validarConfirmPassword($password, $confirm_password) {
    if ($password !== $confirm_password) {
        return "Las contraseñas no coinciden.";
    }
    
    return true;
}

/**
 * Validar el email 
 */
function validarEmail($email) {
    $email = trim($email);
    
    if (empty($email)) {
        return "El email es obligatorio.";
    }
    
    // Longitud total máxima: 254 caracteres
    if (strlen($email) > 254) {
        return "El email no puede superar los 254 caracteres.";
    }
    
    // Verificar que tenga formato básico de email
    if (strpos($email, '@') === false) {
        return "El email debe contener un símbolo @.";
    }
    
    list($parte_local, $dominio) = explode('@', $email, 2);
    
    // Validar parte-local (máximo 64 caracteres)
    if (strlen($parte_local) < 1 || strlen($parte_local) > 64) {
        return "La parte local del email debe tener entre 1 y 64 caracteres.";
    }
    
    // Validar dominio (máximo 255 caracteres)
    if (strlen($dominio) < 1 || strlen($dominio) > 255) {
        return "El dominio del email debe tener entre 1 y 255 caracteres.";
    }
    
    // Validar caracteres de parte-local
    // Letras mayúsculas/minúsculas, dígitos, caracteres: !#$%&'*+-/=?^_`{|}~ y punto
    if (!preg_match('/^[a-zA-Z0-9!#$%&\'*+\-\/=?^_`{|}~.]+$/', $parte_local)) {
        return "La parte local del email contiene caracteres no permitidos.";
    }
    
    // El punto no puede aparecer al principio ni al final
    if (substr($parte_local, 0, 1) === '.' || substr($parte_local, -1) === '.') {
        return "El punto no puede aparecer al principio ni al final de la parte local.";
    }
    
    // No pueden aparecer dos o más puntos seguidos
    if (strpos($parte_local, '..') !== false) {
        return "No pueden aparecer dos o más puntos seguidos en la parte local.";
    }
    
    // Validar dominio: secuencia de uno o más subdominios separados por punto
    $subdominios = explode('.', $dominio);
    
    if (count($subdominios) < 1) {
        return "El dominio debe contener al menos un subdominio.";
    }
    
    foreach ($subdominios as $subdominio) {
        // Longitud máxima de subdominio: 63 caracteres
        if (strlen($subdominio) > 63) {
            return "Cada subdominio no puede superar los 63 caracteres.";
        }
        
        if (strlen($subdominio) < 1) {
            return "Cada subdominio debe tener al menos 1 carácter.";
        }
        
        // Caracteres permitidos en subdominio: letras inglés, números y guion
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $subdominio)) {
            return "El subdominio '$subdominio' contiene caracteres no permitidos.";
        }
        
        // El guion no puede aparecer ni al principio ni al final
        if (substr($subdominio, 0, 1) === '-' || substr($subdominio, -1) === '-') {
            return "El guion no puede aparecer ni al principio ni al final de un subdominio.";
        }
    }
    
    return true;
}

/**
 * Valida que se haya seleccionado un sexo
 */
function validarSexo($sexo) {
    if (empty($sexo)) {
        return "Debe seleccionar un sexo.";
    }
    
    // Validar que sea un valor numérico válido (1, 2)
    if (!in_array($sexo, ['1', '2'])) {
        return "El valor del sexo no es válido.";
    }
    
    return true;
}

/**
 * Valida la fecha de nacimiento
 */
function validarFechaNacimiento($fecha_nacimiento) {
    if (empty($fecha_nacimiento)) {
        return "La fecha de nacimiento es obligatoria.";
    }
    
    // Verificar formato dd/mm/yyyy
    if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $fecha_nacimiento)) {
        return "El formato de fecha debe ser dd/mm/yyyy.";
    }
    
    // Separar día, mes y año
    list($dia, $mes, $anio) = explode('/', $fecha_nacimiento);
    
    // Validar fecha
    if (!checkdate($mes, $dia, $anio)) {
        return "La fecha de nacimiento no es válida.";
    }
    
    // Calcular edad exacta
    $nacimiento = DateTime::createFromFormat('d/m/Y', $fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;
    
    // Verificar si ya cumplió 18 años este año
    $cumple18 = $nacimiento->modify('+18 years');
    if ($cumple18 > $hoy) {
        return "Debe tener al menos 18 años recién cumplidos.";
    }
    
    return true;
}

/**
 * Valida todos los campos del formulario de usuario
 * $es_actualizacion: indica si es una actualización de datos (true) o un nuevo registro (false)
 */
function validarFormularioUsuario($datos, $es_actualizacion = false) {
    $errores = [];
    
    // Validar usuario
    $resultado_usuario = validarUsuario($datos['usuario'] ?? '');
    if ($resultado_usuario !== true) {
        $errores['usuario'] = $resultado_usuario;
    }
    
    // Validar email
    $resultado_email = validarEmail($datos['email'] ?? '');
    if ($resultado_email !== true) {
        $errores['email'] = $resultado_email;
    }
    
    // Validar contraseña (solo si no es actualización o si se proporciona nueva contraseña)
    if (!$es_actualizacion || !empty($datos['password'])) {
        $resultado_password = validarPassword($datos['password'] ?? '');
        if ($resultado_password !== true) {
            $errores['password'] = $resultado_password;
        }
        
        // Validar confirmación de contraseña
        if (isset($datos['confirm_password'])) {
            $resultado_confirm = validarConfirmPassword($datos['password'] ?? '', $datos['confirm_password'] ?? '');
            if ($resultado_confirm !== true) {
                $errores['confirm_password'] = $resultado_confirm;
            }
        }
    }
    
    // Validar sexo
    $resultado_sexo = validarSexo($datos['sexo'] ?? '');
    if ($resultado_sexo !== true) {
        $errores['sexo'] = $resultado_sexo;
    }
    
    // Validar fecha de nacimiento
    $resultado_fecha = validarFechaNacimiento($datos['fecha_nacimiento'] ?? '');
    if ($resultado_fecha !== true) {
        $errores['fecha_nacimiento'] = $resultado_fecha;
    }
    
    return empty($errores) ? true : $errores;
}

?>
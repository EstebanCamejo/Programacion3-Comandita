# 🍽️ Simulación de Restaurante - API REST

Este proyecto es una **API REST desarrollada en PHP con Slim Framework** que simula la gestión completa de un restaurante.  
Incluye operaciones para **usuarios, mesas, pedidos, productos, encuestas y logs**, con seguridad basada en **JWT y roles**.

## 🚀 Funcionalidades principales
- **Usuarios:** CRUD completo, login con generación de token JWT.
- **Roles:** Socio, mozo, cocinero, bartender, pastelero, cervecero.  
- **Mesas y pedidos:** Gestión de estados, cierres, facturación entre fechas, pedidos vencidos.
- **Productos:** CRUD, importación/exportación CSV, ranking por ventas.
- **Logs:** Auditoría de operaciones por sector y empleado.
- **Reportes:** Generación de PDF y consultas analíticas (ej. mesa más usada, productos más vendidos).

## 🔒 Seguridad
- Autenticación con **JWT**.
- Autorización por **roles** mediante middlewares.
- Endpoints protegidos según el tipo de usuario.

## 🛠️ Tecnologías
- **PHP 7+**
- **Slim Framework**
- **MySQL + PDO** (consultas parametrizadas seguras)
- **Postman** para pruebas y consumo de endpoints
- **Dompdf** para generación de reportes en PDF

## 📂 Estructura
- `Controllers/` → lógica de negocio (ej. `UsuarioController.php`)
- `Models/` → interacción con la base de datos (ej. `Usuario.php`)
- `Middlewares/` → autenticación y autorización (ej. `AutentificadorMiddleware.php`)
- `index.php` → definición de rutas y grupos de endpoints

## 🧪 Pruebas
El proyecto fue probado con **Postman**, simulando el flujo completo de un restaurante:  
- Login de usuarios y generación de token.  
- Acceso a endpoints según rol.  
- Operaciones CRUD y consultas analíticas.  

## 📌 Objetivo
Este proyecto fue desarrollado como una **simulación de un sistema de restaurante**, demostrando modularidad, seguridad y escalabilidad en el diseño de APIs REST.

---

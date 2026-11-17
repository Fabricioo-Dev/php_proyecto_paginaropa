# ELITE - E-Commerce de Moda Premium 🛍️

Elite es un sistema de tienda de ropa online desarrollado con **PHP puro, MySQL y Vanilla JavaScript**. 

Este proyecto fue creado para la materia de *Bases de Datos II* (Licenciatura en Sistemas, Universidad Champagnat) con un fuerte enfoque en la **integridad de los datos, la seguridad y la arquitectura del backend**.

## 🚀 Características Destacadas

### 🔐 Backend y Base de Datos (El núcleo del proyecto)
El sistema no utiliza ORMs ni frameworks, todo está programado a medida para garantizar el control total sobre las transacciones y la seguridad:
* **Transacciones ACID y Control de Concurrencia:** El proceso de compra (checkout) utiliza transacciones explícitas (`BEGIN`, `COMMIT`, `ROLLBACK`) y bloqueos pesimistas (`SELECT ... FOR UPDATE`) para prevenir *race conditions* (condiciones de carrera) al descontar el stock.
* **Seguridad contra Inyección SQL:** El 100% de las consultas que reciben datos del usuario utilizan sentencias preparadas (`prepare` y `bind_param`).
* **Protección de Credenciales:** Hashing seguro de contraseñas utilizando el algoritmo `bcrypt` (`password_hash`).
* **Integridad Referencial Estricta:** Uso de Claves Foráneas (FK) compuestas y restricciones `ON DELETE RESTRICT` para evitar inconsistencias o archivos huérfanos.
* **Lógica en la Base de Datos:**
  * **Triggers:** Disparadores para normalizar datos (correos en minúsculas) y para registrar auditorías de cambios de precio (`AFTER UPDATE`).
  * **Vistas y Funciones SQL:** Optimización de reportes de inventario y agrupaciones de proveedores directamente en el motor de base de datos.
* **Borrado Lógico:** Los productos y registros críticos no se eliminan físicamente (evitando romper historiales de ventas), sino que cambian su estado a `Discontinuada`.

### 💻 Frontend y Experiencia de Usuario (UI/UX)
* **Diseño Minimalista y Responsive:** Interfaz moderna en blanco y negro (estilo flat design), totalmente adaptable a dispositivos móviles, sin depender de librerías CSS externas.
* **Carga Asíncrona (AJAX):** El catálogo de productos y la adición al carrito se comunican con una API RESTful propia (JSON) para no recargar la página.
* **Filtros en el DOM:** El panel de administración procesa la búsqueda de miles de registros en tiempo real manipulando el DOM con Vanilla JS, aliviando la carga del servidor.

## 📁 Estructura del Proyecto

* `/Backend`: Scripts que procesan la lógica del negocio (compras, sesiones, ABM de usuarios).
* `/administrador`: Panel de control (CRUD de prendas, marcas, proveedores y reportes de inventario).
* `/restful_api`: Endpoints en PHP que devuelven JSON para ser consumidos vía AJAX.
* `/CSS`: Hojas de estilo puras (CSS Grid y Flexbox).

## 🛠️ Tecnologías Utilizadas
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla + jQuery para AJAX).
* **Backend:** PHP 8.2
* **Base de Datos:** MySQL / MariaDB (Script relacional complejo).

## ⚙️ Instalación Local

1. Clona el repositorio en la carpeta `htdocs` de tu servidor local (ej. XAMPP).
2. Crea una base de datos en phpMyAdmin o tu cliente SQL llamada `ropa_online` con cotejamiento `utf8mb4_general_ci`.
3. Importa el archivo `ropa_online (9).sql` que incluye la estructura, vistas, funciones, triggers y datos de prueba.
4. Abre `http://localhost/php_proyecto_paginaropa-main` en tu navegador.

*Para acceder al panel de administrador, utiliza un usuario con el campo `tipo_de_usuario = 1` en la base de datos.*

---
**Desarrolladores:** Fabricio Funes & Uriel Cabrera

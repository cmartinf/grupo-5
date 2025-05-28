# Portal de Noticias - Proyecto Integrador

---

## 1. Descripción del Sistema

Este proyecto consiste en un **portal de noticias**, que permite a los usuarios consultar noticias organizadas por categorías, tendencias y etiquetas, con una interfaz atractiva y amigable.  

La solución consta de un **frontend desarrollado en Vue.js 3** y un **backend construido con Laravel**, desacoplados y comunicados mediante API REST usando Axios.

### Problema  
Muchos portales de noticias no ofrecen una experiencia fluida en dispositivos móviles ni un sistema organizado para filtrar noticias según intereses. Además, la gestión de contenido suele ser poco intuitiva para los administradores.

### Solución  
Desarrollar una plataforma que permita:  
- Visualización clara y organizada de noticias (carousel, categorías, tendencias, etiquetas).  
- Gestión eficiente de noticias, autores y etiquetas con roles diferenciados (usuarios y admins).  
- Arquitectura modular y escalable, asegurando facilidad de mantenimiento y expansión futura.

### Usuarios y objetivos  
- **Usuarios finales:** Consultar noticias filtradas, navegar por categorías y tendencias.  
- **Administradores:** Crear, editar y eliminar noticias, gestionar autores y etiquetas, controlar acceso mediante autenticación basada en roles.

---

## 2. Arquitectura Propuesta

![Diagrama de Arquitectura](ruta/a/diagrama-arquitectura.png)  
*Ejemplo: Diagrama con capas Frontend, Backend, Base de Datos y comunicación mediante API REST.*

- **Frontend:** Vue.js 3, aplicación SPA que consume API REST del backend.  
- **Backend:** Laravel 11, expositor de endpoints REST, gestión de lógica de negocio y base de datos MySQL.  
- **Comunicación:** Axios en frontend para consumir los endpoints del backend de forma asíncrona.  
- **Base de datos:** MySQL, donde se almacenan usuarios, roles, noticias, etiquetas y autores.

### Decisiones tecnológicas  
- Laravel: Framework PHP robusto con soporte nativo para APIs y seguridad (Laravel Breeze para autenticación).  
- Vue.js 3: Framework moderno para SPA, modular y altamente performant.  
- Axios: Cliente HTTP sencillo y eficiente para consumir API REST.  
- MySQL: Base de datos relacional confiable y ampliamente usada.  

---

## 3. Frontend

- **Framework:** Vue.js 3  
- **Comunicación con Backend:** Axios para llamadas HTTP REST a endpoints Laravel.  
- **Componentes principales:**  
  - Carousel de titulares principales.  
  - Listado de noticias por categorías.  
  - Widget de noticias trending.  
  - Filtros por etiquetas.  
- **Flujo de navegación:**  
  - Página principal con carousel y secciones.  
  - Navegación a detalle de noticia y filtrado dinámico por categorías o etiquetas.  
- **Estado:** Opcionalmente Vuex para gestión de estado centralizada (según necesidades).  

---

## 4. Backend

- **Endpoints principales:**  
  - `GET /api/news` - Listar noticias (con filtros opcionales).  
  - `GET /api/news/{id}` - Detalle de noticia.  
  - `POST /api/news` - Crear noticia (solo admin).  
  - `PUT /api/news/{id}` - Editar noticia (solo admin).  
  - `DELETE /api/news/{id}` - Eliminar noticia (solo admin).  
  - Endpoints similares para autores y etiquetas.  
- **Buenas prácticas:**  
  - Modularización por controladores, modelos y rutas.  
  - Uso de middleware para autenticación y autorización.  
  - Validación de requests mediante Form Requests.  
- **Patrones:**  
  - MVC clásico de Laravel.  
  - Principios SOLID para mantener código limpio y escalable.  

---

## 5. Testing

- **Tipos de tests:**  
  - Unitarios: Para lógica de negocio y modelos en backend.  
  - Integración: Para endpoints API, asegurando respuestas y permisos.  
  - Tests funcionales básicos en frontend (opcional).  
- **Herramientas:**  
  - PHPUnit para backend Laravel.  
  - Jest / Vue Test Utils para frontend (si se implementa).  
- **Justificación:**  
  - Garantizar calidad y robustez del código.  
  - Detectar fallas tempranas y asegurar integridad de funcionalidades.  

---

## 6. Despliegue y CI/CD

- **Plataforma de hosting:**  
  - Backend desplegado en servidor compatible PHP (ejemplo: Heroku, AWS, DigitalOcean).  
  - Frontend desplegado en servicios estáticos (ejemplo: Vercel, Netlify).  
- **Automatización:**  
  - Uso de GitHub Actions para pipeline CI/CD: pruebas automatizadas y despliegue continuo.  
- **Integración frontend-backend:**  
  - Configuración del `.env` en frontend para apuntar a backend desplegado.  

---

## 7. Seguridad

- **Autenticación y autorización:**  
  - Laravel Breeze para autenticación segura con roles (admin y usuario).  
- **Validación de inputs:**  
  - Validación exhaustiva en backend con Form Requests para prevenir inyecciones o datos inválidos.  
- **Gestión de errores:**  
  - Manejo centralizado de errores con respuestas JSON claras para frontend.  
- **Buenas prácticas:**  
  - Uso de HTTPS en despliegue.  
  - Protección contra CSRF, XSS y ataques comunes.  

---

## Tecnologías utilizadas

| Componente | Tecnología                      |
|------------|--------------------------------|
| Backend    | Laravel 11, PHP 8.1+, MySQL    |
| Frontend   | Vue.js 3, Bootstrap 5, Axios   |
| Testing    | PHPUnit, Jest (opcional)        |
| CI/CD      | GitHub Actions                 |
| Despliegue | en desarrollo |

---

## Instrucciones para instalar y correr el proyecto

### Clonar el repositorio
```bash
git clone https://github.com/cmartinf/grupo-5
cd Manager_News
```

### Backend (Laravel) Setup
1. Navegar al directorio de Laravel:
   ```bash
   cd BackEnd
   ```

2. Instalar las dependencias:
   ```bash
   composer install
   ```

3. Copiar el archivo `.env.example` a `.env` y configurar las variables de entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configurar el archivo `.env`:
   - Establece las credenciales de la base de datos: :
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=Manager_News
     DB_USERNAME=root
     DB_PASSWORD=your_password
     ```

5. Correr las migraciones y seeders:
   ```bash
   php artisan migrate --seed
   ```

6. El servidor de Laravel en el backend:
   ```bash
   php artisan serve
   ```
   The backend will run at `http://127.0.0.1:8000`.

---

### Frontend (Vue.js) Setup
1. Navegar hasta el directorio Vue.js:
   ```bash
   cd FrontEnd
   ```

2. Instalar las dependencias:
   ```bash
   npm install
   ```

3. Copiar el archivo `.env.example` a `.env`:
   ```bash
   cp .env.example .env
   ```

4. Configurar la URL del backend en `.env`:
   ```bash
   VITE_APP_API_URL=http://127.0.0.1:8000
   ```

5. Iniciar el servidor de desarrollo:
   ```bash
   npm run dev
   ```
   The frontend will run at `http://127.0.0.1:5173`.

---

## Ejecutar la aplicación
- Asegurarse que los servidores **backend** y **frontend** estén funcionando:
  - BackEnd: `php artisan serve`
  - FrontEnd: `npm run dev`
  
- Acceso a la aplicación por **frontend URL** (`http://127.0.0.1:5173`).

---




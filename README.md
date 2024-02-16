# Cambios en la Aplicación Cliente

### Detalles y Navegación Mejorados

- Ahora puedes ver detalles las opciones de "Siguiente" y "Anterior".

### Lista de Clientes Ordenada

- La lista de clientes puede ordenarse por nombre, apellido, correo electrónico, género o IP para una mejor organización.

### Validación Mejorada

- Las operaciones de Nuevo y Modificar verifican los datos, incluyendo correo electrónico único, formato correcto de IP y teléfono.

### Imágenes de Clientes

- Se muestra la imagen asociada al cliente almacenada en 'uploads', o una por defecto desde [robohasp.org](https://robohasp.org) si no existe.

### Información de IP y Bandera del País

- En Detalles, se muestra la bandera del país asociado a la IP utilizando [ip-api.com](https://ip-api.com/) y [flagpedia.net](https://flagpedia.net/).

### Generación de PDF

- Ahora puedes generar un PDF con todos los detalles del cliente.

### Nueva Tabla de Usuarios

- Se ha creado una tabla de usuarios en la base de datos con campos de login, password (encriptada) y rol (0/1).
- La aplicación ahora requiere un login y password correctos para el acceso. 
- Después de tres intentos erróneos, se solicitará reiniciar el navegador.

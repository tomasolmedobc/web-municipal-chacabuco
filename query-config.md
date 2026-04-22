# Cambia las imagenes destacadas que no tienen imagenes, por una por defecto en base de datos

UPDATE noticias
SET imagen_destacada = '/images/importantes/default-noticia.webp'
WHERE imagen_destacada IS NULL
   OR imagen_destacada = '';


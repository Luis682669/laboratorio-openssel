🛠️ Dificultades Técnicas y Soluciones Aplicadas

Dificultad: Error de ruta absoluta y fallo en el entorno al cargar `openssl.cnf` en WAMP/XAMPP 


Descripción del problema:** Al ejecutar inicialmente los scripts lineales provistos por la cátedra (como `FirmarMesaje.php` y `firma7.php`) , el intérprete de PHP arrojó una excepción crítica indicando que no era posible ubicar el archivo de configuración externa de OpenSSL. Esto se debió a que el código original contenía una ruta rígida y absoluta apuntando hacia una instalación externa de Windows (`C:\OpenSSL-Win64\bin\cnf\openssl.cnf`) , la cual no coincidía con la estructura jerárquica de carpetas local del servidor web activo (WampServer/XAMPP). Como consecuencia directa, las funciones criptográficas de firma digital fallaban al no poder instanciar de forma correcta el par de llaves asimétricas públicas y privadas.


 
Solución técnica aplicada (Abstracción nativa del entorno):** En lugar de forzar otra ruta rígida absoluta que volviera a romper el principio DRY (Don't Repeat Yourself) o la portabilidad del código , se aplicó la recomendación oficial de soporte para servidores locales modernos. Se ingresó al código fuente de los scripts independientes y **se eliminó por completo la línea correspondiente al parámetro `'config'**` dentro del arreglo asociativo `$configArgs`.


Bloque de código corregido y optimizado en el script:


php
// Se removió la directiva 'config' rígida para que PHP herede las variables globales del sistema
$configArgs = array(
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA
);

Resultados

<img width="595" height="1214" alt="image" src="https://github.com/user-attachments/assets/c3fb4150-8a5b-4656-8287-f4c84add36f0" />
<img width="927" height="1072" alt="image" src="https://github.com/user-attachments/assets/488d5184-2143-4fb8-b96a-f8cb8d1188ec" />
<img width="927" height="898" alt="image" src="https://github.com/user-attachments/assets/8be7c1d1-7137-4cc5-b172-3f4bdf26bc65" />




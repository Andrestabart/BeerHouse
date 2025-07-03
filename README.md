# BeerHouse - Gastro Bar Orders

Aplicación web sencilla para registrar pedidos en un gastro bar utilizando PHP puro y SQLite. Permite guardar información de los clientes y sus pedidos, exportar los pedidos diarios en CSV y generar un reporte PDF.

## Uso rápido
1. Asegúrate de tener PHP con soporte para SQLite.
2. Coloca todos los archivos en un servidor que ejecute PHP.
3. Accede a `index.php` desde tu navegador.
4. Ingresa los pedidos mediante el formulario.
5. Usa los enlaces "Exportar CSV" y "Reporte PDF" para obtener los pedidos del día.

El PDF se genera sin librerías externas mediante un generador básico incluido en `export_pdf.php`.

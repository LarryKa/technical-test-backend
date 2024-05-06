# Despues de clonar el proyecto usar el comando composer install
# Posteriormente usar el comando php artisan key:generate
# Crear base de datos technicaltest
# Copiar archivo .env.example y quitar el ".example" quedando de la siguiente forma .env
# En archivo .env seleccionar la base de datos creada DB_DATABASE=technicaltest, usar su respectivo DB_USERNAME y DB_PASSWORD
# ejecutar el comando php artisan migrate
# ejecutat el comando php artisan db:seed --class=ContactSeeder para llenar la base de datos con información ficticia 
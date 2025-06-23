#!/bin/bash
# echo "Waiting for the database to be ready..."#!/bin/bash
echo "Waiting for the database to be ready..."
#echo "Current working directory: $(pwd)"
export $(grep -v '^#' /var/www/.env | xargs)
while ! mysqladmin ping -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
    echo "${DB_HOST}"
    echo "Database is not ready yet. Retrying in 1 second..."
    sleep 3
done
echo "done"

echo "Running migrations..."
php artisan migrate --force

# Check if the database has already been seeded
# SEEDED_FILE=/var/www/storage/app/database_seeded
# if [ ! -f "$SEEDED_FILE" ]; then
#     echo "Seeding the database..."
php artisan db:seed --force

    # Create a file to indicate that the database has been seeded
#     touch $SEEDED_FILE
#     echo "Database seeding completed."
# else
#     echo "Database has already been seeded. Skipping seeding."
# fi

#Create the symbolic link
# echo "Creating symbolic link for storage..."
# php artisan storage:link
# chown -R www:www /var/www/storage /var/www/public/storage
# chmod -R 755 /var/www/storage /var/www/public/storage
# # chown -R www:www storage
# # chown -R www:www bootstrap/cache
# # chown -R www:www public/storage

# chmod -R 777 public/
# chmod -R 777 storage bootstrap/cache

php artisan cache:clear
php artisan view:clear


# Install npm dependencies
# echo "Installing npm dependencies..."
# npm install

# # Run npm dev server
# echo "Starting npm dev server..."
# npm run dev &

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm



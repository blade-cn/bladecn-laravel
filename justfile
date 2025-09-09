setup:
    @echo "Setting up the development environment..."
    composer install
    mkdir -p workbench/database
    touch workbench/database/database.sqlite
    echo "DB_CONNECTION=sqlite\nDB_DATABASE=workbench/database/database.sqlite" > workbench/.env
    ./vendor/bin/testbench migrate

recreate-db:
    @echo "Recreating the SQLite database..."
    ./vendor/bin/testbench migrate:refresh

check:
    @echo "Running static analysis and tests..."
    composer run check

setup:
    @echo "Setting up the development environment..."
    composer install
    ./vendor/bin/testbench workbench:devtool --basic -n
    mkdir -p workbench/database
    touch workbench/database/database.sqlite
    ./vendor/bin/testbench migrate
    git restore composer.json

recreate-db:
    @echo "Recreating the SQLite database..."
    ./vendor/bin/testbench migrate:refresh

check:
    @echo "Running static analysis and tests..."
    composer run check

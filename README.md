# Simple Movies List

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/lrencallado/simple-movie-list.git
    ```
2. Navigate to the project directory:
    ```bash
    cd simple-movie-list
    ```
3. Install dependencies using Composer:
    ```bash
    composer install
    ```
4. Install Node.js dependencies:
    ```bash
    npm install
    ```
5. Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```
6. Update the `.env` file with your database credentials:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```
7. Generate the application key:
    ```bash
    php artisan key:generate
    ```
8. Run database migrations:
    ```bash
    php artisan migrate
    ```
9. Seed the database:
    ```bash
    php artisan db:seed --class=MoviesTableSeeder
    ```

## Usage

1. Start the development server:
    ```bash
    composer run dev
    ```
2. Open your browser and navigate to `http://localhost:8000`.

## Testing

1. Run the test:
    ```bash
    php artisan test --filter=MovieApiTest
    ```

# Champions League Standings Simulator

A simple app to simulate a Champions League game with 4 teams.

## Quick Start

1. **Clone the repo & install dependencies**
    ```bash
    git clone <repo-url>
    cd champions-league-standings
    composer install
    npm install
    ```
2. **Set up environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3. **Run migrations and seed data**
    ```bash
    php artisan migrate:fresh --seed
    ```
4. **Build front**
    ```bash
    npm run build
    ```
5. **Start the app**
    ```bash
    php artisan serve
    npm run dev
    ```
6. **Open** [http://localhost:8000](http://localhost:8000) in your browser.

## Usage

-   Click "Play Week" to simulate one week
-   Click "Play All" to finish the league
-   Click "Reset League" to start over
-   Edit any match result to see updated standings

---

For any issues, just open an issue or PR. Enjoy!

**Author:** Stefan Prence

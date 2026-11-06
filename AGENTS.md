# AGENTS.md

Instrukcje dla agentów pracujących w tym repozytorium.

## Projekt

StreamRip — katalog kart kolekcjonerskich dla widzów streama. Aplikacja PHP 8.3
bez frameworka: własny router, kontrolery i widoki `.html.php`.

## Tech stack

| Warstwa | Technologia |
|---|---|
| Backend | PHP 8.3 (bez frameworka) |
| Serwer HTTP | Nginx + php-fpm |
| Autoload | Composer (PSR-4) |
| Konteneryzacja | Docker Compose |
| Baza danych | PostgreSQL w compose — **jeszcze nieużywana przez kod** |

## Uruchomienie

```bash
docker compose up --build -d
```

Aplikacja: http://localhost:8080 (port nadpisuje `APP_PORT`).

Po zmianach w `composer.json` lub strukturze namespace:

```bash
docker compose exec php composer dump-autoload
```

## Struktura

```
public/index.php              # front controller — jedyny punkt wejścia HTTP
public/css/style.css          # style
public/views/*.html.php       # widoki (fragmenty treści wstawiane do layoutu)
public/views/layout.html.php  # wspólny layout (header, content, footer)
public/views/partials/*.php   # partiale: header, footer, card-list
src/Routing.php               # router (rejestracja + dispatch)
src/controllers/              # kontrolery (namespace App\Controller)
src/Repository/               # źródła danych (namespace App\Repository)
docker/                       # konfiguracja nginx, php, db
```

## Architektura — obowiązujące zasady

Przepływ: **nginx → `public/index.php` → `Routing::dispatch()` → kontroler → widok**.

- **Trasy** rejestruje się wyłącznie w `public/index.php` przez `$routing->get(...)` /
  `$routing->post(...)`. Nie dodawaj routingu w kontrolerach.
- **Parametry trasy** zapisuje się jako `{nazwa}`; router obsługuje tylko wartości
  cyfrowe (`\d+`) i konwertuje je na `int`.
- **Kontrolery** dziedziczą po `App\Controller\AppController` (wymusza to `dispatch()`,
  inaczej `RuntimeException`). Klasy są `final`, akcje zwracają `void`.
- **Dane** przekazuje się do widoku wyłącznie przez `$this->render('nazwa', [...])`.
  Klucze tablicy stają się zmiennymi w widoku (`extract`).
- **Layout**: `render()` domyślnie wstawia widok do `public/views/layout.html.php`
  (jako `$content`); layout sam wywołuje partiale `header` i `footer`. Widoki nie
  wywołują ich samodzielnie. Pełną stronę bez layoutu renderuje się przez
  `$this->render(..., null)` (np. `/overlay`).
- **Partiale** wywołuje się przez `$this->partial('nazwa', [...])`; trafiają do
  `public/views/partials/`.
- **Escaping**: każdy tekst z danych wstawiaj przez `htmlspecialchars(...)`.
  Liczby rzutuj przez `(int)` / formatuj `number_format(...)`.
- **Dane źródłowe**: `App\Repository\CardRepository` (tablica w pamięci, `all()` /
  `find(int)`). Pozostałe dane przykładowe są lokalnie w kontrolerach. Nie zakładaj
  istnienia bazy danych, mimo że Postgres jest w `docker-compose.yaml`.
- **404**: brak trasy obsługuje `public/index.php` (`dispatch()` zwraca `false` →
  `DefaultController::notFound()`). `CardController::show()` sam ustawia 404, gdy
  karta nie istnieje. Utrzymuj ten wzorzec, dopóki nie powstanie centralna obsługa
  wyjątków.

## Konwencje kodu

- **Nie dodawaj frameworków ani zależności bez zgody.**
- `declare(strict_types=1);` w każdym pliku PHP.
- Typy parametrów i wartości zwracanych zawsze jawnie.
- Wcięcie 4 spacje, otwierający `{` w nowej linii dla klas/metod.
- Identyfikatory, komentarze w kodzie, treści UI, dokumentacja i commity po
  angielsku; komunikacja z użytkownikiem po polsku.
- **Nie dodawaj komentarzy** do kodu, chyba że zostaniesz o to poproszony.
- Klasy domenowe/Repository jako `final`; brak dziedziczenia poza `AppController`.
- HTML: semantyczny, dostępny (`aria-*`, `role`), nazwy klas w BEM
  (`block__element--modifier`).

## Stan prac / znane placeholdery

Nie traktuj poniższych jako błędów do „naprawienia” bez polecenia:

- Formularze używają `method="post"`, ale **nie ma zarejestrowanych tras POST** —
  ich wysłanie daje 404. To celowe placeholdery.
- Brak bazy danych — `CardRepository` to dane w pamięci (świadome, tymczasowe).
- Routing obsługuje tylko parametry numeryczne (świadome, tymczasowe).
- Sesje/uwierzytelnianie są planowane; obecnie `/admin` i `/profile` są publiczne.
- Katalog `src/controllers` (mała litera) odbiega od PSR-4 (`src/Controller`) i działa
  dzięki osobnemu mapowaniu w `composer.json`. Zmiana nazwy jest planowana, ale
  niskopriorytetowa — nie wykonuj jej przy okazji innych zadań.

## Weryfikacja

W projekcie nie ma frameworka testowego ani skonfigurowanego lintera/statycznej
analizy. Przed uznaniem zadania za gotowe:

1. Sprawdź składnię zmienionych plików: `php -l <plik>` (lokalnie lub w kontenerze).
2. Jeśli zmieniałeś autoload/namespace: `composer dump-autoload`.
3. Przetestuj ręcznie dotknięte trasy pod http://localhost:8080.

Jeśli dodasz narzędzia (PHPUnit, PHPStan, linter) — zaktualizuj tę sekcję.

## Git

- Nie commituj, nie pushuj i nie twórz PR bez wyraźnego polecenia.
- Branch dla każdej funkcji; małe, logiczne commity.
- Commity po angielsku: `feat: short description` — nie dodawaj zakresu w nawiasach
  (`feat(theme):`).
- Sekrety wyłącznie w `.env` (poza repozytorium).

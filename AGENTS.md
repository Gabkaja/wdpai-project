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
| Baza danych | PostgreSQL (PDO, `pdo_pgsql`) |

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
src/Model/                    # modele danych (namespace App\Model)
src/Repository/               # źródła danych (namespace App\Repository)
src/Database.php              # fabryka połączenia PDO
database/                     # numerowane skrypty SQL (schemat, widoki, dane startowe)
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
- **Dane źródłowe**: karty, ranking i dane panelu admina pochodzą z PostgreSQL przez `App\Repository\*`. Dane `/profile` pozostają lokalnie w kontrolerze do czasu wdrożenia sesji.
- **404**: brak trasy obsługuje `public/index.php` (`dispatch()` zwraca `false` →
  `DefaultController::notFound()`). `CardController::show()` sam ustawia 404, gdy
  karta nie istnieje. Nieobsłużony wyjątek jest łapany w `public/index.php`,
  logowany do `system_logs` przez `LogRepository::add()` (z fallbackiem na
  `error_log()`), a użytkownik widzi stronę 500 przez
  `DefaultController::serverError()`.

## Baza danych (PostgreSQL)

- Połączenie tworzy wyłącznie `App\Database::connect()` (PDO, `pdo_pgsql`, `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`). Konfiguracja z `getenv()`: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, przekazywane do kontenera `php` w `docker-compose.yaml` z `.env`. Nie twórz `config.php` ani stałych z hasłami. Wewnątrz Dockera host bazy to `db`, port `5432`.
- SQL wolno pisać tylko w `src/Repository/`. Kontrolery i widoki nie znają PDO ani SQL.
- Każde zapytanie z wartością spoza kodu: `prepare()` + `execute()` z nazwanymi parametrami. Zakaz konkatenacji i interpolacji. `query()` tylko dla stałych zapytań bez parametrów.
- Modele w `src/Model/` (`App\Model`): `final`, bez PDO i SQL, typy jawne. Repozytorium mapuje wiersz na model w prywatnej metodzie `mapRowTo<Model>`. Nie twórz katalogów `src/models` ani `src/repositories`.
- Repozytoria dostają `PDO` przez konstruktor. Kontroler tworzy połączenie raz (`(new Database())->connect()`) i przekazuje je repozytoriom.
- Schemat i dane startowe: `database/NN-nazwa.sql`, montowane do `/docker-entrypoint-initdb.d`. Wykonują się tylko przy pustym wolumenie (`docker compose down -v`, potem `docker compose up --build -d`). Zmianę schematu po scommitowaniu zapisuj w nowym pliku z kolejnym numerem.
- Konwencje SQL: słowa kluczowe wielkimi literami, nazwy w `snake_case`, tabele w liczbie mnogiej, klucze główne `GENERATED ALWAYS AS IDENTITY`, `TIMESTAMPTZ` dla dat, każdy klucz obcy z jawnym `ON DELETE`, nazwane ograniczenia (`<tabela>_<kolumna>_check`).
- Transakcje (`beginTransaction()`, `commit()`, `rollBack()`) tylko w repozytoriach; przy wyjątku `rollBack()` i ponowny `throw`.
- Komunikaty błędów PDO nie trafiają do użytkownika ani do widoku; szczegóły idą do `error_log()` lub `system_logs`.
- Zakaz komentarzy dotyczy też docblocków i plików SQL.
- Nie dodawaj ORM ani bibliotek do obsługi bazy.

## Sesje i uwierzytelnianie

- Sesje natywne PHP, uruchamiane wyłącznie przez klasę `App\Session` (`src/Session.php`). Nie wołaj `session_start()` nigdzie indziej.
- Ustawienia cookie sesji: `HttpOnly`, `SameSite=Lax`, `Secure` gdy żądanie idzie przez HTTPS, `session.use_strict_mode=1`, `session.use_only_cookies=1`.
- W sesji przechowuj wyłącznie `user_id` (int) i token CSRF. Rola i dane użytkownika są za każdym razem odczytywane z bazy przez `UserRepository`. Nigdy nie ufaj roli ani id pochodzącym z POST, GET, cookie ani nagłówków.
- Po udanym logowaniu: `session_regenerate_id(true)`. Wylogowanie: wyczyszczenie `$_SESSION`, `session_destroy()` i wygaszenie cookie sesji.
- Hasła wyłącznie przez `password_hash(..., PASSWORD_DEFAULT)` i `password_verify(...)`. Błędne logowanie zwraca ten sam ogólny komunikat niezależnie od tego, czy e-mail istnieje.
- Każda trasa POST wymaga ważnego tokenu CSRF (pole ukryte `csrf_token`, porównanie przez `hash_equals`). Wylogowanie jest trasą POST.
- Ochrona tras: `AppController::requireLogin()` i `AppController::requireRole(string $role)`. Niezalogowany → przekierowanie na `/login`; zalogowany bez wymaganej roli → 403. Strony chronione wysyłają `Cache-Control: no-store`.
- Po udanym POST zawsze przekierowanie (Post/Redirect/Get). Przy błędach walidacji: ponowne wyrenderowanie formularza ze statusem 422.
- Trasy POST rejestruje się tak jak GET, w `public/index.php`.

## Integracja z Kick (OAuth)

- Kick OAuth 2.1 z obowiązkowym PKCE: `code_verifier` (losowe bajty, base64url) i `state` generowane przez `random_bytes`, przechowywane wyłącznie w sesji i zużywane jednorazowo; `state` porównuj przez `hash_equals`; `code_challenge_method=S256`.
- Konfiguracja z `getenv()`: `KICK_CLIENT_ID`, `KICK_CLIENT_SECRET`, `KICK_REDIRECT_URI`, przekazywane do kontenera `php` w `docker-compose.yaml` z `.env`. Nigdy nie loguj ani nie renderuj sekretu, kodu autoryzacyjnego ani tokenów.
- Wywołania HTTP do Kicka tylko przez `App\Service\KickClient` (`src/Service/KickClient.php`), na `curl` z PHP (bez nowych bibliotek), z timeoutem. Szczegóły endpointów i pól odpowiedzi weryfikuj w oficjalnej dokumentacji https://docs.kick.com, nie zgaduj.
- Tokeny dostępowe Kicka nie są zapisywane w bazie, o ile zadanie tego wyraźnie nie wymaga. Z odpowiedzi API zapisujemy tylko `kick_user_id` i `kick_username`.
- Tożsamość z Kicka jest wiązana z kontem wyłącznie przez `kick_user_id` (tabela `kick_accounts`), nigdy przez nazwę ani e-mail.
- Wejście przez Kick nie omija ról ani sesji: po udanym callbacku wołaj `Session::login()` tak jak przy logowaniu hasłem.

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

- Formularze avatar upload i promo code w `/profile` nie mają tras POST (celowy placeholder do czasu wdrożenia kolekcji i punktów).
- Routing obsługuje tylko parametry numeryczne (świadome, tymczasowe).
- Katalog `src/controllers` (mała litera) odbiega od PSR-4 (`src/Controller`) i działa
  dzięki osobnemu mapowaniu w `composer.json`. Zmiana nazwy jest planowana, ale
  niskopriorytetowa — nie wykonuj jej przy okazji innych zadań.

## Weryfikacja

W projekcie nie ma frameworka testowego ani skonfigurowanego lintera/statycznej
analizy. Przed uznaniem zadania za gotowe:

1. Sprawdź składnię zmienionych plików: `php -l <plik>` (lokalnie lub w kontenerze).
2. Jeśli zmieniałeś autoload/namespace: `composer dump-autoload`.
3. Przetestuj ręcznie dotknięte trasy pod http://localhost:8080.
4. Po zmianie `database/*.sql`: `docker compose down -v && docker compose up --build -d`, potem sprawdź strukturę przez `docker compose exec db psql`.

Jeśli dodasz narzędzia (PHPUnit, PHPStan, linter) — zaktualizuj tę sekcję.

## Git

- Nie commituj, nie pushuj i nie twórz PR bez wyraźnego polecenia.
- Branch dla każdej funkcji; małe, logiczne commity.
- Łącz branch z `main` merge commitem (`git merge --no-ff`); nie używaj
  fast-forward przy merge'u funkcji.
- Nie usuwaj branchy po merge'u — mają zostać w repozytorium.
- Commity po angielsku: `feat: short description` — nie dodawaj zakresu w nawiasach
  (`feat(theme):`).
- Sekrety wyłącznie w `.env` (poza repozytorium).

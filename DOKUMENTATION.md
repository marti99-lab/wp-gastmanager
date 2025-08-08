## Datei: DOKUMENTATION.md

# WP Gastmanager – Entwickler-Dokumentation

## Projektziel

Ein WordPress-Plugin zur Verwaltung von Aufgaben im Hotel- und Gastgewerbe.
Ziel ist es, Mitarbeitern Aufgaben zuzuweisen, Fristen zu setzen und eine einfache Übersicht zu schaffen.

---

## Aktueller Stand

### 1. Plugin-Basiskonfiguration
- `wp-gastmanager.php` erstellt
- Plugin-Header mit GPL2 und Textdomain
- Konstante `WPGM_PATH` definiert
- Klasse `WPGM_CPT_Aufgabe` eingebunden

### 2. Custom Post Type (CPT) „Aufgabe“
- Datei: `includes/class-cpt-aufgabe.php`
- Registriert CPT `aufgabe` mit Unterstützung für:
  - Titel
  - Beschreibung (Editor)
  - Autor
- Anzeige im WP-Menü mit Dashicon „clipboard“
- Gutenberg-kompatibel (`show_in_rest`)

### 3. Metaboxen für den Custom Post Type Aufgabe:
- Zimmernummer (Textfeld)
- Fälligkeitsdatum (Datumsauswahl)
- Verantwortliche Person (Benutzerauswahl)

### 4. Mehrsprachigkeit (Internationalisierung)

- Plugin ist vorbereitet für Internationalisierung (i18n)
- Textdomain: `wp-gastmanager`
- Alle statischen Texte mit `__()` bzw. `esc_html__()` übersetzbar gemacht
- Übersetzungsdateien befinden sich im Ordner `/languages/`

#### Unterstützte Sprachen:

| Sprache            | Locale   | Dateien                                 |
|--------------------|----------|------------------------------------------|
| Deutsch (Standard) | `de_DE`  | Kein `.po/.mo` nötig – Originaltexte in Deutsch |
| Englisch (UK)      | `en_GB`  | `wp-gastmanager-en_GB.po`, `wp-gastmanager-en_GB.mo` |
| Französisch        | `fr_FR`  | `wp-gastmanager-fr_FR.po`, `wp-gastmanager-fr_FR.mo` |

- `.po`-Dateien enthalten die editierbaren Übersetzungen (Textformat)
- `.mo`-Dateien sind die kompilierte Form, die WordPress tatsächlich verwendet
- `.pot`-Datei (`wp-gastmanager.pot`) dient als Vorlage für neue Übersetzungen

### 5. Shortcode für die Frontend-Ansicht der Aufgabenliste

- Ein Shortcode `[wpgm_aufgabenliste]`, der im Frontend Aufgaben als Liste anzeigt
#### Optionales Attribut `show`:
| Attribut         | Beschreibung                           | Hinweis                                         |
|------------------|----------------------------------------|-------------------------------------------------|
| *(kein Attribut)*| Automatisches Verhalten je nach Rolle  | Standardfall                                    |
| `show="own"`     | Zeigt nur eigene Aufgaben              | Für alle Benutzer verfügbar                     |
| `show="all"`     | Zeigt alle Aufgaben                    | Nur sichtbar für bestimmte Rollen (z. B. Admin) |

- `show="all"` wird nur berücksichtigt, wenn der Benutzer zu einer **autorisierten Rolle** gehört. Ansonsten wird automatisch auf `own` zurückgefallen.
- Rollenbasierte Sichtbarkeit:
  - Rollen wie `administrator`, `editor`, `manager` sehen alle Aufgaben
  - Andere Benutzer (z. B. `mitarbeiter`, `autor`) sehen nur eigene Aufgaben
- Übersetzbare Ausgabe der Aufgabenliste
- HTML-Ausgabe als einfache Liste mit Titel, Zimmernummer, Fälligkeitsdatum, Verantwortlichem

### 6. Benutzerrollen (Basis)

- Vorbereitete Rollen: `mitarbeiter`, `hausdame`, `technik`
- Registrierung erfolgt z. B. beim Plugin-Setup (zukünftig integrierbar)
- Zuweisung über WP-Backend (Benutzer > Bearbeiten)
- Keine speziellen Capabilities erforderlich (vereinfachte Rechtevergabe)
- Erweiterbar für spätere Differenzierung nach Zugriffsrechten

### 7. Benutzerrollen bei Aktivierung / Deaktivierung

- Bei Plugin-Aktivierung: Registrierung der Rollen mitarbeiter, hausdame, technik
  (via register_activation_hook() und Klasse WPGM_Role_Manager)
- Bei Plugin-Deaktivierung: Entfernen der Rollen
  (via register_deactivation_hook())

### 8. REST-API-Endpunkt für externe Nutzung

- Route: /wp-json/wp-gastmanager/v1/aufgaben (REST-API-Endpunkt der bereitgestellt wird.)
- Gibt Aufgaben als JSON zurück – mit rollenbasierter Sichtbarkeit
- Nur für eingeloggte Nutzer zugänglich (z. B. für externe Tools oder Apps)

### 9. CSV-Export der Aufgabenliste

- Exportiert Aufgaben (ID, Titel, Zimmer, Fälligkeit, Verantwortlicher) als CSV-Datei
- Backend-Seite nur für berechtigte Rollen (manage_options) sichtbar

### 10. Fortschrittsanzeige pro Aufgabe
- Neue Metabox „Fortschritt (%)“ zur Eingabe im Backend (0–100 %)
- Fortschrittswert wird im Frontend-Shortcode und REST-API ausgegeben (fortschritt)

### 11. Filter- und Suchfunktionen (Frontend & Backend)
- Backend: Filter nach Verantwortlichem, Zimmernummer und Fälligkeitsdatum.
- Frontend: Optionales Filterformular (show_filters="yes") im Shortcode.
- Rollenbasiert: Verantwortlich-Filter nur für berechtigte Rollen sichtbar.

## Geplant als nächstes
- Benutzerübersicht mit Anzahl offener Aufgaben
- UI-Verbesserung für Shortcode-Ansicht (Tabellen, Farben, Fortschrittsbalken)
- „Erledigt“-Status mit Checkbox im Frontend
- Import-Funktion für Aufgaben (CSV/Excel)

---

## Hinweise

- Alle Funktionen sind gekapselt in Klassen
- Textdomain `wp-gastmanager` wird konsistent verwendet
- Alle Texte werden korrekt internationalisiert (`__()` / `esc_html__()`)
- WordPress Coding Standards werden eingehalten
- Erweiterbarkeit und Wartbarkeit sind Ziel bei der Strukturierung
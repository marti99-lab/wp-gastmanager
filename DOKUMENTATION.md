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

---

## Geplant als nächstes

- Shortcode für Frontend-Ansicht der Aufgabenliste
- Nutzerrollen & Rechte (z. B. nur eigene Aufgaben sehen)
- REST-API-Endpunkt für externe Nutzung
- CSV-Export für Tagesplanung
- Fortschrittsanzeige pro Projekt / Zimmer

---

## Hinweise

- Alle Funktionen sind gekapselt in Klassen
- Textdomain `wp-gastmanager` wird konsistent verwendet
- Alle Texte werden korrekt internationalisiert (`__()` / `esc_html__()`)
- WordPress Coding Standards werden eingehalten
- Erweiterbarkeit und Wartbarkeit sind Ziel bei der Strukturierung
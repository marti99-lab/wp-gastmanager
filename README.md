## Datei: README.md

# wp-gastmanager
Ein WordPress-Plugin, das interne Aufgaben und Gästebezogene Infos für Hotelbetreiber/Personal verwaltet.

# Projekt-Struktur

```bash

wp-gastmanager/
├── wp-gastmanager.php             // Plugin-Starter, zentrale Initialisierung
├── README.md                      // Für die Nutzer (Installation, Anwendung)
├── DOKUMENTATION.md               // Für die Entwicklung & technische Dokumentation
├── LICENSE                        // GPL 2.0 Lizenz
├── includes/
│   ├── class-csv-export.php
│   ├── class-roles.php
│   ├── class-rest-api.php
│   └── class-cpt-aufgabe.php      // Custom Post Type "Aufgabe"
├── languages/
│   ├── wp-gastmanager.pot         // Text-Template
│   ├── wp-gastmanager-en_GB.po    // Englisch-Übersetzung
│   ├── wp-gastmanager-en_GB.mo    // Kompilierte Version (maschinell lesbar)
│   ├── wp-gastmanager-fr_FR.po
│   └── wp-gastmanager-fr_FR.mo
```



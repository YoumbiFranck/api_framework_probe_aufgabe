# Aufgabe

Ziel ist es, eine API zu entwickeln. Die API soll Schnittstellen zur Verfügung stellen, um Termine zu verwalten.
Folgende Anwendungsfälle sollen implementiert werden:

Benutzer registrieren/anmelden:
- Registrierung
  - Benutzername
  - E-Mail
  - Passwort
- Anmeldung/Abmeldung
- AuthMiddleware anpassen um zu prüfen, ob ein User eingeloggt ist

Terminverwaltung (für angemeldete Benutzer):
- Termin anlegen
  - Beschreibung zum Termin
  - Zeitraum
  - Anhang (File Upload)
  - Benutzer einladen
- Termin löschen
- Termin aktualisieren
- Termine anzeigen

Folgende Funktionen sind optional:
- Benutzerprofil anzeigen
- Benutzerprofil bearbeiten
- Benutzer Einladung zum Termin annehmen/ablehnen
- Verifizierung (E-Mail)

**Datenbank: An die API soll eine MariaDB Datenbank angebunden werden. Die Struktur der Datenbank sollst du dabei selber aufbauen.**

**Das Versionsmanagement soll über Git erfolgen**

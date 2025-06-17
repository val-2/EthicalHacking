# Ambiente di Sviluppo WordPress con Docker

Questa configurazione ti permette di avviare un ambiente di sviluppo WordPress completo utilizzando Docker e Docker Compose.

## Prerequisiti

-   [Docker](https://docs.docker.com/get-docker/) installato sul tuo sistema.
-   [Docker Compose](https://docs.docker.com/compose/install/) (solitamente incluso con Docker Desktop).

## Come iniziare

1.  **Modifica le password**: Apri il file `docker-compose.yml` e cambia le password di default (`your_strong_root_password` e `your_strong_password`) con delle password sicure.

2.  **Avvia i container**: Esegui questo comando dalla cartella principale del progetto. Docker scaricherà le immagini necessarie (se non le hai già) e avvierà i container in background.

    ```bash
    docker-compose up -d
    ```

3.  **Accedi a WordPress**: Una volta che i container sono attivi, puoi accedere al tuo sito WordPress aprendo il browser e visitando `http://localhost:8080`. Segui la procedura di installazione di WordPress.

4.  **Arrestare l'ambiente**: Per fermare i container, esegui:
    ```bash
    docker-compose down
    ```

## Come Replicare un Sito WordPress Esistente

Per replicare un sito che hai già creato, segui questi passaggi:

### 1. Copia i file del tuo sito

Copia il contenuto delle seguenti cartelle dal tuo sito esistente a questo progetto:

-   **Temi**: Copia la cartella del tuo tema da `wp-content/themes` del sito originale alla cartella `./wp-content/themes` di questo progetto.
-   **Plugin**: Copia tutti i tuoi plugin da `wp-content/plugins` del sito originale alla cartella `./wp-content/plugins` di questo progetto.
-   **Uploads**: Copia tutti i file media da `wp-content/uploads` del sito originale alla cartella `./wp-content/uploads` di questo progetto.

### 2. Esporta il Database del sito originale

Dal tuo sito WordPress attuale, devi esportare il database. Puoi farlo in diversi modi:

-   **Tramite un plugin**: Utilizza un plugin come "All-in-One WP Migration" o "Duplicator" per creare un backup completo (file e database).
-   **Tramite phpMyAdmin**: Se il tuo hosting fornisce phpMyAdmin, puoi usarlo per esportare il database in formato `.sql`.
-   **Tramite WP-CLI**: Se hai accesso alla riga di comando, puoi usare il comando `wp db export`.

Salva il file `.sql` del database in un posto sicuro.

### 3. Importa il Database nel nuovo ambiente Docker

Il modo più semplice per importare il database è sfruttare lo script di inizializzazione del container MariaDB.

1.  **Ferma l'ambiente Docker (se attivo)**:
    ```bash
    docker-compose down -v
    ```
    **ATTENZIONE**: Il flag `-v` rimuove anche il volume del database (`db_data`), quindi qualsiasi dato presente nel database Docker verrà cancellato. Usalo solo per la prima importazione.

2.  **Crea una cartella per lo script SQL**: Crea una nuova cartella chiamata `initdb` nella directory principale del progetto.

3.  **Sposta il tuo file SQL**: Rinomina il file del database che hai esportato in `dump.sql` e spostalo dentro la cartella `initdb`.

4.  **Modifica il `docker-compose.yml`**: Aggiungi un volume al servizio `db` per caricare il tuo file SQL all'avvio.

    Modifica la sezione `services.db.volumes` nel `docker-compose.yml` in questo modo:

    ```yaml
    # ... (dentro la definizione del servizio db)
    volumes:
      - db_data:/var/lib/mysql
      - ./initdb:/docker-entrypoint-initdb.d
    # ...
    ```

5.  **Avvia di nuovo l'ambiente**:
    ```bash
    docker-compose up -d
    ```

Al primo avvio, il container `db` eseguirà automaticamente il tuo file `dump.sql`, importando tutti i dati del tuo vecchio sito.

Una volta completata l'importazione, il tuo sito dovrebbe essere un clone perfetto di quello originale, accessibile su `http://localhost:8080`.

**IMPORTANTE**: Dopo la prima importazione, è consigliabile rimuovere il volume `./initdb:/docker-entrypoint-initdb.d` dal `docker-compose.yml` per evitare che lo script venga eseguito di nuovo inutilmente o causi problemi se ricrei i container. 

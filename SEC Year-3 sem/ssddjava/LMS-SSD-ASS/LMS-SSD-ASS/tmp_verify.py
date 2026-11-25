import urllib.request
from pathlib import Path

url = 'https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.33/'
try:
    with urllib.request.urlopen(url) as resp:
        html = resp.read().decode('utf-8', errors='replace')
        print('STATUS', resp.status)
        for line in html.splitlines():
            if '.jar' in line or 'href' in line:
                print(line)
except Exception as e:
    print('ERROR', type(e).__name__, e)

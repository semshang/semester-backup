import urllib.request
from pathlib import Path

versions = ['8.0.33', '8.0.32', '8.0.31', '8.0.30', '6.0.6', '5.1.49']
for v in versions:
    url = f'https://repo1.maven.org/maven2/mysql/mysql-connector-java/{v}/'
    try:
        with urllib.request.urlopen(url) as resp:
            html = resp.read().decode('utf-8', errors='replace')
            files = [line.strip() for line in html.splitlines() if 'href=' in line]
            jars = [line for line in files if '.jar' in line and 'pom' not in line]
            print(f'=== {v} STATUS {resp.status} ===')
            print('jar count:', len(jars))
            for line in jars:
                print(line)
    except Exception as e:
        print(f'=== {v} ERROR === {type(e).__name__}: {e}')

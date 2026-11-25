from pathlib import Path
p = Path(r'd:\Semester\SEC Year-3 sem\web-php\CSY2089 (Web Programming ) AS-II\as2\jobs\public\admin\auth.php')
data = p.read_bytes()
print(data[:20])
print('BOM:', data.startswith(b'\xef\xbb\xbf'))

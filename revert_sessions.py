import os
import re

def revert_sessions(directory):
    pattern = re.compile(r'require_once __DIR__ \. "/(\.\./)*config/session\.php";')
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.php'):
                path = os.path.join(root, file)
                try:
                    with open(path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    if pattern.search(content):
                        new_content = pattern.sub('session_start();', content)
                        with open(path, 'w', encoding='utf-8', newline='') as f:
                            f.write(new_content)
                        print(f"Updated: {path}")
                except Exception as e:
                    print(f"Error processing {path}: {e}")

if __name__ == "__main__":
    revert_sessions('vistas')
    revert_sessions('controladores')

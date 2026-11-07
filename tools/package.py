#!/usr/bin/env python3
"""Create an installable avela/ theme ZIP, excluding development and test files."""
from pathlib import Path
import sys,zipfile
root=Path(__file__).resolve().parents[1]
destination=Path(sys.argv[1]) if len(sys.argv)>1 else root.parent/'avela-1.1.0.zip'
excluded={'tests','tools','.git','.github','dist','previews','node_modules','__pycache__'}
with zipfile.ZipFile(destination,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as archive:
 for path in sorted(root.rglob('*')):
  rel=path.relative_to(root)
  if path.is_file() and not any(part in excluded for part in rel.parts) and path.name not in {'README.md','.gitignore'}:
   archive.write(path,Path('avela')/rel)
print(destination)

cd /storage/emulated/0/Download/pix/

echo "# pix" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/cybecoari/pix.git
git push -u origin main
echo off
cd wikis
echo Enter the wiki repository URL (ex. https://gitlab.com/project.wiki.git):
read repo
git clone $repo wikigit
mv wikigit/.git ./
rm -rf wikigit
git add .
git commit -m "$(git status | grep ': ')"
git push
rm -rf .git
echo on
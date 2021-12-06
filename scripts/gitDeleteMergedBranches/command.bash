#https://stackoverflow.com/questions/6127328/how-can-i-delete-all-git-branches-which-have-been-merged
# Add this to your Git configuration by running git config -e --global
#[alias]
#    cleanup = "!git branch --merged | grep  -v '\\*\\|master\\|develop' | xargs -n 1 -r git branch -d"


#delete branches merged into master
git branch --merged master | grep  -v '\\*\\|master\\|dev\\|staging' | xargs -n 1 -r git branch -d

#to delete remote too
git branch --remote --merged master | grep  -v '\\*\\|master\\|dev\\|staging' | sed 's/origin\///' | xargs -n 1 git push origin --delete

#faster way is
git push origin --delete branch1 branch2 branch3

#to delete branches merged in to current branch
# git branch --merged | grep  -v '\\*\\|master\\|develop' | xargs -n 1 -r git branch -d



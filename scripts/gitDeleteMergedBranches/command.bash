git branch --merged | grep  -v '\\*\\|master\\|develop' | xargs -n 1 -r git branch -d

#https://stackoverflow.com/questions/6127328/how-can-i-delete-all-git-branches-which-have-been-merged
# Add this to your Git configuration by running git config -e --global
#[alias]
#    cleanup = "!git branch --merged | grep  -v '\\*\\|master\\|develop' | xargs -n 1 -r git branch -d"


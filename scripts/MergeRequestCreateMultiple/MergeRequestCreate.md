How to install:
1. cp scripts/MergeRequestCreateMultiple/MergeRequestCreate.yml.dist scripts/MergeRequestCreateMultiple/MergeRequestCreate.yml
2. set parameters in .yml file
3. /usr/bin/php scripts/MergeRequestCreateMultiple/MergeRequestCreate.php

Extracts task name from current branch and release from first arg to make MR with this data in title.

You can run script from phpstorm and use $Prompt$ for first arg to write release number manually
Also you can use second argument to add message to title

<?php

$file = $argv[1];
$content = file_get_contents($file);

$content = preg_replace(
    '/\$this\-\>addSql\(\'ALTER TABLE ([^\s]+) ADD ([^\s]+) [^;]+;/us',
    'if (!$schema->getTable(\'$1\')->hasColumn(\'$2\')) {
            $0
        }',
    $content
);


$content = preg_replace(
    '/\$this\-\>addSql\(\'ALTER TABLE ([^\s]+) DROP COLUMN ([^\s]+)\'[^;]+;/us',
    'if ($schema->getTable(\'$1\')->hasColumn(\'$2\')) {
            $0
        }',
    $content
);


$content = preg_replace(
    "/\n        \/\/ this down\(\) migration is auto\-generated\, please modify it to your needs/us",
    '',
    $content
);


$content = preg_replace(
    "/\/\*\*\n \* Auto\-generated Migration\: Please modify to your needs\!\n \*\/\n/us",
    '',
    $content
);


$content = preg_replace(
    "/\n        \/\/ this up\(\) migration is auto\-generated\, please modify it to your needs/us",
    '',
    $content
);

file_put_contents($file, $content);

